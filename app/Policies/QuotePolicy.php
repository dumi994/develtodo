<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Quote $item): bool
    {
        return $item->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Quote $item): bool
    {
        return $item->user_id === $user->id;
    }

    public function delete(User $user, Quote $item): bool
    {
        return $item->user_id === $user->id;
    }

    public function restore(User $user, Quote $item): bool
    {
        return $item->user_id === $user->id;
    }

    public function forceDelete(User $user, Quote $item): bool
    {
        return $item->user_id === $user->id;
    }
}
