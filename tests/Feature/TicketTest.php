<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/tickets')->assertRedirect('/login');
    }

    public function test_ticket_can_be_created_with_defaults(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tickets', [
            'client' => 'Cliente X',
            'subject' => 'Bug nel modulo',
        ]);

        $response->assertRedirect(route('tickets.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('tickets', [
            'user_id' => $user->id,
            'client' => 'Cliente X',
            'subject' => 'Bug nel modulo',
            'status' => 'aperto',
        ]);

        $this->assertNotNull(Ticket::first()->last_update);
    }

    public function test_ticket_client_and_subject_are_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/tickets', [])->assertSessionHasErrors(['client', 'subject']);
    }

    public function test_ticket_priority_must_be_valid(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/tickets', ['client' => 'C', 'subject' => 'S', 'priority' => 'critica'])
            ->assertSessionHasErrors('priority');
    }

    public function test_ticket_category_must_be_valid(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/tickets', ['client' => 'C', 'subject' => 'S', 'category' => 'altro'])
            ->assertSessionHasErrors('category');
    }

    public function test_ticket_can_be_updated(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::create([
            'user_id' => $user->id,
            'client' => 'Cliente X',
            'subject' => 'Bug',
            'status' => 'aperto',
            'last_update' => now()->subDay(),
        ]);

        $this->actingAs($user)
            ->patch("/tickets/{$ticket->id}", [
                'client' => 'Cliente X',
                'subject' => 'Bug',
                'status' => 'in_lavorazione',
            ])
            ->assertRedirect(route('tickets.index'));

        $ticket->refresh();
        $this->assertSame('in_lavorazione', $ticket->status);
        $this->assertTrue($ticket->last_update->isToday());
    }

    public function test_user_cannot_update_other_users_ticket(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $ticket = Ticket::create([
            'user_id' => $other->id,
            'client' => 'Altrui',
            'subject' => 'Bug',
            'status' => 'aperto',
            'last_update' => now(),
        ]);

        $this->actingAs($user)
            ->patch("/tickets/{$ticket->id}", ['client' => 'Altrui', 'subject' => 'Modificato'])
            ->assertForbidden();
    }

    public function test_user_cannot_delete_other_users_ticket(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $ticket = Ticket::create([
            'user_id' => $other->id,
            'client' => 'Altrui',
            'subject' => 'Bug',
            'status' => 'aperto',
            'last_update' => now(),
        ]);

        $this->actingAs($user)->delete("/tickets/{$ticket->id}")->assertForbidden();

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    }
}