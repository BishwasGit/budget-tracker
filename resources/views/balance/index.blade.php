@extends('layouts.app')

@section('title', 'Dashboard - Budget Tracker')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Dashboard</h1>
    <div class="flex flex-row gap-6 mb-8">
        <div class="flex-1 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-2">Balance You can Use</h2>
            <p class="text-3xl font-bold text-blue-600">${{ number_format($balance->current_balance, 2) }}</p>
        </div>
        <div class="flex-1 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-2">Total to Pay</h2>
            <p class="text-3xl font-bold text-red-600">${{ number_format($balance->total_to_pay, 2) }}</p>
        </div>
        <div class="flex-1 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-2">Total to Receive</h2>
            <p class="text-3xl font-bold text-yellow-600">${{ number_format($balance->total_to_receive, 2) }}</p>
        </div>
        <div class="flex-1 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-2">Total Expenses</h2>
            <p class="text-3xl font-bold text-orange-600">${{ number_format($totalExpenses, 2) }}</p>
        </div>
        {{-- <div class="flex-1 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-2">Total Reserved</h2>
            <p class="text-3xl font-bold text-green-600">${{ number_format($reservedAmount, 2) }}</p>
        </div> --}}
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Add Balance</h2>
        <form action="{{ route('balance.add') }}" method="POST" class="flex items-center space-x-4">
            @csrf
            <input type="number" name="amount" step="0.01" min="0" placeholder="Amount"
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   required>
            <button type="submit" class="inline-block bg-blue-500 text-white px-6 py-2 rounded shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition">
                Add Balance
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Recent Transactions</h2>
                <a href="{{ route('transactions.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</a>
            </div>

            @if($recentTransactions->count() > 0)
                <div class="space-y-3">
                    @foreach($recentTransactions as $transaction)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ ucfirst($transaction->type) }}</p>
                            <p class="text-sm text-gray-500">
                                @if($transaction->type == 'payment')
                                    To: {{ $transaction->toPerson->name }}
                                @else
                                    From: {{ $transaction->fromPerson->name }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">${{ number_format($transaction->amount, 2) }}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($transaction->status == 'completed') bg-green-100 text-green-800
                                @elseif($transaction->status == 'partial') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No recent transactions.</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Recent Expenses</h2>
                <a href="{{ route('expenses.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</a>
            </div>

            @if($recentExpenses->count() > 0)
                <div class="space-y-3">
                    @foreach($recentExpenses as $expense)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $expense->title }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $expense->category ?? 'Uncategorized' }} • {{ $expense->date->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-red-600">${{ number_format($expense->amount, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No recent expenses.</p>
            @endif
        </div>
    </div>
</div>
@endsection
