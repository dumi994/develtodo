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
    $seedFile = storage_path('app/.seeded');
    if (!is_dir(dirname($seedFile))) {
      @mkdir(dirname($seedFile), 0777, true);
    }
    file_put_contents($seedFile, now()->toISOString());

    // Autentica l'admin e vai al dashboard
    $admin = User::where('email', $adminEmail)->first();
    if ($admin) {
      auth()->login($admin);
    }

    return redirect()->route('dashboard')->with('status', 'Setup completato! Benvenuto in DevelTodo.');
  }
}
