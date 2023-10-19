<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'string|max:255',
            'avatar' => 'string',
            'idSpotify' => 'string'
        ]);


        $user = User::create($validatedData);

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $user;
    }
    /**
     * Display the specified resource of current user.
     */
    public function showCurrent()
    {
        return Auth::user();
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!$user) {
            return response()->json(['message' => 'user non trouvé'], 404);
        }

        // Valider les données de la requête
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'nbWin' => 'int|max:11',
            'nbGame' => 'int|max:11',
            'email' => 'string|max:255',
        ]);

        // Mettre à jour les données du user
        $user->update($validatedData);

        return response()->json(['message' => 'user mis à jour avec succès'], 200);
    }

    public function getHistory(User $user)
    {
        return response()->json($user->groups);
    }
}
