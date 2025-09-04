<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'organization',
        'client_limit',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function predictions()
    {
        return $this->hasMany(Prediction::class);
    }

    /**
     * Get messages sent by this user
     */
    public function sentMessages()
    {
        return $this->hasMany(\App\Models\Message::class, 'sender_id');
    }

    /**
     * Get messages received by this user
     */
    public function receivedMessages()
    {
        return $this->hasMany(\App\Models\Message::class, 'recipient_id');
    }

    /**
     * Get unread messages count for this user
     */
    public function getUnreadMessagesCount(): int
    {
        return $this->receivedMessages()->unread()->count();
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a regular user (client)
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user is a superadmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Track user login time
     */
    public function trackLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get formatted role with organization
     */
    public function getRoleWithOrganizationAttribute(): string
    {
        if ($this->organization) {
            return ucfirst($this->role) . ' of ' . $this->organization;
        }
        
        return ucfirst($this->role);
    }

    /**
     * Get display name for user (role with organization)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->getRoleWithOrganizationAttribute();
    }

    /**
     * Check if admin can create more clients
     */
    public function canCreateMoreClients(): bool
    {
        if (!$this->isAdmin()) {
            return false;
        }

        // If no limit is set, allow unlimited clients
        if (is_null($this->client_limit)) {
            return true;
        }

        $currentClientCount = User::where('role', 'user')
            ->where('organization', $this->organization)
            ->count();

        return $currentClientCount < $this->client_limit;
    }

    /**
     * Get remaining client slots for admin
     */
    public function getRemainingClientSlots(): int
    {
        if (!$this->isAdmin()) {
            return 0;
        }

        // If no limit is set, return -1 to indicate unlimited
        if (is_null($this->client_limit)) {
            return -1;
        }

        $currentClientCount = User::where('role', 'user')
            ->where('organization', $this->organization)
            ->count();

        return max(0, $this->client_limit - $currentClientCount);
    }

    /**
     * Get current client count for admin
     */
    public function getCurrentClientCount(): int
    {
        if (!$this->isAdmin()) {
            return 0;
        }

        return User::where('role', 'user')
            ->where('organization', $this->organization)
            ->count();
    }
}
