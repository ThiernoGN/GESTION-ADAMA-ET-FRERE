<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'categorie_id',
        'nom',
        'reference',
        'description',
        'prix_achat',
        'prix_vente',
        'stock_actuel',
        'stock_minimum',
        'contenance',
        'genre',
        'image',
        'actif',
        'fournisseur_id',
    ];

    protected $casts = [
        'actif'         => 'boolean',
        'prix_achat'    => 'decimal:2',
        'prix_vente'    => 'decimal:2',
        'stock_actuel'  => 'integer',
        'stock_minimum' => 'integer',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function venteLignes()
    {
        return $this->hasMany(VenteLigne::class);
    }
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }
    // ─── Scopes ──────────────────────────────────────────────

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeStockFaible($query)
    {
        return $query->whereColumn('stock_actuel', '<=', 'stock_minimum');
    }

    public function scopeEnRupture($query)
    {
        return $query->where('stock_actuel', 0);
    }

    public function scopeHomme($query)
    {
        return $query->where('genre', 'homme');
    }

    public function scopeFemme($query)
    {
        return $query->where('genre', 'femme');
    }

    public function scopeMixte($query)
    {
        return $query->where('genre', 'mixte');
    }

    // ─── Accesseurs ──────────────────────────────────────────

    public function getMargeAttribute(): float
    {
        return $this->prix_vente - $this->prix_achat;
    }

    public function getPourcentageMargeAttribute(): float
    {
        if ($this->prix_achat <= 0) return 0;
        return round(($this->marge / $this->prix_achat) * 100, 1);
    }

    public function getLabelGenreAttribute(): string
    {
        return match ($this->genre) {
            'homme' => '💙 Homme',
            'femme' => '💗 Femme',
            'mixte' => '💜 Mixte',
            default => $this->genre,
        };
    }

    public function getStatutStockAttribute(): string
    {
        if ($this->stock_actuel === 0) return 'rupture';
        if ($this->estStockFaible())   return 'faible';
        return 'ok';
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function estStockFaible(): bool
    {
        return $this->stock_actuel <= $this->stock_minimum;
    }

    public function estEnRupture(): bool
    {
        return $this->stock_actuel === 0;
    }

    public function totalVendu(): int
    {
        return $this->venteLignes->sum('quantite');
    }

    public function chiffreAffaires(): float
    {
        return $this->venteLignes->sum('sous_total');
    }
}