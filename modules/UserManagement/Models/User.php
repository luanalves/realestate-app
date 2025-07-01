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
     * Bootstrap the model and its traits.
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
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
     * Get the role that owns the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Validate user credentials for Laravel Passport password grant.
     * This method is called by Laravel Passport BEFORE creating the access token.
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
