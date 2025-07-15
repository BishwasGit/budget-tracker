<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Person;
use App\Models\Transaction;
use App\Models\Balance;
use App\Models\Expense;
use App\Models\Goal;

class BackupController extends Controller
{
    public function index()
    {
        // Get user's data counts for the dashboard
        $dataStats = [
            'people' => Person::where('user_id', Auth::id())->count(),
            'transactions' => Transaction::where('user_id', Auth::id())->count(),
            'expenses' => Expense::where('user_id', Auth::id())->count(),
            'goals' => Goal::where('user_id', Auth::id())->count(),
        ];

        return view('backup.index', compact('dataStats'));
    }

    public function download()
    {
        $userId = Auth::id();
        $userName = Auth::user()->name;

        // Gather all user data
        $backupData = [
            'user' => [
                'id' => $userId,
                'name' => $userName,
                'email' => Auth::user()->email,
            ],
            'backup_date' => now()->toDateTimeString(),
            'people' => Person::where('user_id', $userId)->get()->toArray(),
            'transactions' => Transaction::with(['fromPerson', 'toPerson'])
                ->where('user_id', $userId)->get()->toArray(),
            'balance' => Balance::where('user_id', $userId)->first()?->toArray(),
            'expenses' => Expense::where('user_id', $userId)->get()->toArray(),
            'goals' => Goal::where('user_id', $userId)->get()->toArray(),
        ];

        // Create JSON backup
        $jsonData = json_encode($backupData, JSON_PRETTY_PRINT);

        // Generate filename with timestamp
        $filename = 'budget_backup_' . $userName . '_' . now()->format('Y-m-d_H-i-s') . '.json';

        // Return as download
        return response($jsonData)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function downloadCsv()
    {
        $userId = Auth::id();
        $userName = Auth::user()->name;

        // Create CSV content
        $csvContent = $this->generateCsvBackup($userId);

        // Generate filename with timestamp
        $filename = 'budget_backup_' . $userName . '_' . now()->format('Y-m-d_H-i-s') . '.csv';

        // Return as download
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function downloadSql()
    {
        $userId = Auth::id();
        $userName = Auth::user()->name;

        // Create SQL content
        $sqlContent = $this->generateSqlBackup($userId);

        // Generate filename with timestamp
        $filename = 'budget_backup_' . $userName . '_' . now()->format('Y-m-d_H-i-s') . '.sql';

        // Return as download
        return response($sqlContent)
            ->header('Content-Type', 'application/sql')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function generateCsvBackup($userId)
    {
        $csv = "Budget Tracker Backup - " . Auth::user()->name . "\n";
        $csv .= "Generated on: " . now()->format('Y-m-d H:i:s') . "\n\n";

        // People data
        $csv .= "PEOPLE\n";
        $csv .= "ID,Name,Email,Phone,Address,Created At\n";
        $people = Person::where('user_id', $userId)->get();
        foreach ($people as $person) {
            $csv .= implode(',', [
                $person->id,
                '"' . str_replace('"', '""', $person->name) . '"',
                '"' . str_replace('"', '""', $person->email ?? '') . '"',
                '"' . str_replace('"', '""', $person->phone ?? '') . '"',
                '"' . str_replace('"', '""', $person->address ?? '') . '"',
                $person->created_at
            ]) . "\n";
        }

        // Transactions data
        $csv .= "\nTRANSACTIONS\n";
        $csv .= "ID,Type,Amount,Paid Amount,Remaining Amount,Status,From Person,To Person,Description,Created At\n";
        $transactions = Transaction::with(['fromPerson', 'toPerson'])->where('user_id', $userId)->get();
        foreach ($transactions as $transaction) {
            $csv .= implode(',', [
                $transaction->id,
                $transaction->type,
                $transaction->amount,
                $transaction->paid_amount,
                $transaction->remaining_amount,
                $transaction->status,
                '"' . str_replace('"', '""', $transaction->fromPerson->name ?? '') . '"',
                '"' . str_replace('"', '""', $transaction->toPerson->name ?? '') . '"',
                '"' . str_replace('"', '""', $transaction->description ?? '') . '"',
                $transaction->created_at
            ]) . "\n";
        }

        // Expenses data
        $csv .= "\nEXPENSES\n";
        $csv .= "ID,Title,Description,Amount,Category,Date,Created At\n";
        $expenses = Expense::where('user_id', $userId)->get();
        foreach ($expenses as $expense) {
            $csv .= implode(',', [
                $expense->id,
                '"' . str_replace('"', '""', $expense->title) . '"',
                '"' . str_replace('"', '""', $expense->description ?? '') . '"',
                $expense->amount,
                '"' . str_replace('"', '""', $expense->category ?? '') . '"',
                $expense->date,
                $expense->created_at
            ]) . "\n";
        }

        // Goals data
        $csv .= "\nGOALS\n";
        $csv .= "ID,Title,Description,Target Amount,Current Amount,Deadline,Status,Created At\n";
        $goals = Goal::where('user_id', $userId)->get();
        foreach ($goals as $goal) {
            $csv .= implode(',', [
                $goal->id,
                '"' . str_replace('"', '""', $goal->title) . '"',
                '"' . str_replace('"', '""', $goal->description ?? '') . '"',
                $goal->target_amount,
                $goal->current_amount,
                $goal->deadline ?? '',
                $goal->status ?? 'active',
                $goal->created_at
            ]) . "\n";
        }

        // Balance data
        $csv .= "\nBALANCE\n";
        $csv .= "Current Balance,Total To Pay,Total To Receive,Updated At\n";
        $balance = Balance::where('user_id', $userId)->first();
        if ($balance) {
            $csv .= implode(',', [
                $balance->current_balance,
                $balance->total_to_pay,
                $balance->total_to_receive,
                $balance->updated_at
            ]) . "\n";
        }

        return $csv;
    }

    private function generateSqlBackup($userId)
    {
        $userName = Auth::user()->name;
        $userEmail = Auth::user()->email;
        $timestamp = now()->format('Y-m-d H:i:s');

        $sql = "-- Budget Tracker SQL Backup\n";
        $sql .= "-- User: {$userName} ({$userEmail})\n";
        $sql .= "-- Generated on: {$timestamp}\n";
        $sql .= "-- This backup contains only data for user ID: {$userId}\n\n";

        $sql .= "-- Note: This SQL backup is for data restoration purposes\n";
        $sql .= "-- Make sure to have the same database structure before importing\n\n";

        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        // User data (for reference)
        $sql .= "-- USER DATA (for reference)\n";
        $sql .= "-- INSERT INTO users (id, name, email, created_at, updated_at) VALUES\n";
        $sql .= "-- ({$userId}, '{$userName}', '{$userEmail}', NOW(), NOW());\n\n";

        // People data
        $people = Person::where('user_id', $userId)->get();
        if ($people->count() > 0) {
            $sql .= "-- PEOPLE DATA\n";
            $sql .= "INSERT INTO people (id, name, email, phone, address, user_id, created_at, updated_at) VALUES\n";
            $values = [];
            foreach ($people as $person) {
                $name = $this->escapeSqlString($person->name);
                $email = $this->escapeSqlString($person->email);
                $phone = $this->escapeSqlString($person->phone);
                $address = $this->escapeSqlString($person->address);
                $createdAt = $person->created_at->format('Y-m-d H:i:s');
                $updatedAt = $person->updated_at->format('Y-m-d H:i:s');

                $values[] = "({$person->id}, '{$name}', " .
                           ($email ? "'{$email}'" : 'NULL') . ", " .
                           ($phone ? "'{$phone}'" : 'NULL') . ", " .
                           ($address ? "'{$address}'" : 'NULL') . ", " .
                           "{$userId}, '{$createdAt}', '{$updatedAt}')";
            }
            $sql .= implode(",\n", $values) . ";\n\n";
        }

        // Balance data
        $balance = Balance::where('user_id', $userId)->first();
        if ($balance) {
            $sql .= "-- BALANCE DATA\n";
            $sql .= "INSERT INTO balances (id, current_balance, total_to_pay, total_to_receive, user_id, created_at, updated_at) VALUES\n";
            $createdAt = $balance->created_at->format('Y-m-d H:i:s');
            $updatedAt = $balance->updated_at->format('Y-m-d H:i:s');
            $sql .= "({$balance->id}, " . (float)$balance->current_balance . ", " . (float)$balance->total_to_pay . ", " . (float)$balance->total_to_receive . ", {$userId}, '{$createdAt}', '{$updatedAt}');\n\n";
        }

        // Transactions data
        $transactions = Transaction::where('user_id', $userId)->get();
        if ($transactions->count() > 0) {
            $sql .= "-- TRANSACTIONS DATA\n";
            $sql .= "INSERT INTO transactions (id, type, amount, paid_amount, remaining_amount, description, status, from_person_id, to_person_id, parent_transaction_id, user_id, created_at, updated_at) VALUES\n";
            $values = [];
            foreach ($transactions as $transaction) {
                $description = $this->escapeSqlString($transaction->description);
                $createdAt = $transaction->created_at->format('Y-m-d H:i:s');
                $updatedAt = $transaction->updated_at->format('Y-m-d H:i:s');

                $values[] = "({$transaction->id}, '{$transaction->type}', " . (float)$transaction->amount . ", " . (float)$transaction->paid_amount . ", " . (float)$transaction->remaining_amount . ", " .
                           ($description ? "'{$description}'" : 'NULL') . ", '{$transaction->status}', " .
                           ($transaction->from_person_id ?: 'NULL') . ", " .
                           ($transaction->to_person_id ?: 'NULL') . ", " .
                           ($transaction->parent_transaction_id ?: 'NULL') . ", " .
                           "{$userId}, '{$createdAt}', '{$updatedAt}')";
            }
            $sql .= implode(",\n", $values) . ";\n\n";
        }

        // Expenses data
        $expenses = Expense::where('user_id', $userId)->get();
        if ($expenses->count() > 0) {
            $sql .= "-- EXPENSES DATA\n";
            $sql .= "INSERT INTO expenses (id, title, description, amount, category, date, user_id, created_at, updated_at) VALUES\n";
            $values = [];
            foreach ($expenses as $expense) {
                $title = $this->escapeSqlString($expense->title);
                $description = $this->escapeSqlString($expense->description);
                $category = $this->escapeSqlString($expense->category);
                $date = $expense->date;
                $createdAt = $expense->created_at->format('Y-m-d H:i:s');
                $updatedAt = $expense->updated_at->format('Y-m-d H:i:s');

                $values[] = "({$expense->id}, '{$title}', " .
                           ($description ? "'{$description}'" : 'NULL') . ", " . (float)$expense->amount . ", " .
                           ($category ? "'{$category}'" : 'NULL') . ", '" . (string)$date . "', " .
                           "{$userId}, '{$createdAt}', '{$updatedAt}')";
            }
            $sql .= implode(",\n", $values) . ";\n\n";
        }

        // Goals data
        $goals = Goal::where('user_id', $userId)->get();
        if ($goals->count() > 0) {
            $sql .= "-- GOALS DATA\n";
            $sql .= "INSERT INTO goals (id, title, description, target_amount, current_amount, deadline, status, user_id, created_at, updated_at) VALUES\n";
            $values = [];
            foreach ($goals as $goal) {
                $title = $this->escapeSqlString($goal->title);
                $description = $this->escapeSqlString($goal->description);
                $deadline = $goal->deadline;
                $status = $goal->status ?? 'active';
                $createdAt = $goal->created_at->format('Y-m-d H:i:s');
                $updatedAt = $goal->updated_at->format('Y-m-d H:i:s');

                $values[] = "({$goal->id}, '{$title}', " .
                           ($description ? "'{$description}'" : 'NULL') . ", " . (float)$goal->target_amount . ", " . (float)$goal->current_amount . ", " .
                           ($deadline ? "'" . (string)$deadline . "'" : 'NULL') . ", '{$status}', " .
                           "{$userId}, '{$createdAt}', '{$updatedAt}')";
            }
            $sql .= implode(",\n", $values) . ";\n\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n\n";
        $sql .= "-- End of backup\n";

        return $sql;
    }

    private function escapeSqlString($string)
    {
        if ($string === null) {
            return null;
        }
        return str_replace(["'", "\\"], ["''", "\\\\"], $string);
    }
}
