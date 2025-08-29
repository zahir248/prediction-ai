<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Store new client
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Client created successfully.');
    }

    /**
     * Update client
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Delete client
     */
    public function deleteUser(User $user)
    {
        // Delete all predictions associated with the user
        $user->predictions()->delete();
        
        // Delete the user
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Client deleted successfully.');
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        Auth::user()->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
