<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vente extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'client_id',
        'user_id',
        'total_ht',
        'remise',
        'total_ttc',
        'mode_paiement',
        'statut',
    ];

    protected $casts = [
        'total_ht'  => 'decimal:2',
        'remise'    => 'decimal:2',
        'total_ttc' => 'decimal:2',
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

    // ─── Helpers ─────────────────────────────────────────────
    public static function genererNumero(): string
    {
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return 'VNT-' . now()->year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

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
}
