@extends('layouts.app')
@section('title', 'Ventes')

@section('content')
<div class="p-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-stone-800">Ventes</h2>
            <p class="text-sm text-stone-500 mt-1">Liste de toutes les ventes enregistrées</p>
        </div>
        <a href="{{ route('ventes.create') }}"
           class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-black px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle vente
        </a>
    </div>

    {{-- Filtres --}}
    <div class="bg-white border border-stone-200 rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('ventes.index') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="N° vente, client, téléphone..."
                   class="border border-stone-200 rounded-lg px-3 py-2 text-sm flex-1 min-w-48 focus:outline-none focus:ring-2 focus:ring-amber-500">

            <select name="statut" class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">Tous les statuts</option>
                <option value="payee"    {{ request('statut') === 'payee'    ? 'selected' : '' }}>Payée</option>
                <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                <option value="annulee"  {{ request('statut') === 'annulee'  ? 'selected' : '' }}>Annulée</option>
            </select>

            <select name="mode_paiement" class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">Tous paiements</option>
                <option value="especes"      {{ request('mode_paiement') === 'especes'      ? 'selected' : '' }}>Espèces</option>
                <option value="carte"        {{ request('mode_paiement') === 'carte'        ? 'selected' : '' }}>Carte</option>
                <option value="mobile_money" {{ request('mode_paiement') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                <option value="credit"       {{ request('mode_paiement') === 'credit'       ? 'selected' : '' }}>Crédit</option>
            </select>

            <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                   class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                   class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">

            <button type="submit" class="bg-stone-800 hover:bg-stone-900 text-white px-4 py-2 rounded-lg text-sm transition">
                Filtrer
            </button>
            @if(request()->hasAny(['search','statut','mode_paiement','date_debut','date_fin']))
            <a href="{{ route('ventes.index') }}"
               class="border border-stone-200 hover:bg-stone-50 text-stone-600 px-4 py-2 rounded-lg text-sm transition">
                Réinitialiser
            </a>
            @endif
        </form>
    </div>

    {{-- Tableau --}}
    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class=" border-b border-stone-200 ">
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase tracking-wide">N° Vente</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase tracking-wide">Client</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase tracking-wide">Vendeur</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase tracking-wide">Paiement</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase tracking-wide">Total</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase tracking-wide">Statut</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase tracking-wide">Date</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($ventes as $vente)
                <tr class="hover:bg-stone-50 transition">
                    <td class="px-4 py-3 font-medium text-amber-600">{{ $vente->numero }}</td>

                    <td class="px-4 py-3 text-stone-700">
                        @if($vente->client)
                            <div class="font-medium">{{ $vente->client->nom }} {{ $vente->client->prenom }}</div>
                            <div class="text-xs text-stone-400">{{ $vente->client->telephone }}</div>
                        @else
                            <span class="text-stone-400 italic">Client passager</span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-stone-600">{{ $vente->vendeur->name }}</td>

                    <td class="px-4 py-3 text-stone-600">
                        @php
                            $icons = ['especes'=>'💵','carte'=>'💳','mobile_money'=>'📱','credit'=>'📋'];
                        @endphp
                        {{ $icons[$vente->mode_paiement] ?? '' }}
                        {{ ucfirst(str_replace('_',' ',$vente->mode_paiement)) }}
                    </td>

                    <td class="px-4 py-3 text-right font-semibold text-stone-800">
                        {{ number_format($vente->total_ttc, 0, ',', ' ') }} GNF
                        @if($vente->remise > 0)
                        <div class="text-xs text-green-600 font-normal">
                            -{{ number_format($vente->remise, 0, ',', ' ') }} GNF
                        </div>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-center">
                        @if($vente->statut === 'payee')
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">✅ Payée</span>
                        @elseif($vente->statut === 'en_cours')
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">⏳ En cours</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">❌ Annulée</span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-stone-500 text-xs">
                        {{ $vente->created_at->format('d/m/Y') }}<br>
                        <span class="text-stone-400">{{ $vente->created_at->format('H:i') }}</span>
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1">
                            {{-- Voir --}}
                            <a href="{{ route('ventes.show', $vente) }}"
                               class="p-1.5 rounded-lg hover:bg-blue-500 text-blue-500 transition" title="Voir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            {{-- Facture PDF --}}
                            <a href="{{ route('ventes.facture', $vente) }}" target="_blank"
                               class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-600 transition" title="Facture PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </a>
                            {{-- Modifier --}}
                            @if($vente->statut !== 'annulee' && auth()->user()->isAdmin())
                            <a href="{{ route('ventes.edit', $vente) }}"
                               class="p-1.5 rounded-lg hover:bg-stone-100 text-stone-500 transition" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            {{-- Annuler --}}
                            <form method="POST" action="{{ route('ventes.annuler', $vente) }}"
                                  onsubmit="return confirm('Annuler cette vente ? Le stock sera restauré.')">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50 text-red-500 transition" title="Annuler">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-stone-400">
                        <svg class="w-10 h-10 mx-auto text-stone-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4"/>
                        </svg>
                        <p class="text-sm">Aucune vente trouvée</p>
                        <a href="{{ route('ventes.create') }}" class="text-amber-600 text-sm mt-1 inline-block">
                            Enregistrer une vente →
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($ventes->hasPages())
        <div class="px-4 py-3 border-t border-stone-100">
            {{ $ventes->links() }}
        </div>
        @endif
    </div>

    <div class="mt-3 text-sm text-stone-400 text-right">
        {{ $ventes->total() }} vente(s) trouvée(s)
    </div>
</div>
@endsection
