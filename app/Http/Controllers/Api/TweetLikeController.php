<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tweet;
use Illuminate\Http\Request;

class TweetLikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Tweet $tweet)
    {
      $tweet->liked()->attach(auth()->id());
      return response()->json(['message' => 'Tweet liked successfully'], 201);
    }
  
    // ...
  
    // 🔽 編集
    public function destroy(Tweet $tweet)
    {
      $tweet->liked()->detach(auth()->id());
      return response()->json(['message' => 'Tweet disliked successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

}
