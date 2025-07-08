@extends('layouts.app')

@section('title', 'New Transaction - Budget Tracker')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">New Transaction</h1>
            <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-900">← Back to Transactions</a>
        </div>

        <div class="bg-white shadow-lg rounded-lg">
            <form action="{{ route('transactions.store') }}" method="POST" class="p-6">
                @csrf

                <div class="mb-6">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                    <select name="type" id="type" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select type...</option>
                        <option value="payment" {{ old('type') == 'payment' ? 'selected' : '' }}>Payment (To Pay)</option>
                        <option value="receipt" {{ old('type') == 'receipt' ? 'selected' : '' }}>Receipt (To Get)</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                    <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount') }}"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00" required>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="from_person_id" class="block text-sm font-medium text-gray-700 mb-2">From (By Whom)</label>
                    <select name="from_person_id" id="from_person_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select person...</option>
                        @foreach($people as $person)
                            <option value="{{ $person->id }}" {{ old('from_person_id') == $person->id ? 'selected' : '' }}>
                                {{ $person->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('from_person_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="to_person_id" class="block text-sm font-medium text-gray-700 mb-2">To (To Whom)</label>
                    <select name="to_person_id" id="to_person_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select person...</option>
                        @foreach($people as $person)
                            <option value="{{ $person->id }}" {{ old('to_person_id') == $person->id ? 'selected' : '' }}>
                                {{ $person->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('to_person_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                    <textarea name="description" id="description" rows="3"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Enter description...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('transactions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">Cancel</a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md font-medium">Create Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(count($people) == 0)
    <div class="max-w-2xl mx-auto mt-4">
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <p>No people found. <a href="{{ route('people.create') }}" class="underline">Add a person</a> first to create transactions.</p>
        </div>
    </div>
@endif
@endsection
