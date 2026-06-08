<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommandeFournisseurLigne extends Model
{
    use HasFactory;

    protected $table = 'commande_fournisseur_lignes';

    protected $fillable = [
        'commande_fournisseur_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
    ];

    protected $casts = [
        'quantite'      => 'integer',
        'prix_unitaire' => 'decimal:2',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function commande()
    {
        return $this->belongsTo(CommandeFournisseur::class, 'commande_fournisseur_id');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function sousTotal(): float
    {
        return $this->quantite * $this->prix_unitaire;
    }
}