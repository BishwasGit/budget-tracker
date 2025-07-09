<!-- filepath: c:\xampp\htdocs\copilot-budget-project\resources\views\goals\edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Goal - Budget Tracker')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Edit Goal</h1>
                <p class="text-gray-600 mt-2">Update your savings goal details</p>
            </div>

            <form action="{{ route('goals.update', $goal) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Goal Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $goal->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $goal->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="target_amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Target Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                            <input type="number" id="target_amount" name="target_amount" step="0.01" min="0.01"
                                   value="{{ old('target_amount', $goal->target_amount) }}"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>
                        @error('target_amount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">Deadline (Optional)</label>
                        <input type="date" id="deadline" name="deadline"
                               value="{{ old('deadline', $goal->deadline ? $goal->deadline->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('deadline')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="active" {{ old('status', $goal->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="paused" {{ old('status', $goal->status) == 'paused' ? 'selected' : '' }}>Paused</option>
                        <option value="completed" {{ old('status', $goal->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                    <h3 class="text-sm font-medium text-gray-800 mb-2">Current Progress</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Current Amount:</span>
                            <span class="font-medium">${{ number_format($goal->current_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Progress:</span>
                            <span class="font-medium">{{ number_format($goal->progress_percentage, 1) }}%</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <div class="flex space-x-2">
                        <a href="{{ route('goals.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                                    onclick="return confirm('Are you sure you want to delete this goal?')">
                                Delete Goal
                            </button>
                        </form>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Update Goal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
