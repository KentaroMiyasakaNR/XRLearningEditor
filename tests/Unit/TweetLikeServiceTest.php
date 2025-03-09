<?php

use App\Models\User;
use App\Models\Tweet;
use App\Services\TweetLikeService;

// likeのテスト
it('adds a like to a tweet', function () {
  $tweetLikeService = new TweetLikeService();
  $user = User::factory()->create();
  $tweet = Tweet::factory()->create();

  $tweetLikeService->likeTweet($tweet, $user);

  $this->assertDatabaseHas('tweet_user', [
    'user_id' => $user->id,
    'tweet_id' => $tweet->id
  ]);
});

// 重複likeのテスト
it('does not add a duplicate like', function () {
  $tweetLikeService = new TweetLikeService();
  $user = User::factory()->create();
  $tweet = Tweet::factory()->create();

  // 最初にlikeをする
  $tweetLikeService->likeTweet($tweet, $user);
  // 同じTweetに再度「いいね」を試みる
  $tweetLikeService->likeTweet($tweet, $user);

  // データベースをチェックし、重複したlikeがないことを確認
  $likesCount = $tweet->liked()->where('user_id', $user->id)->count();
  expect($likesCount)->toEqual(1);
});

// dislikeのテスト
it('removes a like from a tweet', function () {
  $tweetLikeService = new TweetLikeService();
  $user = User::factory()->create();
  $tweet = Tweet::factory()->create();

  // likeを追加
  $tweetLikeService->likeTweet($tweet, $user);

  // likeを解除
  $tweetLikeService->dislikeTweet($tweet, $user);

  // データベースをチェックし，likeが削除されていることを確認
  $this->assertDatabaseMissing('tweet_user', [
    'user_id' => $user->id,
    'tweet_id' => $tweet->id
  ]);
});

// 重複dislikeのテスト
it('does nothing if trying to dislike a tweet that is not liked', function () {
  $tweetLikeService = new TweetLikeService();
  $user = User::factory()->create();
  $tweet = Tweet::factory()->create();

  // likeを解除しようとするが，最初からlikeがない
  $tweetLikeService->dislikeTweet($tweet, $user);

  // データベースをチェックし，like存在しないことを確認
  $this->assertDatabaseMissing('tweet_user', [
    'user_id' => $user->id,
    'tweet_id' => $tweet->id
  ]);
});
