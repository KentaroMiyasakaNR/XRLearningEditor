<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tweet;
use Illuminate\Http\Request;
use App\Services\TweetLikeService;

class TweetLikeController extends Controller
{
    protected $tweetLikeService;

    public function __construct(TweetLikeService $tweetLikeService)
    {
        $this->tweetLikeService = $tweetLikeService;
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
      $this->tweetLikeService->likeTweet($tweet, auth()->user());
      return response()->json(['message' => 'Tweet liked successfully'], 201);
    }
  
    // ...
  
    // 🔽 編集
    public function destroy(Tweet $tweet)
    {
      $this->tweetLikeService->dislikeTweet($tweet, auth()->user());
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
