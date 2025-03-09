<?php

namespace App\Services;

use App\Models\Tweet;
use Illuminate\Http\Request;

class TweetService
{

  // 省略

  public function createTweet(Request $request)
  {
    return $request->user()->tweets()->create($request->only('tweet'));
  }

  public function allTweets()
  {
    return Tweet::with('user')->latest()->get();

  }

  public function updateTweet(Request $request, Tweet $tweet)
  {
    $tweet->update($request->only('tweet'));
    return $tweet;
  }

  public function deleteTweet(Tweet $tweet)
  {
    return $tweet->delete();
  }
}