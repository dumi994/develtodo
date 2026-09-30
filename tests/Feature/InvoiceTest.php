<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/invoices')->assertRedirect('/login');
    }

    public function test_invoice_number_is_generated_sequentially(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/invoices', ['client' => 'A', 'amount' => 100]);
        $this->actingAs($user)->post('/invoices', ['client' => 'B', 'amount' => 200]);

        $numbers = Invoice::orderBy('id')->pluck('number')->all();
        $prefix = 'INV-' . now()->format('Ymd') . '-';

        $this->assertSame([$prefix . '001', $prefix . '002'], $numbers);
    }

    public function test_invoice_can_be_created_with_default_status(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/invoices', ['client' => 'Cliente X', 'amount' => 500]);

        $this->assertDatabaseHas('invoices', [
            'user_id' => $user->id,
            'client' => 'Cliente X',
            'amount' => 500,
            'status' => 'da_fare',
        ]);
    }

    public function test_invoice_client_and_amount_are_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/invoices', [])->assertSessionHasErrors(['client', 'amount']);
    }

    public function test_invoice_sets_paid_at_when_marked_paid(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'client' => 'Cliente X',
            'amount' => 500,
            'number' => 'INV-TEST-001',
            'status' => 'da_fare',
        ]);

        $this->actingAs($user)
            ->patch("/invoices/{$invoice->id}", [
                'client' => 'Cliente X',
                'amount' => 500,
                'status' => 'pagata',
            ])
            ->assertRedirect(route('invoices.index'));

        $invoice->refresh();
        $this->assertSame('pagata', $invoice->status);
        $this->assertNotNull($invoice->paid_at);
    }

    public function test_invoice_sets_sent_at_when_marked_sent(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'client' => 'Cliente X',
            'amount' => 500,
            'number' => 'INV-TEST-002',
            'status' => 'da_fare',
        ]);

        $this->actingAs($user)
            ->patch("/invoices/{$invoice->id}", [
                'client' => 'Cliente X',
                'amount' => 500,
                'status' => 'inviata',
            ])
            ->assertRedirect(route('invoices.index'));

        $invoice->refresh();
        $this->assertSame('inviata', $invoice->status);
        $this->assertNotNull($invoice->sent_at);
    }

    public function test_invoice_status_must_be_valid(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'client' => 'Cliente X',
            'amount' => 500,
            'number' => 'INV-TEST-003',
            'status' => 'da_fare',
        ]);

        $this->actingAs($user)
            ->patch("/invoices/{$invoice->id}", ['client' => 'Cliente X', 'amount' => 500, 'status' => 'boh'])
            ->assertSessionHasErrors('status');
    }

    public function test_user_cannot_update_other_users_invoice(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $other->id,
            'client' => 'Altrui',
            'amount' => 500,
            'number' => 'INV-TEST-004',
            'status' => 'da_fare',
        ]);

        $this->actingAs($user)
            ->patch("/invoices/{$invoice->id}", ['client' => 'Altrui', 'amount' => 999])
            ->assertForbidden();
    }

    public function test_user_cannot_delete_other_users_invoice(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $other->id,
            'client' => 'Altrui',
            'amount' => 500,
            'number' => 'INV-TEST-005',
            'status' => 'da_fare',
        ]);

        $this->actingAs($user)->delete("/invoices/{$invoice->id}")->assertForbidden();

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);
    }
}