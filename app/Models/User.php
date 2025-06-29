<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Modules\UserManagement\Models\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
                    'error' => $e->getMessage()
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
                    'error' => $e->getMessage()
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
        ];
    }

    /**
     * Get the role that owns the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
