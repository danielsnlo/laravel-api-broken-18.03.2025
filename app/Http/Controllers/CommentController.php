<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the comments for a post.
     */
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index(Post $post)
    {
        return Comment::where('post_id', $post->id)->get();
    }

    // public function show() {

    // }

    /**
     * Store a newly created comment for a post.
     */
    public function store(Request $request, Post $post)
    {
        // Validate the incoming request data
        $fields = $request->validate([
            'body' => 'required|string|max:255',
        ]);

        $comment = new Comment();
        $comment->body = $request->body;
        $comment->post_id = $post->id;  // Associate the comment with the post
        $comment->user_id = $post->user_id;
        $comment->save();

        // Return the created comment
        return $comment; // HTTP 201 Created
    }
}
