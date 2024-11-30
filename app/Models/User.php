<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Transaction;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $user = Auth::user();
        $roles = $this->roles->pluck('name');

        if ($panel->getId() === 'Admin_dashboard' && $this->roles->contains('id', 1)){
            return true;
        }
        else if ($panel->getId() === 'user_dashboard' && $this->roles->contains('id', 2)){
            return true;
        }else
            return false;
    }

    protected static function booted()
    {
        static::created(function ($user) {
            if (!$user->roles->where('id', 1)) {
                // Jika user tidak memiliki role dengan ID 1, tetapkan role 'santri'
                $user->assignRole('santri');
            }
        });
    }
    
}