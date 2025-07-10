<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Person;
use App\Models\Balance;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['fromPerson', 'toPerson'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $people = Person::where('user_id', Auth::id())->get();
        return view('transactions.create', compact('people'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:payment,receipt',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'from_person_id' => 'required|exists:people,id',
            'to_person_id' => 'required|exists:people,id|different:from_person_id'
        ]);

        // Validate that both people belong to the current user
        $fromPerson = Person::where('id', $request->from_person_id)->where('user_id', Auth::id())->first();
        $toPerson = Person::where('id', $request->to_person_id)->where('user_id', Auth::id())->first();

        if (!$fromPerson || !$toPerson) {
            abort(403, 'Unauthorized action.');
        }

        $transaction = Transaction::create([
            'type' => $request->type,
            'amount' => $request->amount,
            'paid_amount' => 0,
            'remaining_amount' => $request->amount,
            'description' => $request->description,
            'status' => 'pending',
            'from_person_id' => $request->from_person_id,
            'to_person_id' => $request->to_person_id,
            'user_id' => Auth::id()
        ]);

        $this->updateBalance();

        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully!');
    }

    public function markPaid(Request $request, Transaction $transaction)
    {
        // Ensure user can only mark their own transactions as paid
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'paid_amount' => 'required|numeric|min:0.01|max:' . $transaction->remaining_amount
        ]);

        $paidAmount = (float) $request->paid_amount;
        $transaction->setAttribute('paid_amount', (float) $transaction->paid_amount + $paidAmount);
        $transaction->setAttribute('remaining_amount', (float) $transaction->remaining_amount - $paidAmount);

        if ($transaction->remaining_amount <= 0) {
            $transaction->status = 'completed';
        } else {
            $transaction->status = 'partial';

            // Create a new transaction for the remaining amount
            Transaction::create([
                'type' => $transaction->type,
                'amount' => $transaction->remaining_amount,
                'paid_amount' => 0,
                'remaining_amount' => $transaction->remaining_amount,
                'description' => 'Remaining amount from transaction #' . $transaction->id,
                'status' => 'pending',
                'from_person_id' => $transaction->from_person_id,
                'to_person_id' => $transaction->to_person_id,
                'parent_transaction_id' => $transaction->id,
                'user_id' => Auth::id()
            ]);
        }

        $transaction->save();

        // Deduct/add the paid amount from/to the current balance
        $balance = Balance::where('user_id', Auth::id())->first();
        if ($balance) {
            if ($transaction->type === 'payment') {
                // Payment: money goes out, deduct from balance
                $balance->current_balance -= $paidAmount;
            } elseif ($transaction->type === 'receipt') {
                // Receipt: money comes in, add to balance
                $balance->current_balance += $paidAmount;
            }
            $balance->save();
        }

        $this->updateBalance();

        return redirect()->back()->with('success', 'Payment recorded successfully!');
    }

    private function updateBalance()
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

        $totalToPay = Transaction::where('type', 'payment')
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'completed')
            ->sum('remaining_amount');

        $totalToReceive = Transaction::where('type', 'receipt')
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'completed')
            ->sum('remaining_amount');

        $balance->total_to_pay = $totalToPay;
        $balance->total_to_receive = $totalToReceive;
        $balance->save();
    }
}
