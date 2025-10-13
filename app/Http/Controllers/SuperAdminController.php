<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Prediction;
use App\Models\SystemSetting;
use App\Services\AIServiceFactory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuperAdminController extends Controller
{
    /**
     * Show superadmin dashboard
     */
    public function dashboard(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_predictions' => Prediction::count(),
            'admins' => User::whereIn('role', ['admin', 'superadmin'])->count(),
            'system_health' => $this->getSystemHealth(),
        ];

        $recent_activities = $this->getRecentActivities();

        return view('superadmin.dashboard', compact('stats', 'recent_activities'));
    }

    /**
     * Show system settings
     */
    public function settings(): View
    {
        $currentProvider = SystemSetting::get('ai_provider', 'gemini');
        $availableProviders = AIServiceFactory::getAvailableProviders();
        
        return view('superadmin.settings', compact('currentProvider', 'availableProviders'));
    }

    /**
     * Display admin users
     */
    public function admins(): View
    {
        $admins = User::where('role', 'admin')
            ->withCount('predictions')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Add client count and limit information for each admin
        foreach ($admins as $admin) {
            $admin->client_count = $admin->getCurrentClientCount();
            $admin->remaining_slots = $admin->getRemainingClientSlots();
        }

        return view('superadmin.admins.index', compact('admins'));
    }

    /**
     * Show admin details
     */
    public function showAdmin(User $user): View
    {
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            abort(404);
        }

        $user->load('predictions');
        return view('superadmin.admins.show', compact('user'));
    }

    /**
     * Update admin role
     */
    public function updateAdminRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,superadmin'
        ]);

        // Prevent superadmin from demoting themselves
        if ($user->id === auth()->id() && $request->role !== 'superadmin') {
            return redirect()->back()->with('error', 'You cannot demote yourself from superadmin.');
        }

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', 'Admin role updated successfully.');
    }

    /**
     * Store a new admin user
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,superadmin',
            'organization' => 'nullable|string|max:255',
            'client_limit' => 'nullable|integer|min:1'
        ]);

        // Create the new admin/superadmin user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'organization' => $request->organization,
            'client_limit' => $request->role === 'admin' ? $request->client_limit : null,
        ]);

        return redirect()->route('superadmin.admins.index')
            ->with('success', ucfirst($request->role) . ' user created successfully!');
    }

    /**
     * Update an admin user
     */
    public function updateAdmin(Request $request, User $user)
    {
        // Ensure only admin or superadmin users can be updated
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'Only admin and superadmin users can be updated.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,superadmin',
            'password' => 'nullable|string|min:8',
            'organization' => 'nullable|string|max:255',
            'client_limit' => 'nullable|integer|min:1'
        ]);

        // Store the old organization for comparison
        $oldOrganization = $user->organization;
        $newOrganization = $request->organization;

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'organization' => $newOrganization,
        ];

        // Only update client_limit if role is admin
        if ($request->role === 'admin') {
            // Validate that client limit is not below current user count
            if ($request->filled('client_limit')) {
                $currentClientCount = $user->getCurrentClientCount();
                if ($request->client_limit < $currentClientCount) {
                    return redirect()->back()
                        ->with('error', "Cannot set client limit to {$request->client_limit}. The admin currently has {$currentClientCount} clients. Client limit must be at least {$currentClientCount}.");
                }
            }
            $updateData['client_limit'] = $request->client_limit;
        } else {
            $updateData['client_limit'] = null; // Superadmins don't have client limits
        }

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        // If organization changed and the user is an admin, update all users with 'user' role in the old organization
        if ($oldOrganization !== $newOrganization && $user->role === 'admin' && !empty($oldOrganization)) {
            $affectedUsersCount = User::where('role', 'user')
                ->where('organization', $oldOrganization)
                ->update(['organization' => $newOrganization]);
            
            // Add a note about the cascading update in the success message
            if ($affectedUsersCount > 0) {
                return redirect()->route('superadmin.admins.index')
                    ->with('success', ucfirst($request->role) . ' user updated successfully! Organization updated for ' . $affectedUsersCount . ' client(s) as well.');
            }
        }

        return redirect()->route('superadmin.admins.index')
            ->with('success', ucfirst($request->role) . ' user updated successfully!');
    }

    /**
     * Delete an admin user
     */
    public function deleteAdmin(User $user)
    {
        // Ensure only admin or superadmin users can be deleted
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'Only admin and superadmin users can be deleted.');
        }

        // Prevent deletion of the current user
        if ($user->id === auth()->id()) {
            abort(403, 'You cannot delete yourself.');
        }

        // Store the organization and role before deletion for cascading updates
        $userOrganization = $user->organization;
        $userRole = $user->role;

        $user->delete();

        // If the deleted user was an admin with an organization, set organization to null for all users with 'user' role in that organization
        if ($userRole === 'admin' && !empty($userOrganization)) {
            $affectedUsersCount = User::where('role', 'user')
                ->where('organization', $userOrganization)
                ->update(['organization' => null]);
            
            // Add a note about the cascading update in the success message
            if ($affectedUsersCount > 0) {
                return redirect()->route('superadmin.admins.index')
                    ->with('success', ucfirst($userRole) . ' user deleted successfully! Organization cleared for ' . $affectedUsersCount . ' client(s) as well.');
            }
        }

        return redirect()->route('superadmin.admins.index')
            ->with('success', ucfirst($userRole) . ' user deleted successfully!');
    }

    /**
     * Display regular users
     */
    public function users(): View
    {
        $users = User::where('role', 'user')
            ->withCount('predictions')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get all organization admins for the dropdown
        $organizationAdmins = User::where('role', 'admin')
            ->whereNotNull('organization')
            ->select('id', 'name', 'organization')
            ->orderBy('organization')
            ->orderBy('name')
            ->get();

        return view('superadmin.users.index', compact('users', 'organizationAdmins'));
    }

    /**
     * Store a new regular user
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'organization' => 'nullable|string|max:255'
        ]);

        // Check client limit if organization is specified
        if (!empty($request->organization)) {
            // Find the admin for the target organization
            $targetAdmin = User::where('role', 'admin')
                ->where('organization', $request->organization)
                ->first();

            if ($targetAdmin && !$targetAdmin->canCreateMoreClients()) {
                return redirect()->back()
                    ->with('error', "Cannot assign client to '{$request->organization}'. The admin has reached their client limit ({$targetAdmin->client_limit} clients).");
            }
        }

        // Create the new regular user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user', // Force role to be regular user
            'organization' => $request->organization,
        ]);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'Client created successfully!');
    }

    /**
     * Update a regular user
     */
    public function updateUser(Request $request, User $user)
    {
        // Ensure only regular users can be updated
        if ($user->role !== 'user') {
            abort(403, 'Only regular users can be updated.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'organization' => 'nullable|string|max:255'
        ]);

        // Check client limit if organization is being changed
        if ($request->organization !== $user->organization && !empty($request->organization)) {
            // Find the admin for the target organization
            $targetAdmin = User::where('role', 'admin')
                ->where('organization', $request->organization)
                ->first();

            if ($targetAdmin && !$targetAdmin->canCreateMoreClients()) {
                return redirect()->back()
                    ->with('error', "Cannot assign client to '{$request->organization}'. The admin has reached their client limit ({$targetAdmin->client_limit} clients).");
            }
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'organization' => $request->organization,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Delete a regular user
     */
    public function deleteUser(User $user)
    {
        // Ensure only regular users can be deleted
        if ($user->role !== 'user') {
            abort(403, 'Only regular users can be deleted.');
        }

        // Delete user's predictions first (if any)
        $user->predictions()->delete();
        
        // Delete the user
        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User and all their predictions deleted successfully!');
    }

    /**
     * Show all predictions
     */
    public function predictions(): View
    {
        $predictions = Prediction::with('user')->paginate(15);
        return view('superadmin.predictions.index', compact('predictions'));
    }

    /**
     * Show prediction details
     */
    public function showPrediction(Prediction $prediction): View
    {
        $prediction->load('user');
        return view('superadmin.predictions.show', compact('prediction'));
    }

    /**
     * Show system logs
     */
    public function logs(): View
    {
        $logs = $this->getSystemLogs();
        return view('superadmin.logs', compact('logs'));
    }

    /**
     * Get system health information
     */
    private function getSystemHealth(): array
    {
        try {
            $dbConnection = DB::connection()->getPdo();
            $dbStatus = 'Connected';
        } catch (\Exception $e) {
            $dbStatus = 'Disconnected';
        }

        return [
            'database' => $dbStatus,
            'storage_writable' => is_writable(storage_path()),
            'cache_writable' => is_writable(storage_path('framework/cache')),
            'logs_writable' => is_writable(storage_path('logs')),
        ];
    }

    /**
     * Get recent system activities
     */
    private function getRecentActivities(): array
    {
        $activities = [];

        // Get recent user creations
        $recentUsers = User::latest()->take(5)->get();
        foreach ($recentUsers as $user) {
            $activities[] = [
                'type' => 'user_created',
                'description' => "New user created: {$user->name}",
                'timestamp' => $user->created_at,
                'user' => $user
            ];
        }

        // Get recent predictions
        $recentPredictions = Prediction::with('user')->latest()->take(5)->get();
        foreach ($recentPredictions as $prediction) {
            $activities[] = [
                'type' => 'prediction_created',
                'description' => "New prediction by {$prediction->user->name}",
                'timestamp' => $prediction->created_at,
                'user' => $prediction->user
            ];
        }

        // Sort by timestamp
        usort($activities, function($a, $b) {
            return $b['timestamp']->timestamp - $a['timestamp']->timestamp;
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Get system logs
     */
    private function getSystemLogs(): array
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $lines = explode("\n", $logContent);
            
            // Get last 100 lines
            $recentLines = array_slice($lines, -100);
            
            foreach ($recentLines as $line) {
                if (!empty(trim($line))) {
                    $logs[] = $line;
                }
            }
        }

        return array_reverse($logs);
    }

    /**
     * Update superadmin profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update basic info
        $user->name = $request->name;
        $user->email = $request->email;

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Set client limit for an admin user
     */
    public function setClientLimit(Request $request, User $user)
    {
        // Ensure only admin users can have client limits set
        if ($user->role !== 'admin') {
            abort(403, 'Client limits can only be set for admin users.');
        }

        $request->validate([
            'client_limit' => 'required|integer|min:1'
        ]);

        // Validate that client limit is not below current user count
        $currentClientCount = $user->getCurrentClientCount();
        if ($request->client_limit < $currentClientCount) {
            return redirect()->back()
                ->with('error', "Cannot set client limit to {$request->client_limit}. The admin currently has {$currentClientCount} clients. Client limit must be at least {$currentClientCount}.");
        }

        $user->update(['client_limit' => $request->client_limit]);

        return redirect()->back()->with('success', 'Client limit updated successfully!');
    }

    /**
     * Update AI provider setting
     */
    public function updateAIProvider(Request $request)
    {
        $request->validate([
            'ai_provider' => 'required|in:gemini,chatgpt'
        ]);

        try {
            AIServiceFactory::setProvider($request->ai_provider);
            
            return redirect()->back()->with('success', 'AI provider updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update AI provider: ' . $e->getMessage());
        }
    }

    /**
     * Test AI provider connection
     */
    public function testAIProvider(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:gemini,chatgpt'
        ]);

        try {
            // Temporarily set the provider for testing
            $originalProvider = SystemSetting::get('ai_provider', 'gemini');
            SystemSetting::set('ai_provider', $request->provider, 'AI provider for predictions');
            
            $result = AIServiceFactory::testCurrentProvider();
            
            // Restore original provider
            SystemSetting::set('ai_provider', $originalProvider, 'AI provider for predictions');
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to test AI provider: ' . $e->getMessage()
            ], 500);
        }
    }
}
