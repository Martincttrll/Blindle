<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $songs = Song::all();

        return $songs;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'previewUrl' => 'required|string|max:255',
        ]);

        // Créer une nouvelle chanson
        $group = Song::create($validatedData);

        return response()->json($group, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Song $song)
    {
        return $song;
    }

}
