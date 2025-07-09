<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Balance;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::orderBy('date', 'desc')->paginate(10);
        $totalExpenses = Expense::sum('amount');

        return view('expenses.index', compact('expenses', 'totalExpenses'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'date' => 'required|date'
        ]);

        $expense = Expense::create($request->all());

        // Update balance - subtract expense from current balance
        $this->updateBalance(-$expense->amount);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense added successfully!');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'date' => 'required|date'
        ]);

        $oldAmount = $expense->amount;
        $expense->update($request->all());

        // Update balance - reverse old expense and apply new expense
        $this->updateBalance($oldAmount - $expense->amount);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        $amount = $expense->amount;
        $expense->delete();

        // Update balance - add back the deleted expense amount
        $this->updateBalance($amount);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully!');
    }

    private function updateBalance($amount)
    {
        $balance = Balance::first();
        if (!$balance) {
            $balance = Balance::create([
                'current_balance' => 0,
                'total_to_pay' => 0,
                'total_to_receive' => 0
            ]);
        }

        $balance->current_balance += $amount;
        $balance->save();
    }
}
