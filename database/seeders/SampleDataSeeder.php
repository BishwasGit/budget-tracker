<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create initial balance
        \App\Models\Balance::create([
            'current_balance' => 1000.00,
            'total_to_pay' => 0,
            'total_to_receive' => 0
        ]);

        // Create sample people
        $people = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '555-0123',
                'address' => '123 Main St, City, State 12345'
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '555-0456',
                'address' => '456 Oak Ave, City, State 12345'
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'phone' => '555-0789',
                'address' => '789 Pine Rd, City, State 12345'
            ]
        ];

        foreach ($people as $person) {
            \App\Models\Person::create($person);
        }

        // Create sample transactions
        $transactions = [
            [
                'type' => 'payment',
                'amount' => 500.00,
                'paid_amount' => 0,
                'remaining_amount' => 500.00,
                'description' => 'Loan to John',
                'status' => 'pending',
                'from_person_id' => 1, // John
                'to_person_id' => 2, // Jane
            ],
            [
                'type' => 'receipt',
                'amount' => 200.00,
                'paid_amount' => 0,
                'remaining_amount' => 200.00,
                'description' => 'Payment for services',
                'status' => 'pending',
                'from_person_id' => 3, // Bob
                'to_person_id' => 1, // John
            ]
        ];

        foreach ($transactions as $transaction) {
            \App\Models\Transaction::create($transaction);
        }

        // Update balance totals
        $balance = \App\Models\Balance::first();
        $balance->total_to_pay = \App\Models\Transaction::where('type', 'payment')
            ->where('status', '!=', 'completed')
            ->sum('remaining_amount');
        $balance->total_to_receive = \App\Models\Transaction::where('type', 'receipt')
            ->where('status', '!=', 'completed')
            ->sum('remaining_amount');
        $balance->save();
    }
}
