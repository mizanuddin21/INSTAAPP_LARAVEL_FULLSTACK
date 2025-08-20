<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class PostWebController extends Controller
{
    public function index()
    {
        // $posts = Post::latest()->get(); // Fetch posts
        // $posts = Post::with('comments')->latest()->get();
        $posts = Post::with(['comments.user', 'likes']) // load comments + likes
        ->latest()
        ->get()
        ->map(function ($post) {
            $post->is_liked_by_auth_user = $post->likes->contains('user_id', auth()->id());
            return $post;
        });

        // Generate token for the currently authenticated user
        $token = auth()->user()->createToken('WebToken')->plainTextToken;

        return view('posts.index', compact('posts', 'token')); // Blade view
    }

    public function likePost(Post $post)
    {
        $user = auth()->user();

        $like = $post->likes()->where('user_id', $user->id)->first();
        if ($like) {
            // If already liked, unlike it
            $like->delete();
            return back();
        }

        // Otherwise, like it
        $post->likes()->create([
            'user_id' => $user->id,
        ]);

        return back();
    }

    public function storeComment(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string|max:1000',
        ]);

        Comment::create([
            'post_id' => $request->post_id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return back(); // Redirect back to the same page
    }
}
