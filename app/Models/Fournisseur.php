<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fournisseur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'telephone',
        'email',
        'adresse',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function commandes()
    {
        return $this->hasMany(CommandeFournisseur::class);
    }

    // ─── Accesseurs ──────────────────────────────────────────

    public function getTotalCommandesAttribute(): float
    {
        return $this->commandes()->sum('total');
    }

    public function getNombreCommandesAttribute(): int
    {
        return $this->commandes()->count();
    }

    public function getDerniereCommandeAttribute()
    {
        return $this->commandes()->latest()->first()?->created_at;
    }
}
