<?php

namespace Modules\UserManagement\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * User status constants.
     */
    public const STATUS_ACTIVE = true;
    public const STATUS_INACTIVE = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'preferences',
        'tenant_id',
        'is_active',
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
     * Registers model event listeners to invalidate the user cache when a user is updated or deleted.
     */
    protected static function booted(): void
    {
        // Invalidate cache when user is updated
        static::updated(function (User $user) {
            try {
                $userService = app(\Modules\UserManagement\Services\UserService::class);
                $userService->invalidateUserCache($user->id);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to invalidate user cache on update', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        // Invalidate cache when user is deleted
        static::deleted(function (User $user) {
            try {
                $userService = app(\Modules\UserManagement\Services\UserService::class);
                $userService->invalidateUserCache($user->id);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to invalidate user cache on delete', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }

    /**
     * Returns the attribute casting definitions for the user model.
     *
     * Specifies how certain attributes should be automatically converted to and from native types.
     *
     * @return array<string, string> An associative array mapping attribute names to their cast types.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'json',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Returns the role associated with the user.
     *
     * @return BelongsTo The relationship instance linking the user to a role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Validates the user's credentials for Laravel Passport's password grant.
     *
     * Denies authentication and logs a warning if the user is inactive; otherwise, verifies the provided password against the stored hash.
     *
     * @param string $password The plaintext password to validate.
     * @return bool True if the user is active and the password is correct; false otherwise.
     */
    public function validateForPassportPasswordGrant(string $password): bool
    {
        // First check if user is active using constants
        if (!$this->is_active || $this->is_active === self::STATUS_INACTIVE) {
            \Illuminate\Support\Facades\Log::warning('OAuth login attempt by inactive user', [
                'user_id' => $this->id,
                'email' => $this->email,
                'user_status' => $this->is_active ? 'active' : 'inactive',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toISOString(),
            ]);

            return false; // Token will NOT be created
        }

        // If user is active, validate password normally
        return Hash::check($password, $this->password);
    }
}
