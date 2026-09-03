<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Domaine;
use App\Models\Employe;
use App\Models\Etudiant;
use App\Models\Personnel;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UrgentNotificationService
{
    /**
     * Résout la liste des utilisateurs destinataires selon les critères de ciblage.
     *
     * @param string $targetType
     * @param string|null $targetValue
     * @param array $individualIds
     * @return Collection<User>
     */
    public function resolveRecipients(string $targetType, ?string $targetValue = null, array $individualIds = []): Collection
    {
        return match ($targetType) {
            'all' => $this->getAllActiveUsers(),
            'employes' => $this->getAllEmployes(),
            'poste' => $this->getEmployesByPoste($targetValue),
            'stagiaires' => $this->getAllStagiaires(),
            'typestage' => $this->getStagiairesByTypeStage($targetValue),
            'domaine' => $this->getUsersByDomaine($targetValue),
            'individual' => $this->getIndividualUsers($individualIds),
            default => collect(),
        };
    }

    /**
     * Compte le nombre de destinataires pour un critère donné.
     */
    public function countRecipients(string $targetType, ?string $targetValue = null, array $individualIds = []): int
    {
        return $this->resolveRecipients($targetType, $targetValue, $individualIds)->count();
    }

    /**
     * 1. Tous les utilisateurs actifs
     */
    public function getAllActiveUsers(): Collection
    {
        return User::where('status', 'actif')->get();
    }

    /**
     * 2. Tous les employés (rôles employe/superviseur/admin ou personnel relié à un Employe)
     */
    public function getAllEmployes(): Collection
    {
        return User::where('status', 'actif')
            ->where(function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['employe', 'superviseur', 'admin', 'fonctionnaire']);
                })->orWhereHas('personnel', function ($q) {
                    $q->where('personnable_type', Employe::class);
                });
            })
            ->get();
    }

    /**
     * 3. Employés spécifiques par poste (ex: DT, DTA, DG, etc.)
     */
    public function getEmployesByPoste(?string $poste): Collection
    {
        if (empty($poste)) {
            return collect();
        }

        return User::where('status', 'actif')
            ->whereHas('personnel', function ($query) use ($poste) {
                $query->where('personnable_type', Employe::class)
                    ->whereHasMorph('personnable', [Employe::class], function ($q) use ($poste) {
                        $q->where('poste', $poste);
                    });
            })
            ->get();
    }

    /**
     * 4. Tous les stagiaires (rôle etudiant ou personnel relié à Etudiant)
     */
    public function getAllStagiaires(): Collection
    {
        return User::where('status', 'actif')
            ->where(function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'etudiant');
                })->orWhereHas('personnel', function ($q) {
                    $q->where('personnable_type', Etudiant::class);
                });
            })
            ->get();
    }

    /**
     * 5. Stagiaires par type de stage (Académique, Professionnel, etc.)
     */
    public function getStagiairesByTypeStage(?string $typeStageId): Collection
    {
        if (empty($typeStageId)) {
            return collect();
        }

        return User::where('status', 'actif')
            ->whereHas('personnel', function ($query) use ($typeStageId) {
                $query->where('personnable_type', Etudiant::class)
                    ->whereHasMorph('personnable', [Etudiant::class], function ($q) use ($typeStageId) {
                        $q->whereHas('stages', function ($sq) use ($typeStageId) {
                            $sq->where('typestage_id', $typeStageId);
                        });
                    });
            })
            ->get();
    }

    /**
     * 6. Par Domaine d'activité (Direction / Service)
     */
    public function getUsersByDomaine(?string $domaineId): Collection
    {
        if (empty($domaineId)) {
            return collect();
        }

        return User::where('status', 'actif')
            ->where(function ($query) use ($domaineId) {
                $query->where('domaine_id', $domaineId)
                    ->orWhereHas('personnel', function ($pq) use ($domaineId) {
                        $pq->where(function ($morphQ) use ($domaineId) {
                            $morphQ->where(function ($eq) use ($domaineId) {
                                $eq->where('personnable_type', Employe::class)
                                    ->whereHasMorph('personnable', [Employe::class], function ($q) use ($domaineId) {
                                        $q->where('domaine_id', $domaineId);
                                    });
                            })->orWhere(function ($stq) use ($domaineId) {
                                $stq->where('personnable_type', Etudiant::class)
                                    ->whereHasMorph('personnable', [Etudiant::class], function ($q) use ($domaineId) {
                                        $q->whereHas('stages', function ($sq) use ($domaineId) {
                                            $sq->where('domaine_id', $domaineId);
                                        });
                                    });
                            });
                        });
                    });
            })
            ->get();
    }

    /**
     * 7. Sélection personnalisée / individuelle
     */
    public function getIndividualUsers(array $individualIds): Collection
    {
        if (empty($individualIds)) {
            return collect();
        }

        return User::where('status', 'actif')
            ->whereIn('id', $individualIds)
            ->get();
    }

    /**
     * Diffuse une notification urgente en masse vers la cible choisie.
     */
    public function broadcast(array $data, User $sender): array
    {
        $targetType = $data['target_type'];
        $targetValue = $data['target_value'] ?? null;
        $individualIds = $data['individual_ids'] ?? [];

        $recipients = $this->resolveRecipients($targetType, $targetValue, $individualIds);

        if ($recipients->isEmpty()) {
            return [
                'success' => false,
                'count' => 0,
                'message' => 'Aucun destinataire actif trouvé pour les critères sélectionnés.',
            ];
        }

        $batchId = (string) Str::uuid();
        $title = trim($data['title']);
        $message = trim($data['message']);
        $url = !empty($data['url']) ? trim($data['url']) : null;
        $attachment = $data['attachment'] ?? null;
        $now = now();

        $notificationsToInsert = [];
        foreach ($recipients as $recipient) {
            $notificationsToInsert[] = [
                'unique_id' => 'urgent_' . $batchId . '_' . $recipient->id,
                'type' => 'alerte_urgente',
                'is_urgent' => true,
                'target_type' => $targetType,
                'target_value' => is_array($targetValue) ? json_encode($targetValue) : (string) $targetValue,
                'batch_id' => $batchId,
                'sender_id' => $sender->id,
                'title' => $title,
                'message' => $message,
                'icon' => 'exclamation-triangle',
                'color' => 'red',
                'url' => $url,
                'attachment_path' => $attachment['path'] ?? null,
                'attachment_name' => $attachment['name'] ?? null,
                'attachment_mime' => $attachment['mime'] ?? null,
                'attachment_size' => $attachment['size'] ?? null,
                'reference_id' => null,
                'reference_type' => null,
                'user_id' => $recipient->id,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insertion par blocs de 100
        foreach (array_chunk($notificationsToInsert, 100) as $chunk) {
            AppNotification::insert($chunk);
        }

        return [
            'success' => true,
            'batch_id' => $batchId,
            'count' => count($notificationsToInsert),
            'message' => 'Alerte urgente diffusée avec succès à ' . count($notificationsToInsert) . ' destinataire(s).',
        ];
    }

    /**
     * Récupère TOUTES les notifications urgentes actives (non acquittées) pour l'utilisateur.
     *
     * @param User|null $user
     * @return Collection<AppNotification>
     */
    public function getActiveUrgentNotificationsForUser(?User $user): Collection
    {
        if (!$user) {
            return collect();
        }

        return AppNotification::urgentUnreadForUser($user->id)
            ->with('sender')
            ->latest('id')
            ->get();
    }

    /**
     * Récupère la notification urgente active la plus récente pour l'utilisateur (rétro-compatibilité).
     */
    public function getActiveUrgentNotificationForUser(?User $user): ?AppNotification
    {
        return $this->getActiveUrgentNotificationsForUser($user)->first();
    }

    /**
     * Acquitte (marque comme lue) une notification urgente pour l'utilisateur connecté.
     */
    public function acknowledge(int|string $notificationId, User $user): bool
    {
        $notification = AppNotification::where('id', $notificationId)
            ->where('user_id', $user->id)
            ->where('is_urgent', true)
            ->first();

        if ($notification) {
            $notification->update(['read_at' => now()]);
            return true;
        }

        return false;
    }

    /**
     * Acquitte TOUTES les notifications urgentes en attente pour l'utilisateur connecté.
     */
    public function acknowledgeAll(User $user): int
    {
        return AppNotification::urgentUnreadForUser($user->id)
            ->update(['read_at' => now()]);
    }

    /**
     * Récupère l'historique des alertes urgentes groupées par batch.
     */
    public function getRecentBatches(int $limit = 15): Collection
    {
        $batches = AppNotification::urgent()
            ->whereNotNull('batch_id')
            ->select(
                'batch_id',
                'title',
                'message',
                'target_type',
                'target_value',
                'url',
                'sender_id',
                'attachment_path',
                'attachment_name',
                DB::raw('MIN(created_at) as created_at'),
                DB::raw('COUNT(*) as total_recipients'),
                DB::raw('COUNT(read_at) as read_count')
            )
            ->groupBy('batch_id', 'title', 'message', 'target_type', 'target_value', 'url', 'sender_id', 'attachment_path', 'attachment_name')
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();

        // Charger les relations sender
        $senderIds = $batches->pluck('sender_id')->filter()->unique();
        $senders = User::whereIn('id', $senderIds)->get()->keyBy('id');

        return $batches->map(function ($batch) use ($senders) {
            $batch->sender = $senders->get($batch->sender_id);
            $batch->read_percentage = $batch->total_recipients > 0
                ? round(($batch->read_count / $batch->total_recipients) * 100)
                : 0;
            $batch->target_label = $this->formatTargetLabel($batch->target_type, $batch->target_value);
            $batch->attachment_url = $batch->attachment_path
                ? Storage::disk('public')->url($batch->attachment_path)
                : null;
            return $batch;
        });
    }

    /**
     * Récupère le détail des destinataires pour un lot d'alertes.
     */
    public function getBatchDetails(string $batchId): ?array
    {
        $notifications = AppNotification::urgent()
            ->where('batch_id', $batchId)
            ->with(['user.personnel', 'sender'])
            ->orderBy('read_at', 'asc')
            ->get();

        if ($notifications->isEmpty()) {
            return null;
        }

        $first = $notifications->first();
        $total = $notifications->count();
        $readCount = $notifications->whereNotNull('read_at')->count();

        return [
            'batch_id' => $batchId,
            'title' => $first->title,
            'message' => $first->message,
            'url' => $first->url,
            'attachment_path' => $first->attachment_path,
            'attachment_name' => $first->attachment_name,
            'attachment_url' => $first->attachment_url,
            'target_type' => $first->target_type,
            'target_value' => $first->target_value,
            'target_label' => $this->formatTargetLabel($first->target_type, $first->target_value),
            'sender' => $first->sender,
            'created_at' => $first->created_at,
            'total_recipients' => $total,
            'read_count' => $readCount,
            'unread_count' => $total - $readCount,
            'read_percentage' => $total > 0 ? round(($readCount / $total) * 100) : 0,
            'recipients' => $notifications->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'user_id' => $notif->user_id,
                    'name' => $notif->user?->name ?? 'Utilisateur inconnu',
                    'email' => $notif->user?->email ?? '-',
                    'role' => $notif->user?->roles->pluck('name')->implode(', ') ?: 'Utilisateur',
                    'read_at' => $notif->read_at ? $notif->read_at->format('d/m/Y H:i:s') : null,
                    'is_read' => $notif->read_at !== null,
                ];
            }),
        ];
    }

    /**
     * Données d'options pour alimenter le formulaire d'envoi.
     */
    public function getTargetOptions(): array
    {
        // 1. Postes d'employés existants
        $postes = Employe::whereNotNull('poste')
            ->where('poste', '!=', '')
            ->distinct()
            ->orderBy('poste')
            ->pluck('poste')
            ->values()
            ->toArray();

        // S'assurer que les postes clés sont présents même sans données
        $defaultPostes = [
            'Directeur Général',
            'Directeur Technique',
            'Directeur Technique Adjoint',
            'Directeur des Opérations',
            'Administrateur Système',
            'Responsable Administratif',
            'Chef de Projet',
            'Comptable',
        ];
        $postes = array_values(array_unique(array_merge($postes, $defaultPostes)));
        sort($postes);

        // 2. Types de stage
        $typeStages = TypeStage::orderBy('libelle')->get(['id', 'libelle', 'code']);

        // 3. Domaines / Directions
        $domaines = Domaine::orderBy('nom')->get(['id', 'nom']);

        // 4. Utilisateurs actifs pour sélection individuelle
        $users = User::where('status', 'actif')
            ->with(['personnel', 'roles'])
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->pluck('name')->implode(', ') ?: 'Utilisateur',
                ];
            });

        return [
            'postes' => $postes,
            'typeStages' => $typeStages,
            'domaines' => $domaines,
            'users' => $users,
        ];
    }

    /**
     * Formate le libellé lisible de la cible.
     */
    private function formatTargetLabel(string $type, ?string $value): string
    {
        return match ($type) {
            'all' => 'Tous les utilisateurs',
            'employes' => 'Tous les employés',
            'poste' => 'Poste : ' . ($value ?: 'Non spécifié'),
            'stagiaires' => 'Tous les stagiaires',
            'typestage' => 'Type de stage : ' . (TypeStage::find($value)?->libelle ?? $value),
            'domaine' => 'Domaine : ' . (Domaine::find($value)?->nom ?? $value),
            'individual' => 'Sélection individuelle',
            default => ucfirst($type),
        };
    }
}
