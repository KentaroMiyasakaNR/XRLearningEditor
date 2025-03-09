<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Tweet;
use App\Services\CommentService;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
  protected $commentService;

  public function __construct(CommentService $commentService)
  {
    $this->commentService = $commentService;
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
  public function create(Tweet $tweet)
  {
    Gate::authorize('create', Comment::class);
    return view('tweets.comments.create', compact('tweet'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreCommentRequest $request, Tweet $tweet)
  {
    Gate::authorize('create', Comment::class);
    $this->commentService->createComment($request, $tweet);
    return redirect()->route('tweets.show', $tweet);
  }

  /**
   * Display the specified resource.
   */
  public function show(Tweet $tweet, Comment $comment)
  {
    return view('tweets.comments.show', compact('tweet', 'comment'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Tweet $tweet, Comment $comment)
  {
    return view('tweets.comments.edit', compact('tweet', 'comment'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateCommentRequest $request, Tweet $tweet, Comment $comment)
  {
    Gate::authorize('update', $comment);
    $this->commentService->updateComment($request, $comment);
    return redirect()->route('tweets.comments.show', [$tweet, $comment]);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Tweet $tweet, Comment $comment)
  {
    Gate::authorize('delete', $comment);
    $this->commentService->deleteComment($comment);
    return redirect()->route('tweets.show', $tweet);
  }
}

