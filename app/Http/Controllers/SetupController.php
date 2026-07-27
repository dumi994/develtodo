<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class SetupController extends Controller
{
  public function index()
  {
    $seeded = file_exists(storage_path('app/.seeded'));
    return view('setup', compact('seeded'));
  }

  public function run(Request $request)
  {
    $request->validate([
      'seed' => 'nullable|boolean',
    ]);

    // Esegue migration
    Artisan::call('migrate', ['--force' => true]);

    // Crea sempre l'utente admin predefinito (app desktop locale)
    $adminEmail = 'admin@develtodo.local';
    $adminPassword = 'admin123';
    User::updateOrCreate(
      ['email' => $adminEmail],
      [
        'name' => 'Admin',
        'email' => $adminEmail,
        'email_verified_at' => now(),
        'password' => Hash::make($adminPassword),
      ]
    );

    // Seed dati di esempio se richiesto
    if ($request->boolean('seed')) {
      Artisan::call('db:seed', ['--force' => true]);
    }

    // Crea file marker per evitare di ripetere il setup
    file_put_contents(storage_path('app/.seeded'), now()->toISOString());

    return redirect()->route('login')->with('status', 'Setup completato! Usa le credenziali admin@develtodo.local / admin123');
  }
}
