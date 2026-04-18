<?php

namespace App\Http\Controllers\Tasks;

use App\Enums\FollowUpStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Task::class);

        $tenantId = request()->user()->current_tenant_id;

        $tasks = Task::query()
            ->where('tenant_id', $tenantId)
            ->with(['lead:id,first_name,last_name', 'assignedTo:id,name'])
            ->latest()
            ->get()
            ->map(fn (Task $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status?->value,
                'priority' => $task->priority?->value,
                'dueAt' => $task->due_at?->toDateTimeString(),
                'leadName' => $task->lead?->full_name,
                'assignedTo' => $task->assignedTo?->name,
            ]);

        $followUps = FollowUp::query()
            ->where('tenant_id', $tenantId)
            ->with(['lead:id,first_name,last_name', 'task:id,title', 'assignedTo:id,name'])
            ->orderBy('scheduled_for')
            ->get()
            ->map(fn (FollowUp $followUp) => [
                'id' => $followUp->id,
                'scheduledFor' => $followUp->scheduled_for?->toDateTimeString(),
                'status' => $followUp->status?->value,
                'leadName' => $followUp->lead?->full_name,
                'taskTitle' => $followUp->task?->title,
                'assignedTo' => $followUp->assignedTo?->name,
                'notes' => $followUp->notes,
            ]);

        $teamMembers = User::query()
            ->select('users.id', 'users.name')
            ->join('tenant_user', 'tenant_user.user_id', '=', 'users.id')
            ->where('tenant_user.tenant_id', $tenantId)
            ->orderBy('users.name')
            ->get();

        $leads = Lead::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name'])
            ->map(fn (Lead $lead) => [
                'id' => $lead->id,
                'name' => $lead->full_name,
            ]);

        return Inertia::render('tasks/Index', [
            'tasks' => $tasks,
            'followUps' => $followUps,
            'teamMembers' => $teamMembers,
            'leads' => $leads,
            'taskStatusOptions' => TaskStatus::values(),
            'taskPriorityOptions' => TaskPriority::values(),
            'followUpStatusOptions' => FollowUpStatus::values(),
            'stats' => [
                'totalTasks' => Task::query()->where('tenant_id', $tenantId)->count(),
                'pendingTasks' => Task::query()->where('tenant_id', $tenantId)->where('status', TaskStatus::Pending->value)->count(),
                'inProgressTasks' => Task::query()->where('tenant_id', $tenantId)->where('status', TaskStatus::InProgress->value)->count(),
                'pendingFollowUps' => FollowUp::query()->where('tenant_id', $tenantId)->where('status', FollowUpStatus::Pending->value)->count(),
            ],
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $this->authorize('create', Task::class);

        $tenantId = $request->user()->current_tenant_id;
        $data = $request->validated();

        Task::query()->create([
            'tenant_id' => $tenantId,
            'lead_id' => $data['lead_id'] ?? null,
            'assigned_to_user_id' => $data['assigned_to_user_id'] ?? null,
            'created_by_user_id' => $request->user()->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'priority' => $data['priority'],
            'due_at' => $data['due_at'] ?? null,
        ]);

        return back()->with('success', 'Task created successfully.');
    }
}
