<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Tweet;

class CommentController extends Controller
{
    public function index()
    {
        //
    }

    public function create(Tweet $tweet)
    {
        return view('tweets.comments.create', compact('tweet'));
    }

    public function store(Request $request, Tweet $tweet)
    {
        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        $tweet->comments()->create([
            'comment' => $request->comment,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('tweets.show', $tweet);
    }

    public function show(Tweet $tweet, Comment $comment)
    {
        return view('tweets.comments.show', compact('tweet', 'comment'));
    }

    public function edit(Tweet $tweet, Comment $comment)
    {
        return view('tweets.comments.edit', compact('tweet', 'comment'));
    }

    public function update(Request $request, Tweet $tweet, Comment $comment)
    {
        $request->validate([
            'comment' => 'required|string|max:255',
        ]);
    
        $comment->update($request->only('comment'));
    
        return redirect()->route('tweets.comments.show', [$tweet, $comment]);
    }

    public function destroy(Tweet $tweet, Comment $comment)
    {
        $comment->delete();
    
        return redirect()->route('tweets.show', $tweet);
    }
} 