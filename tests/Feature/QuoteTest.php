<?php

namespace Tests\Feature;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/quotes')->assertRedirect('/login');
    }

    public function test_quote_can_be_created_with_defaults(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/quotes', [
            'client' => 'Cliente X',
            'amount' => 1250.50,
        ]);

        $response->assertRedirect(route('quotes.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('quotes', [
            'user_id' => $user->id,
            'client' => 'Cliente X',
            'amount' => 1250.50,
            'status' => 'bozza',
        ]);
    }

    public function test_quote_client_and_amount_are_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/quotes', [])->assertSessionHasErrors(['client', 'amount']);
    }

    public function test_quote_amount_must_be_positive(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/quotes', ['client' => 'C', 'amount' => -5])
            ->assertSessionHasErrors('amount');
    }

    public function test_quote_can_be_updated(): void
    {
        $user = User::factory()->create();
        $quote = Quote::create([
            'user_id' => $user->id,
            'client' => 'Cliente X',
            'amount' => 100,
            'status' => 'bozza',
        ]);

        $this->actingAs($user)
            ->patch("/quotes/{$quote->id}", [
                'client' => 'Cliente X',
                'amount' => 1500,
                'status' => 'inviato',
            ])
            ->assertRedirect(route('quotes.index'));

        $this->assertDatabaseHas('quotes', [
            'id' => $quote->id,
            'amount' => 1500,
            'status' => 'inviato',
        ]);
    }

    public function test_quote_status_must_be_valid(): void
    {
        $user = User::factory()->create();
        $quote = Quote::create([
            'user_id' => $user->id,
            'client' => 'Cliente X',
            'amount' => 100,
            'status' => 'bozza',
        ]);

        $this->actingAs($user)
            ->patch("/quotes/{$quote->id}", ['client' => 'Cliente X', 'amount' => 100, 'status' => 'perso'])
            ->assertSessionHasErrors('status');
    }

    public function test_user_cannot_update_other_users_quote(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $quote = Quote::create([
            'user_id' => $other->id,
            'client' => 'Altrui',
            'amount' => 100,
            'status' => 'bozza',
        ]);

        $this->actingAs($user)
            ->patch("/quotes/{$quote->id}", ['client' => 'Altrui', 'amount' => 999])
            ->assertForbidden();
    }

    public function test_user_cannot_delete_other_users_quote(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $quote = Quote::create([
            'user_id' => $other->id,
            'client' => 'Altrui',
            'amount' => 100,
            'status' => 'bozza',
        ]);

        $this->actingAs($user)->delete("/quotes/{$quote->id}")->assertForbidden();

        $this->assertDatabaseHas('quotes', ['id' => $quote->id]);
    }
}