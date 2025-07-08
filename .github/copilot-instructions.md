<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

# Budget Tracker Laravel Project

This is a Laravel project for budget tracking with payment and receipt management.

## Project Structure

- **Models**: Person, Transaction, Balance
- **Controllers**: BalanceController, TransactionController, PersonController
- **Views**: Blade templates with Tailwind CSS styling
- **Features**: 
  - Current balance tracking
  - Payment management (to pay) with partial payment support
  - Receipt management (to get) with partial receipt support
  - Person management (by whom/to whom)
  - Auto-generation of remaining amount transactions for partial payments

## Key Features

1. **Balance Management**: Track current balance, total to pay, total to receive
2. **Transaction Types**: 
   - Payment (money to pay to someone)
   - Receipt (money to get from someone)
3. **Partial Payments**: When marking a transaction as partially paid, automatically creates a new transaction for the remaining amount
4. **Person Management**: Add/edit people involved in transactions
5. **Responsive Design**: Uses Tailwind CSS for modern, responsive UI

## Database Schema

- **people**: id, name, email, phone, address
- **transactions**: id, type (payment/receipt), amount, paid_amount, remaining_amount, status, from_person_id, to_person_id, parent_transaction_id
- **balances**: id, current_balance, total_to_pay, total_to_receive

## Usage

1. Add people first
2. Create transactions (payments or receipts)
3. Mark transactions as paid (full or partial)
4. Track overall balance and pending amounts
