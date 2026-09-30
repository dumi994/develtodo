<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_search(): void
    {
        $this->get('/search?q=abc')->assertRedirect('/login');
    }

    public function test_search_requires_minimum_two_characters(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/search?q=a')->assertJson([]);
    }

    public function test_search_finds_matching_projects(): void
    {
        $user = User::factory()->create();
        Project::create(['user_id' => $user->id, 'name' => 'Sito vetrina', 'client' => 'Acme']);

        $response = $this->actingAs($user)->get('/search?q=vetrina');

        $response->assertJsonFragment(['type' => 'project', 'title' => 'Sito vetrina']);
    }

    public function test_search_finds_matching_tasks(): void
    {
        $user = User::factory()->create();
        Task::create(['user_id' => $user->id, 'title' => 'Aggiornare il layout']);

        $response = $this->actingAs($user)->get('/search?q=layout');

        $response->assertJsonFragment(['type' => 'task', 'title' => 'Aggiornare il layout']);
    }

    public function test_search_finds_matching_tickets(): void
    {
        $user = User::factory()->create();
        Ticket::create([
            'user_id' => $user->id,
            'client' => 'Cliente X',
            'subject' => 'Email non arrivano',
            'status' => 'aperto',
            'last_update' => now(),
        ]);

        $response = $this->actingAs($user)->get('/search?q=email');

        $response->assertJsonFragment(['type' => 'ticket', 'title' => 'Email non arrivano']);
    }

    public function test_search_finds_matching_quotes(): void
    {
        $user = User::factory()->create();
        Quote::create(['user_id' => $user->id, 'client' => 'Ristorante Bella', 'amount' => 500]);

        $response = $this->actingAs($user)->get('/search?q=bella');

        $response->assertJsonFragment(['type' => 'quote', 'title' => 'Ristorante Bella']);
    }

    public function test_search_finds_matching_invoices(): void
    {
        $user = User::factory()->create();
        Invoice::create([
            'user_id' => $user->id,
            'client' => 'Studio Rossi',
            'amount' => 900,
            'number' => 'INV-20260930-001',
            'status' => 'da_fare',
        ]);

        $response = $this->actingAs($user)->get('/search?q=rossi');

        $response->assertJsonFragment(['type' => 'invoice', 'title' => 'INV-20260930-001']);
    }

    public function test_search_does_not_leak_other_users_data(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Project::create(['user_id' => $other->id, 'name' => 'Progetto segreto', 'client' => 'mega-client-segreto']);
        Task::create(['user_id' => $other->id, 'title' => 'Task segreto']);
        Ticket::create([
            'user_id' => $other->id,
            'client' => 'cliente-segreto',
            'subject' => 'Ticket segreto',
            'status' => 'aperto',
            'last_update' => now(),
        ]);
        Quote::create(['user_id' => $other->id, 'client' => 'preventivo-segreto', 'amount' => 1]);
        Invoice::create([
            'user_id' => $other->id,
            'client' => 'fattura-segreta',
            'amount' => 1,
            'number' => 'INV-segreta-001',
            'status' => 'da_fare',
        ]);

        $response = $this->actingAs($user)->get('/search?q=segreto');

        $json = $response->json();
        $this->assertIsArray($json);
        $this->assertCount(0, $json);
    }
}