<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VenteLigne extends Model
{
    use HasFactory;

    protected $table = 'vente_lignes';

    protected $fillable = [
        'vente_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'sous_total',
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'sous_total'    => 'decimal:2',
        'quantite'      => 'integer',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function benefice(): float
    {
        return ($this->prix_unitaire - $this->produit->prix_achat) * $this->quantite;
    }
}