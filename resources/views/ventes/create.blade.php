@extends('layouts.app')
@section('title', 'Nouvelle Vente')

@section('content')
<div class="p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('ventes.index') }}" class="text-stone-400 hover:text-stone-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-2xl font-semibold text-stone-800">Point de Vente</h2>
    </div>

    <form method="POST" action="{{ route('ventes.store') }}" id="form-vente">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ═══ COLONNE GAUCHE : Catalogue ═══ --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Recherche --}}
                <div class="bg-white border border-stone-200 rounded-xl p-3">
                    <input type="text" id="search-produit"
                           placeholder="🔍 Rechercher un produit..."
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                {{-- Catalogue — MAX 4 LIGNES VISIBLES --}}
                <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
                    <div class="p-3 border-b border-stone-100 flex items-center justify-between">
                        <h3 class="font-medium text-stone-800 text-sm">Catalogue produits</h3>
                        <span class="text-xs text-stone-400">{{ $produits->count() }} produits</span>
                    </div>
                    {{-- max-h = 4 lignes (chaque ligne ~56px) --}}
                    <div class="divide-y divide-stone-100 overflow-y-auto" style="max-height: 224px;" id="liste-produits">
                        @foreach($produits as $produit)
                        <div class="produit-item flex items-center justify-between px-4 py-3 hover:bg-amber-50 cursor-pointer transition"
                             data-id="{{ $produit->id }}"
                             data-nom="{{ $produit->nom }}"
                             data-prix="{{ $produit->prix_vente }}"
                             data-stock="{{ $produit->stock_actuel }}"
                             data-ref="{{ $produit->reference }}"
                             onclick="ajouterProduit(this)">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-base shrink-0">🌸</div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-stone-800 truncate">{{ $produit->nom }}</p>
                                    <p class="text-xs text-stone-400">{{ $produit->reference }} — {{ $produit->contenance }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0 ml-3">
                                <p class="text-sm font-semibold text-amber-600">{{ number_format($produit->prix_vente, 0, ',', ' ') }} GNF</p>
                                <p class="text-xs {{ $produit->estStockFaible() ? 'text-red-500' : 'text-stone-400' }}">
                                    Stock : {{ $produit->stock_actuel }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Panier --}}
                <div class="bg-white border border-stone-200 rounded-xl p-4">
                    <h3 class="font-medium text-stone-800 text-sm mb-3">🛒 Panier</h3>
                    <div id="panier-vide" class="text-center py-4 text-stone-400 text-sm">
                        Cliquez sur un produit pour l'ajouter
                    </div>
                    <div id="panier-lignes" class="space-y-2 hidden"></div>
                </div>

            </div>

            {{-- ═══ COLONNE DROITE : Paiement ═══ --}}
            <div class="space-y-4">

                {{-- Client --}}
                <div class="bg-white border border-stone-200 rounded-xl p-4">
                    <label class="block text-sm font-medium text-stone-700 mb-2">👤 Client</label>
                    <select name="client_id"
                            class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">— Client passager —</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}">
                            {{ $client->nom }} {{ $client->prenom }} ({{ $client->telephone }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Totaux --}}
                <div class="bg-white border border-stone-200 rounded-xl p-4 space-y-3">
                    <div class="flex justify-between text-sm text-stone-600">
                        <span>Sous-total</span>
                        <span id="affiche-ht">0 GNF</span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-stone-600">
                        <label for="remise">Remise (GNF)</label>
                        <input type="number" name="remise" id="remise" value="0" min="0"
                               oninput="calculerTotal()"
                               class="w-28 border border-stone-200 rounded-lg px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="border-t border-stone-100 pt-2 flex justify-between font-semibold text-stone-800">
                        <span>TOTAL TTC</span>
                        <span id="affiche-ttc" class="text-amber-600 text-lg">0 GNF</span>
                    </div>
                </div>

                {{-- Montant payé & Reste --}}
                <div class="bg-white border border-stone-200 rounded-xl p-4">
                    <label class="block text-sm font-medium text-stone-700 mb-2">💵 Montant Payer</label>
                    <input type="number" name="montant_paye" id="montant_paye"
                           min="0" placeholder="0" oninput="calculerReste()"
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">

                    <div id="box-reste" class="hidden mt-3 p-3 rounded-lg border flex justify-between items-center">
                        <span id="label-reste" class="text-sm font-medium"></span>
                        <span id="valeur-reste" class="text-sm font-bold"></span>
                    </div>
                </div>

                {{-- Mode paiement --}}
                <div class="bg-white border border-stone-200 rounded-xl p-4">
                    <label class="block text-sm font-medium text-stone-700 mb-3">Mode de paiement</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['especes'=>'💵 Espèces','mobile_money'=>'📱 Mobile'] as $val => $label)
                        <label class="flex items-center gap-2 border border-stone-200 rounded-lg p-2 cursor-pointer hover:border-amber-400 transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                            <input type="radio" name="mode_paiement" value="{{ $val }}"
                                   {{ $val === 'especes' ? 'checked' : '' }}
                                   class="accent-amber-600">
                            <span class="text-xs">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

               {{-- Bouton valider --}}
                <button type="submit" id="btn-valider"
                        class="w-full bg-amber-600 hover:bg-amber-700 text-white py-3 rounded-xl font-semibold text-sm transition">
                    ✅ Valider la vente
                </button>

            </div>
        </div>
    </form>
</div>

<script>
let panier = {};

