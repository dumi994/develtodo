<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Task;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Ticket;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    public function checkAll()
    {
        $user = Auth::user();
        if (!$user) return;

        $this->checkTaskDeadlines($user);
        $this->checkOverdueInvoices($user);
        $this->checkPendingQuotes($user);
        $this->checkStaleTickets($user);
        $this->checkProjectDeadlines($user);
    }

    public function checkTaskDeadlines($user)
    {
        $tasks = Task::where('user_id', $user->id)
            ->whereIn('status', ['da_fare', 'in_corso'])
            ->whereDate('due_date', now()->addDay())
            ->get();

        foreach ($tasks as $task) {
            $exists = Notification::where('user_id', $user->id)
                ->where('type', 'task_deadline')
                ->where('link', route('tasks.index'))
                ->whereDate('created_at', today())
                ->exists();

            if (!$exists) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'task_deadline',
                    'title' => "Task \"{$task->title}\" scade domani",
                    'icon' => '?',
                    'link' => route('tasks.index'),
                ]);
            }
        }
    }

    public function checkOverdueInvoices($user)
    {
        $invoices = Invoice::where('user_id', $user->id)
            ->where('status', '!=', 'pagata')
            ->whereDate('due_date', '<', now())
            ->get();

        foreach ($invoices as $inv) {
            $exists = Notification::where('user_id', $user->id)
                ->where('type', 'overdue_invoice')
                ->where('link', route('invoices.index'))
                ->whereDate('created_at', today())
                ->exists();

            if (!$exists) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'overdue_invoice',
                    'title' => "Fattura {$inv->number} per {$inv->client} in ritardo",
                    'icon' => '??',
                    'link' => route('invoices.index'),
                ]);
            }
        }
    }

    public function checkPendingQuotes($user)
    {
        $quotes = Quote::where('user_id', $user->id)
            ->where('status', 'inviato')
            ->whereDate('created_at', '<', now()->subDays(7))
            ->get();

        foreach ($quotes as $q) {
            $exists = Notification::where('user_id', $user->id)
                ->where('type', 'pending_quote')
                ->where('link', route('quotes.index'))
                ->whereDate('created_at', today())
                ->exists();

            if (!$exists) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'pending_quote',
                    'title' => "Preventivo per {$q->client} in attesa da 7+ giorni",
                    'icon' => '??',
                    'link' => route('quotes.index'),
                ]);
            }
        }
    }

    public function checkStaleTickets($user)
    {
        $tickets = Ticket::where('user_id', $user->id)
            ->where('status', '!=', 'chiuso')
            ->whereDate('last_update', '<', now()->subDays(3))
            ->get();

        foreach ($tickets as $t) {
            $exists = Notification::where('user_id', $user->id)
                ->where('type', 'stale_ticket')
                ->where('link', route('tickets.index'))
                ->whereDate('created_at', today())
                ->exists();

            if (!$exists) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'stale_ticket',
                    'title' => "Ticket \"{$t->subject}\" non aggiornato da 3+ giorni",
                    'icon' => '??',
                    'link' => route('tickets.index'),
                ]);
            }
        }
    }

    public function checkProjectDeadlines($user)
    {
        $projects = Project::where('user_id', $user->id)
            ->whereIn('status', ['in_arrivo', 'in_corso', 'in_revisione'])
            ->whereDate('due_date', now()->addWeek())
            ->get();

        foreach ($projects as $p) {
            $exists = Notification::where('user_id', $user->id)
                ->where('type', 'project_deadline')
                ->where('link', route('projects.index'))
                ->whereDate('created_at', today())
                ->exists();

            if (!$exists) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'project_deadline',
                    'title' => "Progetto \"{$p->name}\" in scadenza tra 7 giorni",
                    'icon' => '??',
                    'link' => route('projects.index'),
                ]);
            }
        }
    }
}
