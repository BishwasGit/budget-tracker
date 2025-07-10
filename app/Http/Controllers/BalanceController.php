<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\Person;
use App\Models\Expense;
use App\Models\Goal;

class BalanceController extends Controller
{
    public function index()
    {
        $balance = Balance::where('user_id', Auth::id())->first();
        if (!$balance) {
            $balance = Balance::create([
                'user_id' => Auth::id(),
                'current_balance' => 0,
                'total_to_pay' => 0,
                'total_to_receive' => 0
            ]);
        }

        $recentTransactions = Transaction::with(['fromPerson', 'toPerson'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $totalExpenses = Expense::where('user_id', Auth::id())->sum('amount');
        $recentExpenses = Expense::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $reservedAmount = Goal::where('user_id', Auth::id())
            ->where('status', 'active')
            ->sum('current_amount');

        return view('balance.index', compact('balance', 'recentTransactions', 'totalExpenses', 'recentExpenses','reservedAmount'));
    }

    public function addBalance(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $balance = Balance::where('user_id', Auth::id())->first();
        if (!$balance) {
            $balance = Balance::create([
                'user_id' => Auth::id(),
                'current_balance' => 0,
                'total_to_pay' => 0,
                'total_to_receive' => 0
            ]);
        }

        $balance->current_balance += $request->amount;
        $balance->save();

        return redirect()->back()->with('success', 'Balance added successfully!');
    }
}
