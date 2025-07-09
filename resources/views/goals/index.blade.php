<!-- filepath: c:\xampp\htdocs\copilot-budget-project\resources\views\goals\index.blade.php -->
@extends('layouts.app')

@section('title', 'Goals & Saving Buckets - Budget Tracker')

@section('content')
<div class="px-4 sm:px-6 lg:px-8" x-data="{ showAllocateModal: false, showWithdrawModal: false, selectedGoal: null }">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Goals & Saving Buckets</h1>
            <p class="text-gray-600 mt-2">Set savings goals and track your progress</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('goals.create') }}" class="inline-block bg-blue-500 text-white px-6 py-2 rounded shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition">
                Create New Goal
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-2">Available Balance</h2>
            <p class="text-3xl font-bold text-blue-600">${{ number_format($balance->current_balance ?? 0, 2) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-2">Total Goal Amount</h2>
            <p class="text-3xl font-bold text-purple-600">${{ number_format($totalGoalsAmount, 2) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-2">Total Saved</h2>
            <p class="text-3xl font-bold text-green-600">${{ number_format($totalSavedAmount, 2) }}</p>
        </div>
    </div>

    @if($goals->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($goals as $goal)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $goal->title }}</h3>
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            @if($goal->status == 'completed') bg-green-100 text-green-800
                            @elseif($goal->status == 'paused') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($goal->status) }}
                        </span>
                    </div>

                    @if($goal->description)
                        <p class="text-gray-600 text-sm mb-4">{{ $goal->description }}</p>
                    @endif

                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Progress</span>
                            <span class="font-medium">{{ number_format($goal->progress_percentage, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full transition-all duration-300"
                                 style="width: {{ $goal->progress_percentage }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Target:</span>
                            <span class="font-medium">${{ number_format($goal->target_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Saved:</span>
                            <span class="font-medium text-green-600">${{ number_format($goal->current_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Remaining:</span>
                            <span class="font-medium text-red-600">${{ number_format($goal->remaining_amount, 2) }}</span>
                        </div>
                        @if($goal->deadline)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Deadline:</span>
                            <span class="font-medium">{{ $goal->deadline->format('M d, Y') }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="space-y-2">
                        @if($goal->status != 'completed')
                            <button @click="selectedGoal = {{ $goal->id }}; showAllocateModal = true"
                                    class="inline-block bg-blue-500 text-white px-6 py-2 rounded shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition">
                                Allocate Funds
                            </button>
                        @endif

                        @if($goal->current_amount > 0)
                            <button @click="selectedGoal = {{ $goal->id }}; showWithdrawModal = true"
                                    class="inline-block bg-blue-500 text-white px-6 py-2 rounded shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition">
                                Withdraw Funds
                            </button>
                        @endif

                        <a href="{{ route('goals.edit', $goal) }}"
                           class="inline-block bg-blue-500 text-white px-6 py-2 rounded shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition">
                            Edit Goal
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-gray-500 mb-4">No goals found. Start by creating your first savings goal!</p>
            <a href="{{ route('goals.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Create Your First Goal
            </a>
        </div>
    @endif

    <!-- Allocate Modal -->
    <div x-show="showAllocateModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Allocate Funds to Goal</h3>
                <form :action="'/goals/' + selectedGoal + '/allocate'" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount to Allocate</label>
                        <div class="relative">
                            {{-- <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span> --}}
                            <input type="number" name="amount" step="0.01" min="0.01"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="showAllocateModal = false"
                                class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="inline-block bg-green-500 text-white px-6 py-2 rounded shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition">
                            Allocate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Withdraw Modal -->
    <div x-show="showWithdrawModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Withdraw Funds from Goal</h3>
                <form :action="'/goals/' + selectedGoal + '/withdraw'" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount to Withdraw</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                            <input type="number" name="amount" step="0.01" min="0.01"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="showWithdrawModal = false"
                                class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                            Withdraw
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
