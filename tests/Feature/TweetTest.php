<?php
// 🔽 追加
use App\Models\Tweet;
use App\Models\User;

it('displays tweets', function () {
    // ユーザを作成
    $user = User::factory()->create();
  
    // ユーザを認証
    $this->actingAs($user);
  
    // Tweetを作成
    $tweet = Tweet::factory()->create();
  
    // GETリクエスト
    $response = $this->get('/tweets');
  
    // レスポンスにTweetの内容と投稿者名が含まれていることを確認
    $response->assertStatus(200);
    $response->assertSee($tweet->tweet);
    $response->assertSee($tweet->user->name);
  });

  it('displays the create tweet page', function () {
    // テスト用のユーザーを作成
    $user = User::factory()->create();
  
    // ユーザーを認証（ログイン）
    $this->actingAs($user);
  
    // 作成画面にアクセス
    $response = $this->get('/tweets/create');
  
    // ステータスコードが200であることを確認
    $response->assertStatus(200);
  });

  it('allows authenticated users to create a tweet', function () {
    // ユーザを作成
    $user = User::factory()->create();
  
    // ユーザを認証
    $this->actingAs($user);
  
    // Tweetを作成
    $tweetData = ['tweet' => 'This is a test tweet.'];
  
    // POSTリクエスト
    $response = $this->post('/tweets', $tweetData);
  
    // データベースに保存されたことを確認
    $this->assertDatabaseHas('tweets', $tweetData);
  
    // レスポンスの確認
    $response->assertStatus(302);
    $response->assertRedirect('/tweets');
  });

  it('displays a tweet', function () {
    // ユーザを作成
    $user = User::factory()->create();
  
    // ユーザを認証
    $this->actingAs($user);
  
    // Tweetを作成
    $tweet = Tweet::factory()->create();
  
    // GETリクエスト
    $response = $this->get("/tweets/{$tweet->id}");
  
    // レスポンスにTweetの内容が含まれていることを確認
    $response->assertStatus(200);
    $response->assertSee($tweet->tweet);
    $response->assertSee($tweet->created_at->format('Y-m-d H:i'));
    $response->assertSee($tweet->updated_at->format('Y-m-d H:i'));
    $response->assertSee($tweet->tweet);
    $response->assertSee($tweet->user->name);
  });

  
// 編集画面のテスト
it('displays the edit tweet page', function () {
    // テスト用のユーザーを作成
    $user = User::factory()->create();
  
    // ユーザーを認証（ログイン）
    $this->actingAs($user);
  
    // Tweetを作成
    $tweet = Tweet::factory()->create(['user_id' => $user->id]);
  
    // 編集画面にアクセス
    $response = $this->get("/tweets/{$tweet->id}/edit");
  
    // ステータスコードが200であることを確認
    $response->assertStatus(200);
  
    // ビューにTweetの内容が含まれていることを確認
    $response->assertSee($tweet->tweet);
  });

  // 更新処理のテスト
it('allows a user to update their tweet', function () {
    // ユーザを作成
    $user = User::factory()->create();
  
    // ユーザを認証
    $this->actingAs($user);
  
    // Tweetを作成
    $tweet = Tweet::factory()->create(['user_id' => $user->id]);
  
    // 更新データ
    $updatedData = ['tweet' => 'Updated tweet content.'];
  
    // PUTリクエスト
    $response = $this->put("/tweets/{$tweet->id}", $updatedData);
  
    // データベースが更新されたことを確認
    $this->assertDatabaseHas('tweets', $updatedData);
  
    // レスポンスの確認
    $response->assertStatus(302);
    $response->assertRedirect("/tweets/{$tweet->id}");
  });

  // 削除処理のテスト
it('allows a user to delete their tweet', function () {
    // ユーザを作成
    $user = User::factory()->create();
  
    // ユーザを認証
    $this->actingAs($user);
  
    // Tweetを作成
    $tweet = Tweet::factory()->create(['user_id' => $user->id]);
  
    // DELETEリクエスト
    $response = $this->delete("/tweets/{$tweet->id}");
  
    // データベースから削除されたことを確認
    $this->assertDatabaseMissing('tweets', ['id' => $tweet->id]);
  
    // レスポンスの確認
    $response->assertStatus(302);
    $response->assertRedirect('/tweets');
  });

  it('can search tweets by content keyword', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
  
    // キーワードを含むツイートを作成
    Tweet::factory()->create([
      'tweet' => 'This is a test tweet',
      'user_id' => $user->id,
    ]);
  
    // キーワードを含まないツイートを作成
    Tweet::factory()->create([
      'tweet' => 'This is another tweet',
      'user_id' => $user->id,
    ]);
  
    // キーワード "test" で検索
    $response = $this->get(route('tweets.search', ['keyword' => 'test']));
  
    $response->assertStatus(200);
    $response->assertSee('This is a test tweet');
    $response->assertDontSee('This is another tweet');
  });
  
  it('shows no tweets if no match found', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
  
    Tweet::factory()->create([
      'tweet' => 'This is a tweet',
      'user_id' => $user->id,
    ]);
  
    // 存在しないキーワードで検索
    $response = $this->get(route('tweets.search', ['keyword' => 'nonexistent']));
  
    $response->assertStatus(200);
    $response->assertDontSee('This is a tweet');
    $response->assertSee('No tweets found.');
  });
  
  // 更新のテスト（他のユーザのデータが更新できないことを確認）
it('does not allow unauthorized users to update a tweet', function () {
  $owner = User::factory()->create();
  $otherUser = User::factory()->create();
  $tweet = Tweet::factory()->create(['user_id' => $owner->id]);

  $this->actingAs($otherUser);

  $response = $this->put("/tweets/{$tweet->id}", ['tweet' => 'Updated tweet']);

  $response->assertStatus(403); // Forbidden
});

// 削除のテスト（他のユーザのデータが削除できないことを確認）
it('does not allow unauthorized users to delete a tweet', function () {
  $owner = User::factory()->create();
  $otherUser = User::factory()->create();
  $tweet = Tweet::factory()->create(['user_id' => $owner->id]);

  $this->actingAs($otherUser);

  $response = $this->delete("/tweets/{$tweet->id}");

  $response->assertStatus(403); // Forbidden
});