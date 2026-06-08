<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommandeFournisseur extends Model
{
    use HasFactory;

    protected $table = 'commandes_fournisseurs';

    protected $fillable = [
        'fournisseur_id',
        'user_id',
        'total',
        'statut',
        'date_livraison_prevue',
    ];

    protected $casts = [
        'total'                 => 'decimal:2',
        'date_livraison_prevue' => 'date',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lignes()
    {
        return $this->hasMany(CommandeFournisseurLigne::class);
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function estEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }

    public function estRecue(): bool
    {
        return $this->statut === 'recue';
    }

    public function estAnnulee(): bool
    {
        return $this->statut === 'annulee';
    }
}