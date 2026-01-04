<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Get all bank accounts.
     */
    public function index(): JsonResponse
    {
        $bankAccounts = BankAccount::orderBy('created_at', 'desc')->get();

        return response()->json([
            'bank_accounts' => $bankAccounts,
        ]);
    }

    /**
     * Store a new bank account.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:255'],
            'branch_name' => ['required', 'string', 'max:255'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:50'],
            'routing_number' => ['nullable', 'string', 'max:50'],
            'swift_code' => ['nullable', 'string', 'max:20'],
            'currency' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $bankAccount = BankAccount::create($validated);

        return response()->json([
            'message' => 'Bank account created successfully',
            'bank_account' => $bankAccount,
        ], 201);
    }

    /**
     * Update a bank account.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $bankAccount = BankAccount::findOrFail($id);

        $validated = $request->validate([
            'bank_name' => ['sometimes', 'string', 'max:255'],
            'branch_name' => ['sometimes', 'string', 'max:255'],
            'account_name' => ['sometimes', 'string', 'max:255'],
            'account_number' => ['sometimes', 'string', 'max:50'],
            'routing_number' => ['nullable', 'string', 'max:50'],
            'swift_code' => ['nullable', 'string', 'max:20'],
            'currency' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $bankAccount->update($validated);

        return response()->json([
            'message' => 'Bank account updated successfully',
            'bank_account' => $bankAccount,
        ]);
    }

    /**
     * Delete a bank account.
     */
    public function destroy(int $id): JsonResponse
    {
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->delete();

        return response()->json([
            'message' => 'Bank account deleted successfully',
        ]);
    }

    /**
     * Toggle bank account active status.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->update([
            'is_active' => !$bankAccount->is_active,
        ]);

        return response()->json([
            'message' => 'Bank account status updated successfully',
            'bank_account' => $bankAccount,
        ]);
    }
}
