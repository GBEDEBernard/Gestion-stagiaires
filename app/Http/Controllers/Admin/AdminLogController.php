<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLogController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        $minutes = max(1, (int) $request->get('minutes', 60));
        $level = strtolower((string) $request->get('level', 'all'));
        $search = trim((string) $request->get('search', ''));

        $logPath = storage_path('logs/laravel-' . now()->format('Y-m-d') . '.log');
        if (!is_file($logPath)) {
            $logPath = storage_path('logs/laravel.log');
        }

        $logFileExists = is_file($logPath);
        $entries = [];

        if ($logFileExists) {
            $content = file_get_contents($logPath);
            preg_match_all(
                '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*?)(?=\n\[\d{4}-\d{2}-\d{2}|\z)/s',
                $content,
                $matches,
                PREG_SET_ORDER
            );

            $limit = now()->subMinutes($minutes);

            foreach (array_reverse($matches) as $m) {
                $date = Carbon::parse($m[1]);
                if ($date->lt($limit)) {
                    continue;
                }

                $lvl = strtolower($m[3]);
                if ($level !== 'all' && $lvl !== $level) {
                    continue;
                }

                $raw = trim($m[4]);
                $message = $raw;
                $context = null;

                if (preg_match('/^(.*?)(\{.*\})\s*$/s', $raw, $cm)) {
                    $decoded = json_decode($cm[2], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $message = trim($cm[1]);
                        $context = $decoded;
                    }
                }

                if ($search !== '' && stripos($raw, $search) === false) {
                    continue;
                }

                $entries[] = [
                    'date' => $date->format('Y-m-d H:i:s'),
                    'level' => $lvl,
                    'message' => $message,
                    'context' => $context,
                ];
            }
        }

        $stats = [
            'error' => collect($entries)->where('level', 'error')->count(),
            'warning' => collect($entries)->where('level', 'warning')->count(),
            'info' => collect($entries)->where('level', 'info')->count(),
            'total' => count($entries),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'entries' => $entries,
                'stats' => $stats,
            ]);
        }

        return view('admin.logs.index', [
            'entries' => $entries,
            'stats' => $stats,
            'logFileExists' => $logFileExists,
            'logPath' => $logPath,
        ]);
    }
}
