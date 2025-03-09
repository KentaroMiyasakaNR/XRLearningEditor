<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tweet;
use App\Services\TweetLikeService;
class TweetLikeController extends Controller
{  // 🔽 追加
    protected $tweetLikeService;
  
    // 🔽 追加
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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


    public function store(Tweet $tweet)
    {
      // 🔽 編集
      $this->tweetLikeService->likeTweet($tweet, auth()->user());
      return back();
    }
  
    // ...
    public function destroy(Tweet $tweet)
    {
      // 🔽 編集
      $this->tweetLikeService->dislikeTweet($tweet, auth()->user());
      return back();
    }
}
