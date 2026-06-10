<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Categorie;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ParametreController extends Controller
{
    public function index()
    {
        $stats = [
            'utilisateurs' => User::count(),
            'categories'   => Categorie::count(),
            'fournisseurs' => Fournisseur::count(),
        ];
        return view('parametres.index', compact('stats'));
    }

    // ══ UTILISATEURS ══════════════════════════════════════

    public function utilisateurs()
    {
        $utilisateurs = User::latest()->get();
        return view('parametres.utilisateurs', compact('utilisateurs'));
    }

    public function utilisateurStore(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,vendeur,caissier',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'actif'    => true,
        ]);

        return back()->with('success', "Utilisateur {$request->name} créé !");
    }

    public function utilisateurUpdate(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,vendeur,caissier',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
            'actif' => $request->has('actif'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return back()->with('success', "Utilisateur {$user->name} mis à jour !");
    }

    public function utilisateurDestroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors('Impossible de supprimer votre propre compte.');
        }
        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }

    public function utilisateurToggle(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors('Impossible de désactiver votre propre compte.');
        }
        $user->update(['actif' => !$user->actif]);
        $statut = $user->fresh()->actif ? 'activé' : 'désactivé';
        return back()->with('success', "Compte {$statut} avec succès.");
    }

    // ══ CATÉGORIES ════════════════════════════════════════

    public function categories()
    {
        $categories = Categorie::withCount('produits')->orderBy('nom')->get();
        return view('parametres.categories', compact('categories'));
    }

    public function categorieStore(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:255|unique:categories,nom',
            'description' => 'nullable|string',
        ]);
        Categorie::create([
            'nom'         => $request->nom,
            'slug'        => Str::slug($request->nom),
            'description' => $request->description,
        ]);
        return back()->with('success', "Catégorie « {$request->nom} » créée !");
    }

    public function categorieUpdate(Request $request, Categorie $categorie)
    {
        $request->validate([
            'nom'         => 'required|string|max:255|unique:categories,nom,' . $categorie->id,
            'description' => 'nullable|string',
        ]);
        $categorie->update([
            'nom'         => $request->nom,
            'slug'        => Str::slug($request->nom),
            'description' => $request->description,
        ]);
        return back()->with('success', "Catégorie mise à jour !");
    }

    public function categorieDestroy(Categorie $categorie)
    {
        if ($categorie->produits()->count() > 0) {
            return back()->withErrors("Impossible : {$categorie->produits()->count()} produit(s) utilisent cette catégorie.");
        }
        $categorie->delete();
        return back()->with('success', "Catégorie supprimée.");
    }





    // ══ BOUTIQUE ══════════════════════════════════════════

    public function boutique()
    {
        return view('parametres.boutique');
    }

    public function boutiqueUpdate(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:255',
            'adresse'   => 'nullable|string',
            'telephone' => 'nullable|string|max:20',
            'email'     => 'nullable|email',
            'devise'    => 'required|string|max:10',
        ]);

        $this->updateEnv([
            'BOUTIQUE_NOM'       => '"' . $request->nom . '"',
            'BOUTIQUE_ADRESSE'   => '"' . ($request->adresse ?? '') . '"',
            'BOUTIQUE_TELEPHONE' => '"' . ($request->telephone ?? '') . '"',
            'BOUTIQUE_EMAIL'     => '"' . ($request->email ?? '') . '"',
            'BOUTIQUE_DEVISE'    => '"' . $request->devise . '"',
        ]);

        return back()->with('success', 'Informations de la boutique mises à jour !');
    }

    private function updateEnv(array $data): void
    {
        $envPath    = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            if (str_contains($envContent, $key . '=')) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }
        file_put_contents($envPath, $envContent);
    }
}
