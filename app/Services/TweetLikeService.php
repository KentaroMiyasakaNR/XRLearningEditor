<?php

namespace App\Services;
use App\Models\Tweet;
use App\Models\User;

class TweetLikeService
{

   // 省略

  // like 状態を判定する関数
  public function isTweetLiked(Tweet $tweet, User $user)
  {
    return $tweet->liked()->where('user_id', $user->id)->exists();
  }

  public function likeTweet(Tweet $tweet, User $user)
  {
    // like 状態を判定して，like していなければ like する
    if (!$this->isTweetLiked($tweet, $user)) {
      $tweet->liked()->attach($user->id);
    }
    return $tweet->liked()->count();
  }

  public function dislikeTweet(Tweet $tweet, User $user)
  {
    return $tweet->liked()->detach($user->id);
  }
}
