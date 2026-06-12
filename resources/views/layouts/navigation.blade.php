<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links - visible uniquement sur desktop (sm et plus) -->
                <div class="hidden sm:flex space-x-8 sm:-my-px sm:ms-10">

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        🏠 {{ __('Accueil') }}
                    </x-nav-link>

                    <x-nav-link :href="route('ventes.index')" :active="request()->routeIs('ventes.*')">
                        🛒 {{ __('Ventes') }}
                    </x-nav-link>

                    <x-nav-link :href="route('produits.index')" :active="request()->routeIs('produits.*')">
                        🌸 {{ __('Produits') }}
                    </x-nav-link>
                  

                    <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                        👥 {{ __('Clients') }}
                    </x-nav-link>

                    <x-nav-link :href="route('fournisseurs.index')" :active="request()->routeIs('fournisseurs.*')">
                        🚚 {{ __('Fournisseurs') }}
                    </x-nav-link>
                      <x-nav-link :href="route('fichier-client.index')" :active="request()->routeIs('fichier-client.*')">
                        📂 {{ __('Fichier Clients') }}
                    </x-nav-link>
                    @if(auth()->user()->isAdmin())
                    <x-nav-link :href="route('rapports.ventes')" :active="request()->routeIs('rapports.*')">
                        📊 {{ __('Rapports') }}
                    </x-nav-link>
                    @endif
                @if(auth()->user()->isAdmin())
                    <x-nav-link :href="route('parametres.index')" :active="request()->routeIs('parametres.*')">
                        ⚙️ {{ __('Paramètres') }}
                    </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Dropdown desktop - visible uniquement sur desktop -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <a href="{{ route('ventes.create') }}"
                   class="mr-4 inline-flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white text-sm px-3 py-1.5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouvelle vente
                </a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="text-left">
                                    <div class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-400 capitalize">{{ Auth::user()->role }}</div>
                                </div>
                            </div>
                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-xs text-gray-500">Connecté en tant que</p>
                            <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-amber-600 capitalize">{{ Auth::user()->role }}</p>
                        </div>

                        <x-dropdown-link :href="route('ventes.index')">🛒 Ventes</x-dropdown-link>
                        <x-dropdown-link :href="route('clients.index')">👥 Clients</x-dropdown-link>
                        <x-dropdown-link :href="route('fournisseurs.index')">🚚 Fournisseurs</x-dropdown-link>
                        <x-dropdown-link :href="route('produits.index')">🌸 Produits</x-dropdown-link>
                        @if(auth()->user()->isAdmin())
                        <x-nav-link :href="route('parametres.index')" :active="request()->routeIs('parametres.*')">
                            ⚙️ {{ __('Paramètres') }}
                        </x-nav-link>
                        @endif

                        @if(auth()->user()->isAdmin())
                        <x-dropdown-link :href="route('parametres.utilisateurs')">⚙️ Utilisateurs</x-dropdown-link>
                        @endif

                        <div class="border-t border-gray-100"></div>
                        <x-dropdown-link :href="route('profile.edit')">👤 {{ __('Mon profil') }}</x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                🚪 {{ __('Déconnexion') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{--
                Hamburger — visible UNIQUEMENT sur mobile (disparaît à partir de sm: = 640px)
                block = visible par défaut (mobile)
                sm:hidden = caché sur desktop
            --}}
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400
                               hover:text-gray-500 hover:bg-gray-100 focus:outline-none
                               focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }"
                              class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }"
                              class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{--
        Menu mobile déroulant — s'affiche uniquement quand open = true
        ET uniquement sur mobile grâce à sm:hidden sur la nav principale
    --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="sm:hidden bg-white border-t border-gray-100 shadow-lg"
         @click.outside="open = false">

        {{-- Liens de navigation mobile --}}
        <div class="pt-2 pb-3 space-y-1">

            <x-responsive-nav-link :href="route('dashboard')"
                                   :active="request()->routeIs('dashboard')"
                                   @click="open = false">
                🏠 {{ __('Accueil') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('ventes.index')"
                                   :active="request()->routeIs('ventes.*')"
                                   @click="open = false">
                🛒 {{ __('Ventes') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('ventes.create')"
                                   @click="open = false">
                ➕ {{ __('Nouvelle vente') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('produits.index')"
                                   :active="request()->routeIs('produits.*')"
                                   @click="open = false">
                🌸 {{ __('Produits') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('clients.index')"
                                   :active="request()->routeIs('clients.*')"
                                   @click="open = false">
                👥 {{ __('Clients') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('fournisseurs.index')"
                                   :active="request()->routeIs('fournisseurs.*')"
                                   @click="open = false">
                🚚 {{ __('Fournisseurs') }}
            </x-responsive-nav-link>

            @if(auth()->user()->isAdmin())
            <x-responsive-nav-link :href="route('rapports.ventes')"
                                   :active="request()->routeIs('rapports.*')"
                                   @click="open = false">
                📊 {{ __('Rapports') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('parametres.utilisateurs')"
                                   :active="request()->routeIs('parametres.utilisateurs.*')"
                                   @click="open = false">
                ⚙️ {{ __('Utilisateurs') }}
            </x-responsive-nav-link>
            @endif

        </div>

        {{-- Infos utilisateur mobile --}}
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-amber-600 capitalize">{{ Auth::user()->role }}</div>
                </div>
            </div>

            <div class="space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" @click="open = false">
                    👤 {{ __('Mon profil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        🚪 {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>

    </div>
</nav>