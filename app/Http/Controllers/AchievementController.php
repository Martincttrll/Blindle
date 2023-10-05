<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $achievement = Achievement::all();
        return $achievement;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        // Créer une nouvelle chanson
        $achievement = Achievement::create($validatedData);

        return response()->json($achievement, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Achievement $achievement)
    {
        return $achievement;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Achievement $achievement)
    {
        if (!$achievement) {
            return response()->json(['message' => 'Achievement non trouvé'], 404);
        }

        // Valider les données de la requête
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'description' => 'string|max:255',
        ]);

        // Mettre à jour les données du achievement
        $achievement->update($validatedData);

        return response()->json(['message' => 'Achievement mis à jour avec succès'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Achievement $achievement)
    {
         if (!$achievement) {
            return response()->json(['message' => 'Achievement non trouvé'], 404);
        }

        $achievement->delete();
        return response()->json(['message' => 'Achievement supprimé avec succès'], 200);
    }
}
