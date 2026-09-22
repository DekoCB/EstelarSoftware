<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'cargo', 'email', 'password', 'activo',
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
            'password'          => 'hashed',
            'activo'            => 'boolean',
        ];
    }

    public function rolLabel(): string
    {
        return $this->roles->first()?->name ?? 'sin rol';
    }

    public function rolBadgeClass(): string
    {
        return match ($this->roles->first()?->name) {
            'super-admin'    => 'bg-violet-500/15 text-violet-400',
            'administrativo' => 'bg-sky-500/15 text-sky-400',
            'ventas'         => 'bg-emerald-500/15 text-emerald-400',
            'desarrollador'  => 'bg-amber-500/15 text-amber-400',
            'practicante'    => 'bg-slate-500/15 text-slate-400',
            default          => 'bg-slate-700 text-slate-500',
        };
    }

    public function responsibleProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'responsible_user_id');
    }

    public function payrollPayments(): HasMany
    {
        return $this->hasMany(\App\Models\PayrollPayment::class);
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    public function asistenciasHoy(): HasMany
    {
        return $this->hasMany(Asistencia::class)->whereDate('fecha', today())->orderBy('hora');
    }
}
