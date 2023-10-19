<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::all();

        return $groups;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group = Group::create($validatedData);

        return response()->json($group, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        return $group;
    }
    /**
     * Display the specified resource depending on the token.
     */
    public function showFromToken($token)
    {
        $group = Group::with('users')->where('token', $token)->first();

        if (!$group) {
            return response()->json(['message' => 'Aucun groupe trouvé avec ce token'], 404);
        }

        return $group;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {

        if (!$group) {
            return response()->json(['message' => 'Groupe non trouvé'], 404);
        }

        // Valider les données de la requête
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'token' => 'string|max:255',
        ]);

        // Mettre à jour les données du groupe
        $group->update($validatedData);

        return response()->json(['message' => 'Groupe mis à jour avec succès'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {

        if (!$group) {
            return response()->json(['message' => 'Groupe non trouvé'], 404);
        }

        $group->delete();
        return response()->json(['message' => 'Groupe supprimé avec succès'], 200);
    }


    public function join($token)
    {
        $group = $this->showFromToken($token);
        if ($group) {
            $user = Auth::user();
            if (!$user->groups()->where('group_id', $group->id)->exists()) {
                $user->groups()->attach($group->id);
            }
            //Ajout des tracks au group
            //Besoin si j'intègre fonction de suppression de sons ou qu'on visualise les musique dans le lobby avant de les play;
            // foreach ($user->songs as $song) {

            //     $isAlreadyAttached = $group->songs()->wherePivot('song_id', $song->id)->exists();
            //     if (!$isAlreadyAttached) {
            //         $group->songs()->attach($song->id);
            //     }
            // }



            return response()->json(['group' => $group], 200);
        } else {
            return response()->json(['message' => 'Groupe non trouvé'], 404);
        }
    }

    public function getRandomTrack($token)
    {
        $group = $this->showFromToken($token);

        $users = $group->users;
        $userIndex = rand(0, count($users) - 1);
        $user = $users[$userIndex];

        $songs = $user->songs->makeHidden(['songs']);
        $songIndex = rand(0, count($songs) - 1);
        $song = $songs[$songIndex];

        return response()->json(['song' => $song, 'from' => $user->setVisible(["id", "name"])], 200);
    }
}
