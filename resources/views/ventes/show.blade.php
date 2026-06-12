@extends('layouts.app')
@section('title', 'Vente ' . $vente->numero)

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('ventes.index') }}" class="text-stone-400 hover:text-stone-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-semibold text-stone-800">{{ $vente->numero }}</h2>
                <p class="text-sm text-stone-500">{{ $vente->created_at->format('d/m/Y à H:i') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('ventes.facture', $vente) }}" target="_blank"
               class="inline-flex items-center gap-2 border border-amber-300 text-amber-700 hover:bg-amber-50 px-3 py-2 rounded-lg text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Facture PDF
            </a>
            @if($vente->statut !== 'annulee' && auth()->user()->isAdmin())
            <a href="{{ route('ventes.edit', $vente) }}"
               class="inline-flex items-center gap-2 border border-stone-200 text-stone-600 hover:bg-stone-50 px-3 py-2 rounded-lg text-sm transition">
                Modifier
            </a>
            <form method="POST" action="{{ route('ventes.annuler', $vente) }}"
                  onsubmit="return confirm('Annuler cette vente ?')">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 border border-red-200 text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg text-sm transition">
                    Annuler
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Infos vente --}}
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <h3 class="font-medium text-stone-800 mb-4">Informations</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-stone-500">Statut</dt>
                    <dd>
                        @if($vente->statut === 'payee')
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">✅ Payée</span>
                        @elseif($vente->statut === 'en_cours')
                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">⏳ En cours</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">❌ Annulée</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-stone-500">Mode paiement</dt>
                    <dd class="font-medium text-stone-800">{{ $vente->label_paiement }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-stone-500">Vendeur</dt>
                    <dd class="font-medium text-stone-800">{{ $vente->vendeur->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-stone-500">Date</dt>
                    <dd class="font-medium text-stone-800">{{ $vente->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        {{-- Client --}}
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <h3 class="font-medium text-stone-800 mb-4">Client</h3>
            @if($vente->client)
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-stone-500">Nom</dt>
                    <dd class="font-medium text-stone-800">{{ $vente->client->nom_complet }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-stone-500">Téléphone</dt>
                    <dd class="font-medium text-stone-800">{{ $vente->client->telephone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-stone-500">Points fidélité</dt>
                    <dd class="font-medium text-amber-600">⭐ {{ $vente->client->points_fidelite }} pts</dd>
                </div>
            </dl>
            @else
            <p class="text-sm text-stone-400 italic">Client passager</p>
            @endif
        </div>
    </div>

    {{-- Lignes de vente --}}
    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden mb-6">
        <div class="p-4 border-b border-stone-100">
            <h3 class="font-medium text-stone-800">Produits vendus</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-stone-50 border-b border-stone-100">
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Produit</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Qté</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Prix unit.</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Sous-total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach($vente->lignes as $ligne)
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-medium text-stone-800">{{ $ligne->produit->nom }}</p>
                        <p class="text-xs text-stone-400">{{ $ligne->produit->reference }}</p>
                    </td>
                    <td class="px-4 py-3 text-center text-stone-700">{{ $ligne->quantite }}</td>
                    <td class="px-4 py-3 text-right text-stone-700">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} GNF</td>
                    <td class="px-4 py-3 text-right font-semibold text-stone-800">{{ number_format($ligne->sous_total, 0, ',', ' ') }} GNF</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t border-stone-200 bg-stone-50">
                <tr>
                    <td colspan="3" class="px-4 py-2 text-right text-sm text-stone-500">Sous-total HT</td>
                    <td class="px-4 py-2 text-right font-medium">{{ number_format($vente->total_ht, 0, ',', ' ') }} GNF</td>
                </tr>
                @if($vente->remise > 0)
                <tr>
                    <td colspan="3" class="px-4 py-2 text-right text-sm text-green-600">Remise</td>
                    <td class="px-4 py-2 text-right text-green-600 font-medium">-{{ number_format($vente->remise, 0, ',', ' ') }} GNF</td>
                </tr>
                @endif
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right font-bold text-stone-800">TOTAL TTC</td>
                    <td class="px-4 py-3 text-right font-bold text-amber-600 text-lg">{{ number_format($vente->total_ttc, 0, ',', ' ') }} GNF</td>
                </tr>
            </tfoot>
        </table>
    </div>
    {{-- Bloc paiement & solde --}}
<div class="bg-white border border-stone-200 rounded-xl p-5">
    <h3 class="font-medium text-stone-800 mb-4">💰 Paiements</h3>

    <dl class="space-y-3 text-sm">

        {{-- 1er paiement --}}
        <div class="flex justify-between items-center py-2 border-b border-stone-100">
            <dt class="text-stone-500">
                1er paiement
                <span class="text-xs text-stone-400 block">
                    {{ $vente->date_paiement_1
                        ? $vente->date_paiement_1->format('d/m/Y H:i')
                        : $vente->created_at->format('d/m/Y H:i') }}
                </span>
            </dt>
            <dd class="font-semibold text-green-600">
                {{ number_format($vente->montant_paye ?? 0, 0, ',', ' ') }} GNF
            </dd>
        </div>

        {{-- 2ème paiement si existe --}}
        @if($vente->montant_paye_2 > 0)
        <div class="flex justify-between items-center py-2 border-b border-stone-100">
            <dt class="text-stone-500">
                2ème paiement
                <span class="text-xs text-stone-400 block">
                    {{ $vente->date_paiement_2?->format('d/m/Y H:i') ?? '—' }}
                </span>
            </dt>
            <dd class="font-semibold text-green-600">
                {{ number_format($vente->montant_paye_2, 0, ',', ' ') }} GNF
            </dd>
        </div>
        @endif

        {{-- Total payé --}}
        <div class="flex justify-between items-center py-2 border-b border-stone-100">
            <dt class="font-medium text-stone-700">Total payé</dt>
            <dd class="font-bold text-green-600">
                {{ number_format(($vente->montant_paye ?? 0) + ($vente->montant_paye_2 ?? 0), 0, ',', ' ') }} GNF
            </dd>
        </div>

        {{-- Reste à payer --}}
        <div class="flex justify-between items-center py-2">
            <dt class="font-medium {{ $vente->reste_a_payer > 0 ? 'text-red-700' : 'text-green-700' }}">
                Reste à payer
            </dt>
            <dd class="font-bold text-lg {{ $vente->reste_a_payer > 0 ? 'text-red-600' : 'text-green-600' }}">
                {{ $vente->reste_a_payer > 0
                    ? number_format($vente->reste_a_payer, 0, ',', ' ') . ' GNF'
                    : '✅ Soldé' }}
            </dd>
        </div>

    </dl>

    {{-- Formulaire solde --}}
    @if($vente->reste_a_payer > 0 && $vente->statut !== 'annulee' && !$vente->montant_paye_2)
    <div class="mt-4 pt-4 border-t border-stone-100">
        <p class="text-xs text-stone-500 mb-3">💳 Enregistrer un 2ème paiement</p>
        <form method="POST" action="{{ route('ventes.solde', $vente) }}"
              class="flex gap-2 items-center">
            @csrf
            <input type="number" name="montant_paye_2"
                   min="1" max="{{ $vente->reste_a_payer }}"
                   placeholder="Montant (max {{ number_format($vente->reste_a_payer, 0, ',', ' ') }} GNF)"
                   required
                   class="flex-1 border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
                ✅ Encaisser
            </button>
        </form>
    </div>
    @endif

</div>
</div>
@endsection
