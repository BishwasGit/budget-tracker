<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Models\Person;
use App\Models\Transaction;
use App\Models\Balance;
use App\Models\Expense;
use App\Models\Goal;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get counts for display
        $stats = [
            'people' => Person::where('user_id', $user->id)->count(),
            'transactions' => Transaction::where('user_id', $user->id)->count(),
            'expenses' => Expense::where('user_id', $user->id)->count(),
            'goals' => Goal::where('user_id', $user->id)->count(),
            'balance' => Balance::where('user_id', $user->id)->first()
        ];

        return view('backup.index', compact('stats'));
    }

    public function exportJson()
    {
        $user = Auth::user();

        // Collect all user data
        $data = [
            'export_info' => [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'export_date' => Carbon::now()->toISOString(),
                'app_name' => 'Budget Tracker',
                'version' => '1.0'
            ],
            'people' => Person::where('user_id', $user->id)->get()->toArray(),
            'transactions' => Transaction::with(['fromPerson', 'toPerson'])
                ->where('user_id', $user->id)
                ->get()
                ->toArray(),
            'balance' => Balance::where('user_id', $user->id)->first(),
            'expenses' => Expense::where('user_id', $user->id)->get()->toArray(),
            'goals' => Goal::where('user_id', $user->id)->get()->toArray()
        ];

        $filename = 'budget-tracker-backup-' . $user->id . '-' . Carbon::now()->format('Y-m-d-H-i-s') . '.json';

        return Response::json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ], JSON_PRETTY_PRINT);
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type', 'transactions');

        switch ($type) {
            case 'people':
                return $this->exportPeopleCsv($user);
            case 'transactions':
                return $this->exportTransactionsCsv($user);
            case 'expenses':
                return $this->exportExpensesCsv($user);
            case 'goals':
                return $this->exportGoalsCsv($user);
            default:
                return redirect()->back()->with('error', 'Invalid export type');
        }
    }

    private function exportPeopleCsv($user)
    {
        $people = Person::where('user_id', $user->id)->get();

        $csvData = "ID,Name,Email,Phone,Address,Created At\n";
        foreach ($people as $person) {
            $csvData .= sprintf(
                "%d,\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $person->id,
                str_replace('"', '""', $person->name),
                str_replace('"', '""', $person->email ?? ''),
                str_replace('"', '""', $person->phone ?? ''),
                str_replace('"', '""', $person->address ?? ''),
                $person->created_at->format('Y-m-d H:i:s')
            );
        }

        $filename = 'people-export-' . Carbon::now()->format('Y-m-d') . '.csv';

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    private function exportTransactionsCsv($user)
    {
        $transactions = Transaction::with(['fromPerson', 'toPerson'])
            ->where('user_id', $user->id)
            ->get();

        $csvData = "ID,Type,Amount,Paid Amount,Remaining Amount,Status,From Person,To Person,Description,Created At\n";
        foreach ($transactions as $transaction) {
            $csvData .= sprintf(
                "%d,\"%s\",%.2f,%.2f,%.2f,\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $transaction->id,
                $transaction->type,
                $transaction->amount,
                $transaction->paid_amount,
                $transaction->remaining_amount,
                $transaction->status,
                $transaction->fromPerson ? str_replace('"', '""', $transaction->fromPerson->name) : '',
                $transaction->toPerson ? str_replace('"', '""', $transaction->toPerson->name) : '',
                str_replace('"', '""', $transaction->description ?? ''),
                $transaction->created_at->format('Y-m-d H:i:s')
            );
        }

        $filename = 'transactions-export-' . Carbon::now()->format('Y-m-d') . '.csv';

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    private function exportExpensesCsv($user)
    {
        $expenses = Expense::where('user_id', $user->id)->get();

        $csvData = "ID,Title,Description,Amount,Category,Date,Created At\n";
        foreach ($expenses as $expense) {
            $csvData .= sprintf(
                "%d,\"%s\",\"%s\",%.2f,\"%s\",\"%s\",\"%s\"\n",
                $expense->id,
                str_replace('"', '""', $expense->title),
                str_replace('"', '""', $expense->description ?? ''),
                $expense->amount,
                str_replace('"', '""', $expense->category ?? ''),
                $expense->date?->format('Y-m-d') ?? '',
                $expense->created_at->format('Y-m-d H:i:s')
            );
        }

        $filename = 'expenses-export-' . Carbon::now()->format('Y-m-d') . '.csv';

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    private function exportGoalsCsv($user)
    {
        $goals = Goal::where('user_id', $user->id)->get();

        $csvData = "ID,Title,Description,Target Amount,Current Amount,Progress %,Status,Deadline,Created At\n";
        foreach ($goals as $goal) {
            $progress = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0;
            $csvData .= sprintf(
                "%d,\"%s\",\"%s\",%.2f,%.2f,%.2f,\"%s\",\"%s\",\"%s\"\n",
                $goal->id,
                str_replace('"', '""', $goal->title),
                str_replace('"', '""', $goal->description ?? ''),
                $goal->target_amount,
                $goal->current_amount,
                $progress,
                str_replace('"', '""', $goal->status ?? 'active'),
                $goal->deadline?->format('Y-m-d') ?? '',
                $goal->created_at->format('Y-m-d H:i:s')
            );
        }

        $filename = 'goals-export-' . Carbon::now()->format('Y-m-d') . '.csv';

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}
