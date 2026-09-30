<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/projects')->assertRedirect('/login');
        $this->post('/projects', ['name' => 'X'])->assertRedirect('/login');
    }

    public function test_authenticated_user_sees_only_own_projects(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Project::create(['user_id' => $other->id, 'name' => 'Progetto altrui']);
        Project::create(['user_id' => $user->id, 'name' => 'Mio progetto']);

        $response = $this->actingAs($user)->get('/projects')->assertOk();

        $response->assertSee('Mio progetto');
        $response->assertDontSee('Progetto altrui');
    }

    public function test_projects_are_ordered_by_workflow_status_then_due_date(): void
    {
        $user = User::factory()->create();

        Project::create(['user_id' => $user->id, 'name' => 'Completato', 'status' => 'completato', 'due_date' => '2026-01-01']);
        Project::create(['user_id' => $user->id, 'name' => 'In corso', 'status' => 'in_corso', 'due_date' => '2026-12-31']);
        Project::create(['user_id' => $user->id, 'name' => 'In arrivo', 'status' => 'in_arrivo', 'due_date' => '2026-06-01']);

        $response = $this->actingAs($user)->get('/projects')->assertOk();

        $response->assertViewHas('projects', function ($projects) {
            return $projects->pluck('name')->all() === ['In corso', 'In arrivo', 'Completato'];
        });
    }

    public function test_project_can_be_created_with_defaults(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/projects', ['name' => 'Nuovo progetto']);

        $response->assertRedirect(route('projects.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('projects', [
            'user_id' => $user->id,
            'name' => 'Nuovo progetto',
            'status' => 'in_arrivo',
            'color' => '#3b82f6',
        ]);
    }

    public function test_project_name_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/projects', [])->assertSessionHasErrors('name');
    }

    public function test_project_status_must_be_valid(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/projects', ['name' => 'X', 'status' => 'invaso'])
            ->assertSessionHasErrors('status');
    }

    public function test_owner_can_update_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Originale']);

        $this->actingAs($user)
            ->patch("/projects/{$project->id}", ['name' => 'Aggiornato', 'status' => 'in_corso'])
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Aggiornato',
            'status' => 'in_corso',
        ]);
    }

    public function test_user_cannot_update_other_users_project(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $project = Project::create(['user_id' => $other->id, 'name' => 'Altrui']);

        $this->actingAs($user)
            ->patch("/projects/{$project->id}", ['name' => 'Modificato'])
            ->assertForbidden();

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Altrui']);
    }

    public function test_owner_can_delete_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Da eliminare']);

        $this->actingAs($user)
            ->delete("/projects/{$project->id}")
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_user_cannot_delete_other_users_project(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $project = Project::create(['user_id' => $other->id, 'name' => 'Altrui']);

        $this->actingAs($user)->delete("/projects/{$project->id}")->assertForbidden();

        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }
}