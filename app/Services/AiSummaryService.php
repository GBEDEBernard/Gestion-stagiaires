<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\ReportSummary;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiSummaryService
{
    protected string $provider;

    public function __construct()
    {
        $this->provider = config('services.ai.provider', 'openai');
    }

    public function generateForUser(
        User $user,
        string $periodType,
        \Carbon\Carbon $periodStart,
        \Carbon\Carbon $periodEnd,
        bool $force = false
    ): ReportSummary {
        $existing = ReportSummary::where('user_id', $user->id)
            ->where('period_type', $periodType)
            ->where('period_start', $periodStart->toDateString())
            ->where('period_end', $periodEnd->toDateString())
            ->first();

        if ($existing && !$force) {
            return $existing;
        }

        $data = $this->collectData($user, $periodStart, $periodEnd);
        $summary = $this->summarize($data, $user, $periodType);

        return ReportSummary::updateOrCreate(
            [
                'user_id' => $user->id,
                'period_type' => $periodType,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
            ],
            [
                'summary' => $summary,
                'raw_data' => $data,
                'model_used' => $this->getModelName(),
                'generated_at' => now(),
            ]
        );
    }

    public function collectData(User $user, \Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        $reports = DailyReport::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('etudiant.user', fn($sq) => $sq->where('users.id', $user->id));
            })
            ->whereBetween('report_date', [$start, $end])
            ->where('status', 'submitted')
            ->with(['reviews.reviewer', 'task'])
            ->orderBy('report_date')
            ->get();

        $data = [];

        foreach ($reports as $report) {
            $entry = [
                'date' => $report->report_date->toDateString(),
                'title' => $report->title,
                'introduction' => $report->introduction,
                'summary' => $report->summary,
                'blockers' => $report->blockers,
                'next_steps' => $report->next_steps,
                'hours_declared' => $report->hours_declared,
                'task_title' => $report->task?->title,
                'reviews' => $report->reviews->map(fn($r) => [
                    'reviewer' => $r->reviewer?->name,
                    'comment' => $r->comment,
                    'action' => $r->action,
                ])->toArray(),
            ];

            if ($report->task) {
                $messages = $report->task->messages()
                    ->where('type', 'message')
                    ->whereBetween('created_at', [$start, $end])
                    ->with('user')
                    ->orderBy('created_at')
                    ->get()
                    ->map(fn($m) => [
                        'user' => $m->user?->name,
                        'body' => $m->body,
                        'created_at' => $m->created_at->toDateTimeString(),
                    ])
                    ->toArray();

                if ($messages) {
                    $entry['messages'] = $messages;
                }
            }

            $data[] = $entry;
        }

        return $data;
    }

    protected function summarize(array $data, User $user, string $periodType): string
    {
        if (empty($data)) {
            $labels = [
                'weekly' => 'cette semaine',
                'monthly' => 'ce mois-ci',
                'yearly' => 'cette année',
            ];
            return "Aucun rapport soumis {$labels[$periodType]}.";
        }

        $labels = [
            'weekly' => 'hebdomadaire',
            'monthly' => 'mensuel',
            'yearly' => 'annuel',
        ];

        $totalHours = collect($data)->sum('hours_declared');
        $reportCount = count($data);
        $prompt = $this->buildPrompt($data, $user->name, $labels[$periodType], $totalHours, $reportCount);

        return $this->callAI($prompt);
    }

    protected function buildPrompt(array $data, string $userName, string $periodLabel, float $totalHours, int $reportCount): string
    {
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return <<<PROMPT
Tu es un assistant spécialisé dans le résumé de rapports d'activité professionnelle.

Voici les rapports journaliers de {$userName} pour la période {$periodLabel}.
Nombre de rapports : {$reportCount}
Total d'heures déclarées : {$totalHours}h

Pour chaque rapport, tu as : la date, le titre, l'introduction, le résumé, les difficultés (blockers), les prochaines étapes, le nombre d'heures, la tâche associée, les commentaires des superviseurs et les messages échangés.

Réalise un résumé clair et structuré en français qui met en évidence :
1. **Vue d'ensemble** — le travail global accompli
2. **Réalisations principales** — les tâches et objectifs complétés ou avancés
3. **Difficultés rencontrées** — les blocages et obstacles
4. **Prochaines étapes** — ce qui est prévu pour la suite
5. **Évolution** — la progression constatée sur la période

Sois précis et factuel. Utilise un langage professionnel.
Ne commence pas par "Voici le résumé". Va droit au but.

Données des rapports :
{$jsonData}
PROMPT;
    }

    protected function callAI(string $prompt): string
    {
        return $this->provider === 'ollama'
            ? $this->callOllama($prompt)
            : $this->callOpenAI($prompt);
    }

    protected function callOllama(string $prompt): string
    {
        $baseUrl = config('services.ai.ollama.base_url', 'http://localhost:11434');
        $model = config('services.ai.ollama.model', 'llama3.2');

        try {
            $response = Http::timeout(180)->post("{$baseUrl}/api/chat", [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'stream' => false,
                'options' => [
                    'num_predict' => 2000,
                    'temperature' => 0.5,
                ],
            ]);

            if ($response->failed()) {
                $error = $response->json('error') ?? $response->body();
                return "Erreur Ollama : {$error}";
            }

            return $response->json('message.content') ?? 'Résumé non disponible.';
        } catch (\Exception $e) {
            return "Erreur de connexion à Ollama : {$e->getMessage()}. Vérifie qu'Ollama est lancé (ollama serve) et que le modèle {$model} est installé (ollama pull {$model}).";
        }
    }

    protected function callOpenAI(string $prompt): string
    {
        $apiKey = config('services.openai.api_key');

        if (!$apiKey) {
            return "Clé API OpenAI non configurée. Ajoute OPENAI_API_KEY dans .env ou utilise AI_PROVIDER=ollama.";
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 2000,
            'temperature' => 0.5,
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message') ?? $response->body();
            return "Erreur API OpenAI : {$error}";
        }

        return $response->json('choices.0.message.content') ?? 'Résumé non disponible.';
    }

    protected function getModelName(): string
    {
        return $this->provider === 'ollama'
            ? config('services.ai.ollama.model', 'llama3.2')
            : 'gpt-4o-mini';
    }

    public function generateForAllUsers(string $periodType, \Carbon\Carbon $start, \Carbon\Carbon $end, bool $force = false): array
    {
        $reportUserIds = DailyReport::whereBetween('report_date', [$start, $end])
            ->where('status', 'submitted')
            ->whereNotNull('user_id')
            ->pluck('user_id');

        $etudiantIds = DailyReport::whereBetween('report_date', [$start, $end])
            ->where('status', 'submitted')
            ->whereNotNull('etudiant_id')
            ->pluck('etudiant_id');

        $studentUserIds = User::whereHas('etudiant', fn($q) => $q->whereIn('etudiants.id', $etudiantIds))
            ->pluck('users.id');

        $allIds = $reportUserIds->merge($studentUserIds)->unique()->filter();
        $users = User::whereIn('id', $allIds)->get();

        $results = [];

        foreach ($users as $user) {
            try {
                $summary = $this->generateForUser($user, $periodType, $start, $end, $force);
                $results[] = [
                    'user' => $user->name,
                    'status' => 'success',
                    'summary_id' => $summary->id,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'user' => $user->name,
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
