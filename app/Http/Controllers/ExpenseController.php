<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('user')->orderBy('expense_date', 'desc');
        
        if ($request->filled('start_date')) {
            $query->whereDate('expense_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('expense_date', '<=', $request->end_date);
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        $expenses = $query->paginate(20);
        
        $totalExpenses = Expense::sum('amount');
        $todayExpenses = Expense::whereDate('expense_date', now()->toDateString())->sum('amount');
        $monthlyExpenses = Expense::whereMonth('expense_date', now()->month)->sum('amount');
        
        return view('expenses.index', compact('expenses', 'totalExpenses', 'todayExpenses', 'monthlyExpenses'));
    }

    public function create()
    {
        $categories = [
            'Rent' => '🏢 Rent',
            'Utilities' => '💡 Utilities',
            'Salaries' => '👥 Salaries',
            'Transport' => '🚗 Transport',
            'Maintenance' => '🔧 Maintenance',
            'Marketing' => '📢 Marketing',
            'Office Supplies' => '📎 Office Supplies',
            'Taxes' => '📑 Taxes',
            'Insurance' => '🛡️ Insurance',
            'Other' => '📝 Other'
        ];
        
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,mobile_money,bank,credit',
            'notes' => 'nullable|string',
        ]);
        
        $invoiceNumber = 'EXP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        Expense::create([
            'invoice_number' => $invoiceNumber,
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'user_id' => auth()->id(),
        ]);
        
        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully! Invoice: ' . $invoiceNumber);
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = [
            'Rent' => '🏢 Rent',
            'Utilities' => '💡 Utilities',
            'Salaries' => '👥 Salaries',
            'Transport' => '🚗 Transport',
            'Maintenance' => '🔧 Maintenance',
            'Marketing' => '📢 Marketing',
            'Office Supplies' => '📎 Office Supplies',
            'Taxes' => '📑 Taxes',
            'Insurance' => '🛡️ Insurance',
            'Other' => '📝 Other'
        ];
        
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,mobile_money,bank,credit',
            'notes' => 'nullable|string',
        ]);
        
        $expense->update($request->all());
        
        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        
        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully!');
    }
}
