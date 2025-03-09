<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Tweet;
use Illuminate\Http\Request;

class CommentService
{

  // 省略

  public function tweetComments(Tweet $tweet)
  {
      return $tweet->comments()->with('user')->get();
  }

  public function createComment(Request $request, Tweet $tweet)
  {
      return $tweet->comments()->create([
          'comment' => $request->comment,
          'user_id' => $request->user()->id,
      ]);
  }

  public function updateComment(Request $request, Comment $comment)
  {
      $comment->update($request->only('comment'));
      return $comment;
  }

  public function deleteComment(Comment $comment)
  {
      $comment->delete();
  }
}
