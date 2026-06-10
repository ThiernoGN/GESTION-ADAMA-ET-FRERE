<div class="flex flex-wrap gap-2">
    <a href="{{ route('parametres.index') }}"
       class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition
              {{ request()->routeIs('parametres.index') ? 'bg-amber-600 text-white' : 'bg-white border border-stone-200 text-stone-600 hover:border-amber-300' }}">
        ⚙️ Accueil
    </a>
    <a href="{{ route('parametres.utilisateurs') }}"
       class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition
              {{ request()->routeIs('parametres.utilisateurs') ? 'bg-amber-600 text-white' : 'bg-white border border-stone-200 text-stone-600 hover:border-amber-300' }}">
        👤 Utilisateurs
    </a>
    <a href="{{ route('parametres.categories') }}"
       class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition
              {{ request()->routeIs('parametres.categories') ? 'bg-amber-600 text-white' : 'bg-white border border-stone-200 text-stone-600 hover:border-amber-300' }}">
        🏷️ Catégories
    </a>

    <a href="{{ route('fournisseurs.index') }}"
       class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition
              {{ request()->routeIs('fournisseurs.*') ? 'bg-amber-600 text-white' : 'bg-white border border-stone-200 text-stone-600 hover:border-amber-300' }}">
        🚚 Fournisseurs
    </a>
    <a href="{{ route('parametres.boutique') }}"
       class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition
              {{ request()->routeIs('parametres.boutique') ? 'bg-amber-600 text-white' : 'bg-white border border-stone-200 text-stone-600 hover:border-amber-300' }}">
        🏪 Boutique
    </a>
</div>