function ajouterProduit(el) {
    const id    = el.dataset.id;
    const nom   = el.dataset.nom;
    const prix  = parseFloat(el.dataset.prix);
    const stock = parseInt(el.dataset.stock);
    const ref   = el.dataset.ref;

    if (panier[id]) {
        if (panier[id].quantite >= stock) {
            alert(`Stock insuffisant pour ${nom} (max: ${stock})`);
            return;
        }
        panier[id].quantite++;
    } else {
        panier[id] = { id, nom, prix, stock, ref, quantite: 1 };
    }
    rendrePanel();
}

function changerQuantite(id, val) {
    const q = parseInt(val);
    if (q <= 0) { supprimerLigne(id); return; }
    if (q > panier[id].stock) {
        alert(`Stock max : ${panier[id].stock}`);
        document.getElementById(`qte-${id}`).value = panier[id].quantite;
        return;
    }
    panier[id].quantite = q;
    rendrePanel();
}

function supprimerLigne(id) {
    delete panier[id];
    rendrePanel();
}

function rendrePanel() {
    const lignesDiv  = document.getElementById('panier-lignes');
    const panierVide = document.getElementById('panier-vide');
    const btnValider = document.getElementById('btn-valider');
    lignesDiv.innerHTML = '';

    const ids = Object.keys(panier);

    if (ids.length === 0) {
        panierVide.classList.remove('hidden');
        lignesDiv.classList.add('hidden');
        btnValider.disabled = ids.length === 0;
        calculerTotal();
        calculerReste();
        return;
    }

    panierVide.classList.add('hidden');
    lignesDiv.classList.remove('hidden');
    btnValider.disabled = false;

    ids.forEach((id, index) => {
        const item      = panier[id];
        const soustotal = item.prix * item.quantite;

        lignesDiv.innerHTML += `
            <div class="flex items-center gap-2 bg-stone-50 rounded-lg p-2">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-stone-800 truncate">${item.nom}</p>
                    <p class="text-xs text-stone-400">${item.prix.toLocaleString('fr-FR')} GNF/u</p>
                </div>
                <input type="number" id="qte-${id}" value="${item.quantite}" min="1" max="${item.stock}"
                       onchange="changerQuantite('${id}', this.value)"
                       class="w-14 border border-stone-200 rounded px-1 py-0.5 text-xs text-center focus:outline-none focus:ring-1 focus:ring-amber-500">
                <span class="text-xs font-semibold text-amber-600 w-24 text-right">
                    ${soustotal.toLocaleString('fr-FR')} GNF
                </span>
                <button type="button" onclick="supprimerLigne('${id}')"
                        class="text-red-400 hover:text-red-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <input type="hidden" name="lignes[${index}][produit_id]" value="${id}">
                <input type="hidden" name="lignes[${index}][quantite]"   value="${item.quantite}" id="h-qte-${id}">
            </div>
        `;
    });

    calculerTotal();
    calculerReste();
}

function calculerTotal() {
    let ht = 0;
    Object.values(panier).forEach(item => ht += item.prix * item.quantite);
    const remise = parseFloat(document.getElementById('remise').value) || 0;
    const ttc    = Math.max(0, ht - remise);
    document.getElementById('affiche-ht').textContent  = ht.toLocaleString('fr-FR')  + ' GNF';
    document.getElementById('affiche-ttc').textContent = ttc.toLocaleString('fr-FR') + ' GNF';
    calculerReste();
}

function calculerReste() {
    const remise  = parseFloat(document.getElementById('remise').value) || 0;
    const montant = parseFloat(document.getElementById('montant_paye').value) || 0;

    let ht = 0;
    Object.values(panier).forEach(i => ht += i.prix * i.quantite);
    const ttc   = Math.max(0, ht - remise);
    const reste = ttc - montant; // TOTAL TTC - montant payé

    const box    = document.getElementById('box-reste');
    const label  = document.getElementById('label-reste');
    const valeur = document.getElementById('valeur-reste');

    if (ttc === 0 || montant === 0) {
        box.classList.add('hidden');
        return;
    }

    box.classList.remove('hidden');

    if (reste > 0) {
        // Paiement partiel — on enregistre quand même
        box.className     = 'mt-3 p-3 rounded-lg border flex justify-between items-center bg-red-50 border-red-200';
        label.className   = 'text-sm font-medium text-red-700';
        label.textContent = '⚠️ Reste à payer';
        valeur.className  = 'text-sm font-bold text-red-600';
        valeur.textContent = reste.toLocaleString('fr-FR') + ' GNF';

    } else if (reste === 0) {
        box.className     = 'mt-3 p-3 rounded-lg border flex justify-between items-center bg-blue-50 border-blue-200';
        label.className   = 'text-sm font-medium text-blue-700';
        label.textContent = '🎯 Compte exact';
        valeur.className  = 'text-sm font-bold text-blue-600';
        valeur.textContent = '0 GNF';

    } else {
        box.className     = 'mt-3 p-3 rounded-lg border flex justify-between items-center bg-green-50 border-green-200';
        label.className   = 'text-sm font-medium text-green-700';
        label.textContent = '💚 Monnaie à rendre';
        valeur.className  = 'text-sm font-bold text-green-600';
        valeur.textContent = Math.abs(reste).toLocaleString('fr-FR') + ' GNF';
    }
}

document.getElementById('search-produit').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.produit-item').forEach(el => {
        const txt = el.dataset.nom.toLowerCase() + el.dataset.ref.toLowerCase();
        el.style.display = txt.includes(q) ? '' : 'none';
    });
});
</script>
@endsection