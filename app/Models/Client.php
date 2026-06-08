<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email',
        'adresse',
        'points_fidelite',
    ];

    protected $casts = [
        'points_fidelite' => 'integer',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeFidele($query)
    {
        return $query->where('points_fidelite', '>', 0);
    }

    // ─── Accesseurs ──────────────────────────────────────────

    public function getNomCompletAttribute(): string
    {
        return $this->nom . ' ' . $this->prenom;
    }

    public function getTotalAchatsAttribute(): float
    {
        return $this->ventes()->where('statut', 'payee')->sum('total_ttc');
    }

    public function getNombreVentesAttribute(): int
    {
        return $this->ventes()->where('statut', 'payee')->count();
    }

    public function getDerniereVisiteAttribute()
    {
        return $this->ventes()->latest()->first()?->created_at;
    }
}
