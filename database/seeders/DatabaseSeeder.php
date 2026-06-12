<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\Client;
use App\Models\Fournisseur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- UTILISATEURS ---
        User::firstOrCreate(
            ['email' => 'admin@parfumerie.com'],
            [
                'name'     => 'Administrateur',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );
        User::firstOrCreate(
            ['email' => 'vendeur@parfumerie.com'],
            [
                'name'     => 'Jean Vendeur',
                'password' => Hash::make('password'),
                'role'     => 'vendeur',
            ]
        );
        User::firstOrCreate(
            ['email' => 'caissier@parfumerie.com'],
            [
                'name'     => 'Marie Caissier',
                'password' => Hash::make('password'),
                'role'     => 'caissier',
            ]
        );

        // --- CATEGORIES ---
        $categories = [
            ['nom' => 'Eau de Parfum',   'slug' => 'eau-de-parfum'],
            ['nom' => 'Eau de Toilette', 'slug' => 'eau-de-toilette'],
            ['nom' => 'Parfum Oud',      'slug' => 'parfum-oud'],
            ['nom' => 'Coffrets',        'slug' => 'coffrets'],
        ];
        foreach ($categories as $cat) {
            Categorie::firstOrCreate(['slug' => $cat['slug']], $cat);
        }



        // --- PRODUITS ---
        $produits = [
            [
                'categorie_id'  => 1,
                'nom'           => 'Chanel N°5',
                'reference'     => 'CHN-001',
                'description'   => 'Le parfum mythique de Chanel',
                'prix_achat'    => 150000,
                'prix_vente'    => 250000,
                'stock_actuel'  => 20,
                'stock_minimum' => 5,
                'contenance'    => '100ml',
                'genre'         => 'femme',
            ],
            [
                'categorie_id'  => 1,
                'nom'           => 'Dior Sauvage',
                'reference'     => 'DIO-001',
                'description'   => 'Parfum masculin emblématique',
                'prix_achat'    => 120000,
                'prix_vente'    => 200000,
                'stock_actuel'  => 3,
                'stock_minimum' => 5,
                'contenance'    => '100ml',
                'genre'         => 'homme',
            ],
            [
                'categorie_id'  => 2,
                'nom'           => 'Versace Eros',
                'reference'     => 'VER-001',
                'description'   => 'Eau de toilette homme',
                'prix_achat'    => 90000,
                'prix_vente'    => 150000,
                'stock_actuel'  => 15,
                'stock_minimum' => 5,
                'contenance'    => '50ml',
                'genre'         => 'homme',
            ],
            [
                'categorie_id'  => 3,
                'nom'           => 'Armani Code',
                'reference'     => 'ARM-001',
                'description'   => 'Parfum oud oriental',
                'prix_achat'    => 200000,
                'prix_vente'    => 320000,
                'stock_actuel'  => 2,
                'stock_minimum' => 5,
                'contenance'    => '75ml',
                'genre'         => 'mixte',
            ],
            [
                'categorie_id'  => 2,
                'nom'           => 'Lacoste Essential',
                'reference'     => 'LAC-001',
                'description'   => 'Fraîcheur et légèreté',
                'prix_achat'    => 70000,
                'prix_vente'    => 120000,
                'stock_actuel'  => 10,
                'stock_minimum' => 5,
                'contenance'    => '75ml',
                'genre'         => 'homme',
            ],
        ];
        foreach ($produits as $produit) {
            Produit::firstOrCreate(['reference' => $produit['reference']], $produit);
        }

        // --- CLIENTS ---
        $clients = [
            [
                'nom'             => 'Diallo',
                'prenom'          => 'Mamadou',
                'telephone'       => '620000001',
                'email'           => 'mamadou@gmail.com',
                'adresse'         => 'Conakry, Kaloum',
                'points_fidelite' => 150,
            ],
            [
                'nom'             => 'Camara',
                'prenom'          => 'Fatoumata',
                'telephone'       => '620000002',
                'email'           => 'fatoumata@gmail.com',
                'adresse'         => 'Conakry, Ratoma',
                'points_fidelite' => 80,
            ],
            [
                'nom'             => 'Bah',
                'prenom'          => 'Ibrahima',
                'telephone'       => '620000003',
                'email'           => null,
                'adresse'         => 'Conakry, Matoto',
                'points_fidelite' => 0,
            ],
        ];
        foreach ($clients as $client) {
            Client::firstOrCreate(['telephone' => $client['telephone']], $client);
        }

        // --- FOURNISSEURS ---
        $fournisseurs = [
            [
                'nom'       => 'Paris Luxe Distribution',
                'telephone' => '+33123456789',
                'email'     => 'contact@parisluxe.fr',
                'adresse'   => 'Paris, France',
            ],
            [
                'nom'       => 'African Parfums SARL',
                'telephone' => '621000001',
                'email'     => 'info@africanparfums.com',
                'adresse'   => 'Conakry, Guinée',
            ],
        ];
        foreach ($fournisseurs as $fournisseur) {
            Fournisseur::firstOrCreate(['nom' => $fournisseur['nom']], $fournisseur);
        }
    }
}