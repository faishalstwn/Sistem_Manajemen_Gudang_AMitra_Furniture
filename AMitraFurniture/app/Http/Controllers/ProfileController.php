<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Get order statistics
        $ordersYesterday = Order::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::yesterday())
            ->with('orderItems.product')
            ->latest()
            ->get();
            
        $ordersPaid = Order::where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->with('orderItems.product')
            ->latest()
            ->get();
            
        $ordersUnpaid = Order::where('user_id', $user->id)
            ->where('payment_status', 'pending')
            ->with('orderItems.product')
            ->latest()
            ->get();
            
        $ordersProcessing = Order::where('user_id', $user->id)
            ->where('status', 'processing')
            ->with('orderItems.product')
            ->latest()
            ->get();
            
        $ordersShipping = Order::where('user_id', $user->id)
            ->where('status', 'shipped')
            ->with('orderItems.product')
            ->latest()
            ->get();
        
        return view('dashboard.profile', compact(
            'user', 
            'ordersYesterday', 
            'ordersPaid', 
            'ordersUnpaid', 
            'ordersProcessing', 
            'ordersShipping'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500'
        ]);

        Auth::user()->update($validated);
        return redirect()->back()->with('success', 'Profil berhasil diperbarui');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password lama tidak sesuai']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah');
    }
}