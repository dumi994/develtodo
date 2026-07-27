<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
  /**
   * Crea l'utente amministratore predefinito per l'app desktop locale.
   * Credenziali: admin@develtodo.local / admin123
   */
  public function run(): void
  {
    User::updateOrCreate(
      ['email' => 'admin@develtodo.local'],
      [
        'name' => 'Admin',
        'email' => 'admin@develtodo.local',
        'email_verified_at' => now(),
        'password' => Hash::make('admin123'),
      ]
    );
  }
}
