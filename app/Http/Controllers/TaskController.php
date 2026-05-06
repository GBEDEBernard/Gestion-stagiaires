<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Stage;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['stage.etudiant', 'assignedBy'])
            ->visibleTo(auth()->user())
            ->latest()
            ->paginate(10);

        return view('admin.tasks.index', compact('tasks'));
    }

    public function create()
    {
        $stages = Stage::with(['etudiant', 'site'])
            ->orderByDesc('date_debut')
            ->get();

        return view('admin.tasks.create', compact('stages'));
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'priority' => 'required|in:low,normal,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,blocked',
            'due_date' => 'nullable|date',
        ]);

        $stage = Stage::with('etudiant')->findOrFail($payload['stage_id']);

        $task = Task::create([
            'stage_id' => $stage->id,
            'etudiant_id' => $stage->etudiant_id,
            'assigned_by' => auth()->id(),
            'title' => $payload['title'],
            'description' => $payload['description'] ?? null,
            'priority' => $payload['priority'],
            'status' => $payload['status'],
            'due_date' => $payload['due_date'] ?? null,
            'started_at' => $payload['status'] === 'in_progress' ? now() : null,
            'completed_at' => $payload['status'] === 'completed' ? now() : null,
            'last_progress_percent' => $payload['status'] === 'completed' ? 100 : 0,
        ]);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Creation tache',
            'description' => "Tache {$task->title} creee pour le stage {$stage->theme}",
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tache creee avec succes.');
    }

    public function edit(Task $task)
    {
        $stages = Stage::with(['etudiant', 'site'])
            ->orderByDesc('date_debut')
            ->get();

        return view('admin.tasks.edit', compact('task', 'stages'));
    }

    public function update(Request $request, Task $task)
    {
        $payload = $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'priority' => 'required|in:low,normal,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,blocked',
            'due_date' => 'nullable|date',
        ]);

        $stage = Stage::findOrFail($payload['stage_id']);

        $task->update([
            'stage_id' => $stage->id,
            'etudiant_id' => $stage->etudiant_id,
            'title' => $payload['title'],
            'description' => $payload['description'] ?? null,
            'priority' => $payload['priority'],
            'status' => $payload['status'],
            'due_date' => $payload['due_date'] ?? null,
            'started_at' => $payload['status'] === 'in_progress' ? ($task->started_at ?: now()) : $task->started_at,
            'completed_at' => $payload['status'] === 'completed' ? ($task->completed_at ?: now()) : null,
            'last_progress_percent' => $payload['status'] === 'completed' ? 100 : ($payload['status'] === 'pending' ? 0 : $task->last_progress_percent),
        ]);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Mise a jour tache',
            'description' => "Tache {$task->title} modifiee",
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tache mise a jour.');
    }

    public function destroy(Task $task)
    {
        $title = $task->title;
        $task->delete();

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Suppression tache',
            'description' => "Tache {$title} supprimee",
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tache supprimee.');
    }
}
