<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard(): View
    {
        $stats = [
            'total_clients' => User::where('role', 'user')->count(),
            'total_predictions' => Prediction::count(),
            'recent_clients' => User::where('role', 'user')->latest()->take(5)->get(),
            'recent_predictions' => Prediction::with('user')->latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Show all clients
     */
    public function users(): View
    {
        $users = User::where('role', 'user')->with('predictions')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show client details
     */
    public function showUser(User $user): View
    {
        $user->load('predictions');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show all predictions
     */
    public function predictions(): View
    {
        $predictions = Prediction::whereHas('user', function($query) {
            $query->where('role', 'user');
        })->with('user')->paginate(15);
        
        return view('admin.predictions.index', compact('predictions'));
    }

    /**
     * Show prediction details
     */
    public function showPrediction(Prediction $prediction): View
    {
        $prediction->load('user');
        return view('admin.predictions.show', compact('prediction'));
    }

    /**
     * Update client role
     */
    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin'
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', 'Client role updated successfully.');
    }
}
