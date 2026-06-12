@extends('layouts.app')
@section('title', 'Fiche — ' . $client->nom_complet)

@section('content')
<div class="p-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('fichier-client.index') }}" class="text-stone-400 hover:text-stone-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-xl
                    {{ $resume['solde_reel'] > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                    {{ strtoupper(substr($client->nom, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-semibold text-stone-800">{{ $client->nom_complet }}</h2>
                    <p class="text-sm text-stone-500">
                        {{ $client->telephone ?? '' }}
                        @if($client->email) · {{ $client->email }} @endif
                    </p>
                </div>
            </div>
        </div>
        <a href="{{ route('ventes.create') }}?client_id={{ $client->id }}"
           class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            🛒 Nouvelle vente
        </a>
    </div>

    {{-- Résumé financier --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Total Achats</p>
            <p class="text-2xl font-bold text-stone-800 mt-1">
                {{ number_format($resume['total_achats'], 0, ',', ' ') }} GNF
            </p>
            <p class="text-xs text-stone-400 mt-1">{{ $resume['nb_ventes'] }} vente(s)</p>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-xl p-5">
            <p class="text-xs text-green-600 uppercase tracking-wide">Total Payé</p>
            <p class="text-2xl font-bold text-green-600 mt-1">
                {{ number_format($resume['total_paye'], 0, ',', ' ') }} GNF
            </p>
            <p class="text-xs text-green-500 mt-1">Montants encaissés</p>
        </div>

        {{-- SOLDE RÉEL --}}
        <div class="border rounded-xl p-5 col-span-2
            {{ $resume['solde_reel'] > 0 ? 'bg-red-50 border-red-300' : 'bg-green-50 border-green-300' }}">
            <p class="text-xs uppercase tracking-wide font-semibold
                {{ $resume['solde_reel'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                💰 Solde Réel (Total TTC − Total Payé)
            </p>
            <p class="text-3xl font-bold mt-2
                {{ $resume['solde_reel'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                {{ $resume['solde_reel'] > 0 ? '' : '✅ ' }}
                {{ number_format($resume['solde_reel'], 0, ',', ' ') }} GNF
            </p>
            <p class="text-xs mt-2 {{ $resume['solde_reel'] > 0 ? 'text-red-500' : 'text-green-500' }}">
                {{ $resume['solde_reel'] > 0
                    ? "⚠️ Le client doit encore {$resume['nb_avec_solde']} paiement(s)"
                    : 'Client à jour — aucun impayé' }}
            </p>
        </div>

    </div>

    {{-- Filtres ventes --}}
    <div class="bg-white border border-stone-200 rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('fichier-client.show', $client) }}"
              class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-stone-500 mb-1">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                       class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs text-stone-500 mb-1">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                       class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs text-stone-500 mb-1">Paiement</label>
                <select name="paiement"
                        class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Tous</option>
                    <option value="solde"   {{ request('paiement') === 'solde'   ? 'selected' : '' }}>Avec solde</option>
                    <option value="complet" {{ request('paiement') === 'complet' ? 'selected' : '' }}>Payé complet</option>
                </select>
            </div>
            <button type="submit"
                    class="bg-stone-800 hover:bg-stone-900 text-white px-4 py-2 rounded-lg text-sm transition">
                Filtrer
            </button>
            @if(request()->hasAny(['date_debut','date_fin','paiement']))
            <a href="{{ route('fichier-client.show', $client) }}"
               class="border border-stone-200 text-stone-600 hover:bg-stone-50 px-4 py-2 rounded-lg text-sm transition">
                Réinitialiser
            </a>
            @endif
        </form>
    </div>

    {{-- Tableau des ventes --}}
    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-stone-100 flex justify-between items-center">
            <h3 class="font-medium text-stone-800">
                Historique des ventes
                <span class="text-xs font-normal text-stone-400 ml-2">({{ $ventes->count() }} ventes)</span>
            </h3>
        </div>

        @if($ventes->isEmpty())
        <div class="p-8 text-center text-stone-400">
            <p class="text-3xl mb-2">📂</p>
            <p class="text-sm">Aucune vente trouvée</p>
        </div>
        @else
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-stone-50 border-b border-stone-100">
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">N° Vente</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Date</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Produits</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Total TTC</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Montant Payé</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Reste à Payer</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Statut</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach($ventes as $vente)
                <tr class="hover:bg-stone-50 transition
                    {{ $vente->reste_a_payer > 0 ? 'bg-red-50/40' : '' }}">

                    <td class="px-4 py-3">
                        <a href="{{ route('ventes.show', $vente) }}"
                           class="font-medium text-amber-600 hover:underline">
                            {{ $vente->numero }}
                        </a>
                    </td>

                    <td class="px-4 py-3 text-stone-500 text-xs">
                        {{ $vente->created_at->format('d/m/Y') }}<br>
                        <span class="text-stone-400">{{ $vente->created_at->format('H:i') }}</span>
                    </td>

                    <td class="px-4 py-3 text-stone-600 text-xs">
                        @foreach($vente->lignes->take(2) as $ligne)
                            <div>{{ $ligne->produit->nom }} ×{{ $ligne->quantite }}</div>
                        @endforeach
                        @if($vente->lignes->count() > 2)
                            <div class="text-stone-400">+{{ $vente->lignes->count() - 2 }} autre(s)</div>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-right font-semibold text-stone-800">
                        {{ number_format($vente->total_ttc, 0, ',', ' ') }} GNF
                    </td>

                    <td class="px-4 py-3 text-right text-green-600 font-medium">
                        {{ number_format($vente->montant_paye ?? 0, 0, ',', ' ') }} GNF
                    </td>

                    <td class="px-4 py-3 text-right font-bold">
                        @php $reste = $vente->reste_a_payer ?? 0; @endphp
                        @if($reste > 0)
                            <span class="text-red-600">{{ number_format($reste, 0, ',', ' ') }} GNF</span>
                        @else
                            <span class="text-green-600">✅ 0 GNF</span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-center">
                        @if($vente->statut === 'payee')
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">✅ Payée</span>
                        @elseif($vente->statut === 'en_cours')
                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">⏳ En cours</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">❌ Annulée</span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('ventes.show', $vente) }}"
                               class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-500 transition" title="Voir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('ventes.facture', $vente) }}" target="_blank"
                               class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-600 transition" title="Facture">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </a>
                            @if($vente->statut !== 'annulee')
                            <a href="{{ route('ventes.edit', $vente) }}"
                               class="p-1.5 rounded-lg hover:bg-stone-100 text-stone-500 transition" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>

            {{-- Totaux --}}
            <tfoot class="border-t-2 border-stone-200 bg-stone-50">
                <tr>
                    <td colspan="3" class="px-4 py-3 font-bold text-stone-800">TOTAL</td>
                    <td class="px-4 py-3 text-right font-bold text-stone-800">
                        {{ number_format($resume['total_achats'], 0, ',', ' ') }} GNF
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-green-600">
                        {{ number_format($resume['total_paye'], 0, ',', ' ') }} GNF
                    </td>
                    <td class="px-4 py-3 text-right font-bold {{ $resume['solde_reel'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ number_format($resume['solde_reel'], 0, ',', ' ') }} GNF
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
        @endif
    </div>

</div>
@endsection
