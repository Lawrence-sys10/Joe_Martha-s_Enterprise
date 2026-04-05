<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class PasswordController extends Controller
{
    /**
     * Show the form to change password for the current user.
     */
    public function showChangeForm()
    {
        return view('auth.passwords.change');
    }

    /**
     * Update the current user's password.
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password changed successfully!');
    }

    /**
     * Show the form to reset passwords for other users (Admin only).
     */
    public function showResetUsers()
    {
        // Check permission manually
        if (!auth()->user()->can('reset passwords')) {
            abort(403, 'Unauthorized action. You do not have permission to reset passwords.');
        }
        
        $users = User::all();
        return view('auth.passwords.reset-users', compact('users'));
    }

    /**
     * Reset another user's password (Admin only).
     */
    public function resetUserPassword(Request $request, User $user)
    {
        // Check permission manually
        if (!auth()->user()->can('reset passwords')) {
            abort(403, 'Unauthorized action. You do not have permission to reset passwords.');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('password.reset-users')
            ->with('success', 'Password reset successfully for ' . $user->name);
    }
}