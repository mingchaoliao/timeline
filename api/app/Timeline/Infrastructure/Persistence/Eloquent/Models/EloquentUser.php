<?php

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class EloquentUser extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin === 1;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash()
    {
        return $this->password;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->updated_at;
    }

    public static function createNew(
        string $name,
        string $email,
        string $password,
        bool $isHashed = false,
        bool $isAdmin = false
    ): self
    {
        if (!$isHashed) {
            $password = Hash::make($password);
        }
        return static::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'is_admin' => $isAdmin ? 1 : 0
        ]);
    }
}
