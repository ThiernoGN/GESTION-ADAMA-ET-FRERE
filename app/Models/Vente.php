<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vente extends Model
{
    use HasFactory;

protected $fillable = [
    'numero', 'client_id', 'user_id',
    'total_ht', 'remise', 'total_ttc',
    'montant_paye', 'montant_paye_2',
    'date_paiement_1', 'date_paiement_2',
    'reste_a_payer',
    'mode_paiement', 'statut',
];

protected $casts = [
    'total_ht'         => 'decimal:2',
    'remise'           => 'decimal:2',
    'total_ttc'        => 'decimal:2',
    'montant_paye'     => 'decimal:2',
    'montant_paye_2'   => 'decimal:2',
    'reste_a_payer'    => 'decimal:2',
    'date_paiement_1'  => 'datetime',
    'date_paiement_2'  => 'datetime',
];

    // ─── Relations ───────────────────────────────────────────

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function vendeur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lignes()
    {
        return $this->hasMany(VenteLigne::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopePayee($query)
    {
        return $query->where('statut', 'payee');
    }

    public function scopeAnnulee($query)
    {
        return $query->where('statut', 'annulee');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeDuJour($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeDuMois($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    // ─── Accesseurs ──────────────────────────────────────────

    public function getLabelStatutAttribute(): string
    {
        return match ($this->statut) {
            'payee'    => 'Payée',
            'en_cours' => 'En cours',
            'annulee'  => 'Annulée',
            default    => $this->statut,
        };
    }

    public function getLabelPaiementAttribute(): string
    {
        return match ($this->mode_paiement) {
            'especes'      => 'Espèces',
            'carte'        => 'Carte bancaire',
            'mobile_money' => 'Mobile Money',
            'credit'       => 'Crédit',
            default        => $this->mode_paiement,
        };
    }

    public function getCouleurStatutAttribute(): string
    {
        return match ($this->statut) {
            'payee'    => 'green',
            'en_cours' => 'yellow',
            'annulee'  => 'red',
            default    => 'stone',
        };
    }

    public function getIconePaiementAttribute(): string
    {
        return match ($this->mode_paiement) {
            'especes'      => '💵',
            'carte'        => '💳',
            'mobile_money' => '📱',
            'credit'       => '📋',
            default        => '💰',
        };
    }

    // ─── Helpers ─────────────────────────────────────────────

    public static function genererNumero(): string
    {
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return 'VNT-' . now()->year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function estPayee(): bool
    {
        return $this->statut === 'payee';
    }

    public function estAnnulee(): bool
    {
        return $this->statut === 'annulee';
    }

    public function estEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }

    public function nombreArticles(): int
    {
        return $this->lignes->sum('quantite');
    }

    public function benefice(): float
    {
        return $this->lignes->sum(function ($ligne) {
            return ($ligne->prix_unitaire - $ligne->produit->prix_achat) * $ligne->quantite;
        });
    }
}