<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/tasks')->assertRedirect('/login');
    }

    public function test_task_can_be_created_with_defaults(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tasks', ['title' => 'Fare X']);

        $response->assertRedirect(route('tasks.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Fare X',
            'priority' => 'media',
            'status' => 'da_fare',
        ]);
    }

    public function test_task_title_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/tasks', [])->assertSessionHasErrors('title');
    }

    public function test_subtasks_are_converted_to_array(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/tasks', [
            'title' => 'T',
            'subtasks' => "Prima\n\nSeconda",
        ]);

        $task = Task::first();
        $this->assertSame(
            [['title' => 'Prima', 'done' => false], ['title' => 'Seconda', 'done' => false]],
            $task->subtasks
        );
    }

    public function test_task_can_be_assigned_to_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Progetto']);

        $this->actingAs($user)->post('/tasks', [
            'title' => 'T nel progetto',
            'project_id' => $project->id,
        ])->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', ['title' => 'T nel progetto', 'project_id' => $project->id]);
    }

    public function test_task_can_be_marked_done(): void
    {
        $user = User::factory()->create();
        $task = Task::create(['user_id' => $user->id, 'title' => 'T']);

        $this->actingAs($user)->patch("/tasks/{$task->id}", ['title' => 'T', 'done' => 1]);

        $task->refresh();
        $this->assertSame('fatto', $task->status);
        $this->assertSame('Fatto', $task->kanban_col);
        $this->assertNotNull($task->completed_at);
    }

    public function test_task_can_be_unmarked_from_done(): void
    {
        $user = User::factory()->create();
        $task = Task::create([
            'user_id' => $user->id,
            'title' => 'T',
            'status' => 'fatto',
            'kanban_col' => 'Fatto',
            'completed_at' => now(),
        ]);

        $this->actingAs($user)->patch("/tasks/{$task->id}", ['title' => 'T', 'done' => 0]);

        $task->refresh();
        $this->assertSame('da_fare', $task->status);
        $this->assertSame('Da fare', $task->kanban_col);
        $this->assertNull($task->completed_at);
    }

    public function test_timer_can_be_updated(): void
    {
        $user = User::factory()->create();
        $task = Task::create(['user_id' => $user->id, 'title' => 'T']);

        $this->actingAs($user)
            ->post("/tasks/{$task->id}/timer", ['elapsed' => 125])
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'elapsed' => 125]);
    }

    public function test_user_cannot_update_other_users_task(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::create(['user_id' => $other->id, 'title' => 'Altrui']);

        $this->actingAs($user)
            ->patch("/tasks/{$task->id}", ['title' => 'Modificato'])
            ->assertForbidden();
    }

    public function test_user_cannot_delete_other_users_task(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::create(['user_id' => $other->id, 'title' => 'Altrui']);

        $this->actingAs($user)->delete("/tasks/{$task->id}")->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }
}