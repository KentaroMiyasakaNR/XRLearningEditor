// tests/Feature/Api/TweetTest.php

<?php

use App\Models\Tweet;
use App\Models\User;

// 作成のテスト
it('allows authenticated users to create a tweet', function () {
  $user = User::factory()->create();
  $token = $user->createToken('test_token')->plainTextToken;

  $data = ['tweet' => 'This is a new tweet'];

  $response = $this->postJson('/api/tweets', $data, [
    'Authorization' => 'Bearer ' . $token
  ]);

  $response->assertStatus(201);
  $response->assertJson(['tweet' => 'This is a new tweet']);
});

// 一覧取得のテスト
it('displays a list of tweets', function () {
  $user = User::factory()->create();
  $token = $user->createToken('test_token')->plainTextToken;

  Tweet::factory()->count(3)->create();

  $response = $this->getJson('/api/tweets', [
    'Authorization' => 'Bearer ' . $token
  ]);

  $response->assertStatus(200);
  $response->assertJsonCount(3);
});

// 詳細取得のテスト
it('displays a specific tweet', function () {
  $user = User::factory()->create();
  $token = $user->createToken('test_token')->plainTextToken;

  $tweet = Tweet::factory()->create();

  $response = $this->getJson('/api/tweets/' . $tweet->id, [
    'Authorization' => 'Bearer ' . $token
  ]);

  $response->assertStatus(200);
  $response->assertJson(['id' => $tweet->id]);
});

// 更新のテスト
it('allows a user to update their tweet', function () {
  $user = User::factory()->create();
  $token = $user->createToken('test_token')->plainTextToken;

  $tweet = Tweet::factory()->create(['user_id' => $user->id]);

  $data = ['tweet' => 'Updated tweet content'];

  $response = $this->putJson('/api/tweets/' . $tweet->id, $data, [
    'Authorization' => 'Bearer ' . $token
  ]);

  $response->assertStatus(200);
  $response->assertJson(['tweet' => 'Updated tweet content']);
});

// 削除のテスト
it('allows a user to delete their tweet', function () {
  $user = User::factory()->create();
  $token = $user->createToken('test_token')->plainTextToken;

  $tweet = Tweet::factory()->create(['user_id' => $user->id]);

  $response = $this->deleteJson('/api/tweets/' . $tweet->id, [], [
    'Authorization' => 'Bearer ' . $token
  ]);

  $response->assertStatus(200);
  $response->assertJson(['message' => 'Tweet deleted successfully']);
});

// 更新のテスト（他のユーザのデータが更新できないことを確認）
it('does not allow unauthorized users to update a tweet', function () {
  // ユーザを2人作成
  $owner = User::factory()->create();
  $otherUser = User::factory()->create();

  // 一人目で Tweet を作成
  $tweet = Tweet::factory()->create(['user_id' => $owner->id]);

  // 二人目で認証
  $token = $otherUser->createToken('test_token')->plainTextToken;

  // 一人目の Tweet を二人目で更新（失敗するのが正しい）
  $response = $this->putJson('/api/tweets/' . $tweet->id, ['tweet' => 'Updated tweet'], [
    'Authorization' => 'Bearer ' . $token
  ]);

  $response->assertStatus(403); // Forbidden
});

// 削除のテスト（他のユーザのデータが削除できないことを確認）
it('does not allow unauthorized users to delete a tweet', function () {
  $owner = User::factory()->create();
  $otherUser = User::factory()->create();

  $tweet = Tweet::factory()->create(['user_id' => $owner->id]);

  $token = $otherUser->createToken('test_token')->plainTextToken;

  $response = $this->deleteJson('/api/tweets/' . $tweet->id, [], [
    'Authorization' => 'Bearer ' . $token
  ]);

  $response->assertStatus(403); // Forbidden
});