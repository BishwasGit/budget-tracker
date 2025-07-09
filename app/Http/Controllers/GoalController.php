<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Goal;
use App\Models\Balance;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::orderBy('created_at', 'desc')->get();
        $totalGoalsAmount = Goal::sum('target_amount');
        $totalSavedAmount = Goal::sum('current_amount');
        $balance = Balance::first();

        return view('goals.index', compact('goals', 'totalGoalsAmount', 'totalSavedAmount', 'balance'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0.01',
            'deadline' => 'nullable|date|after:today'
        ]);

        Goal::create($request->all());

        return redirect()->route('goals.index')
            ->with('success', 'Goal created successfully!');
    }

    public function edit(Goal $goal)
    {
        return view('goals.edit', compact('goal'));
    }

    public function update(Request $request, Goal $goal)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0.01',
            'deadline' => 'nullable|date|after:today',
            'status' => 'required|in:active,completed,paused'
        ]);

        $goal->update($request->all());

        return redirect()->route('goals.index')
            ->with('success', 'Goal updated successfully!');
    }

    public function destroy(Goal $goal)
    {
        // If goal has saved money, return it to balance
        if ($goal->current_amount > 0) {
            $this->updateBalance($goal->current_amount);
        }

        $goal->delete();

        return redirect()->route('goals.index')
            ->with('success', 'Goal deleted successfully!');
    }

    public function allocate(Request $request, Goal $goal)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        $balance = Balance::first();
        $amount = $request->amount;

        // Check if user has enough balance
        if (!$balance || $balance->current_balance < $amount) {
            return redirect()->back()
                ->with('error', 'Insufficient balance to allocate this amount.');
        }

        // Check if allocation exceeds remaining goal amount
        $remainingAmount = $goal->target_amount - $goal->current_amount;
        if ($amount > $remainingAmount) {
            return redirect()->back()
                ->with('error', 'Amount exceeds remaining goal amount of $' . number_format($remainingAmount, 2));
        }

        // Update goal and balance
        $goal->current_amount += $amount;

        // Mark as completed if target reached
        if ($goal->current_amount >= $goal->target_amount) {
            $goal->status = 'completed';
        }

        $goal->save();

        // Update balance
        $this->updateBalance(-$amount);

        return redirect()->back()
            ->with('success', 'Successfully allocated $' . number_format($amount, 2) . ' to ' . $goal->title);
    }

    public function withdraw(Request $request, Goal $goal)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        $amount = $request->amount;

        // Check if goal has enough saved amount
        if ($goal->current_amount < $amount) {
            return redirect()->back()
                ->with('error', 'Goal does not have enough saved amount.');
        }

        // Update goal
        $goal->current_amount -= $amount;
        $goal->status = 'active'; // Reactivate if was completed
        $goal->save();

        // Return amount to balance
        $this->updateBalance($amount);

        return redirect()->back()
            ->with('success', 'Successfully withdrew $' . number_format($amount, 2) . ' from ' . $goal->title);
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
