<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'actif'];
    protected $hidden   = ['password', 'remember_token'];

    public function isAdmin():   bool { return $this->role === 'admin'; }
    public function isVendeur(): bool { return $this->role === 'vendeur'; }
    public function isCaissier(): bool { return $this->role === 'caissier'; }

    public function ventes() { return $this->hasMany(Vente::class); }
}