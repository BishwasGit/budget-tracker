<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\Person;

class BalanceController extends Controller
{
    public function index()
    {
        $balance = Balance::first();
        if (!$balance) {
            $balance = Balance::create([
                'current_balance' => 0,
                'total_to_pay' => 0,
                'total_to_receive' => 0
            ]);
        }

        $recentTransactions = Transaction::with(['fromPerson', 'toPerson'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('balance.index', compact('balance', 'recentTransactions'));
    }

    public function addBalance(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $balance = Balance::first();
        if (!$balance) {
            $balance = Balance::create([
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
