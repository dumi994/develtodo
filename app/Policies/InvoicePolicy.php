<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invoice $item): bool
    {
        return $item->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Invoice $item): bool
    {
        return $item->user_id === $user->id;
    }

    public function delete(User $user, Invoice $item): bool
    {
        return $item->user_id === $user->id;
    }

    public function restore(User $user, Invoice $item): bool
    {
        return $item->user_id === $user->id;
    }

    public function forceDelete(User $user, Invoice $item): bool
    {
        return $item->user_id === $user->id;
    }
}
