<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreTweetRequest;
use App\Http\Requests\UpdateTweetRequest;
use App\Models\Tweet;
use Illuminate\Http\Request;
use App\Services\TweetService;
use Illuminate\Support\Facades\Gate;

class TweetController extends Controller
{
    protected $tweetService;
    public function __construct(TweetService $tweetService)
    {
        $this->tweetService = $tweetService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Tweet::class);
        $tweets = $this->tweetService->allTweets();
        // dd($tweets);
        return view('tweets.index', compact('tweets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Tweet::class);
        return view('tweets.create');
    }

    public function store(StoreTweetRequest $request)
    {
      // バリデーションは削除
      $tweet = $this->tweetService->createTweet($request);
  
      return redirect()->route('tweets.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tweet $tweet)
    {
        Gate::authorize('view', $tweet);
        $tweet->load('comments');
        return view('tweets.show', compact('tweet'));
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tweet $tweet)
    {
      Gate::authorize('update', $tweet);
      return view('tweets.edit', compact('tweet'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTweetRequest $request, Tweet $tweet)
    {
      // バリデーションは削除
      $updatedTweet = $this->tweetService->updateTweet($request, $tweet);
  
      return redirect()->route('tweets.show', $tweet);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tweet $tweet)
    {
      Gate::authorize('delete', $tweet);
      $tweet->delete();
  
      return redirect()->route('tweets.index');
    }
        /**
     * Search for tweets containing the keyword.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        Gate::authorize('viewAny', Tweet::class);
        $query = Tweet::query();

        // キーワードが指定されている場合のみ検索を実行
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('tweet', 'like', '%' . $keyword . '%');
        }

        // ページネーションを追加（1ページに10件表示）
        $tweets = $query
            ->latest()
            ->paginate(10);

        return view('tweets.search', compact('tweets'));
    }
}
