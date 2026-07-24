<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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

    // Seed se richiesto
    if ($request->boolean('seed')) {
      Artisan::call('db:seed', ['--force' => true]);
    }

    // Crea file marker per evitare di ripetere il setup
    file_put_contents(storage_path('app/.seeded'), now()->toISOString());

    return redirect()->route('dashboard')->with('success', 'Setup completato!');
  }
}
