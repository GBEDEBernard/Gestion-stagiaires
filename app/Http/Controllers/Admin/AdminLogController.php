<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminLogController extends Controller
{
    public function index(Request $request): View
    {
        $logPath = storage_path('logs/laravel.log');
        $entries = [];

        if (is_file($logPath)) {
            $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $lines = array_slice(array_reverse($lines), 0, 250);

            foreach ($lines as $line) {
                if (str_contains($line, 'local.INFO') || str_contains($line, 'local.ERROR') || str_contains($line, 'local.WARNING') || str_contains($line, 'local.CRITICAL')) {
                    $entries[] = $line;
                }
            }
        }

        return view('admin.logs.index', [
            'entries' => array_reverse($entries),
            'logFileExists' => is_file($logPath),
            'logPath' => $logPath,
        ]);
    }
}
