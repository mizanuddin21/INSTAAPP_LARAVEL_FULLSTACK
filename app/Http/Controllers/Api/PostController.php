<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //get semua data
    public function index()
    {
        $posts = Post::all();

        $posts->transform(function($post) {
            $post->image_url = $post->image ? asset('storage/' . $post->image) : null;
            return $post;
        });

        $respData = [
            'status'    => "success",
            'message'   => 'fetch data berhasil',
            'data'      => $posts
        ];

        return response()->json($respData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    //post data baru
    public function store(Request $request)
    {
        $request->validate([
            'caption' => 'required|string|max:255',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png|max:20480', //file upload
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // Simpan file ke folder storage/app/public/posts
            $path = $file->store('images', 'public');
        }  else {
            \Log::info('No image uploaded');
        }


        $post = Post::create([
            'caption' => $request->caption,
            'image' => $path,
            'user_id' => auth()->id(), // ambil dari user login
        ]);

        $respData = [
            'status'    => "success",
            'message'   => 'Post image berhasil',
            'data'      => $post,
            'redirect'  => route('posts.index')
        ];

        return response()->json($respData, 200);
    }

    //get data by name
    public function getByImageName(Request $request){
        // Validasi input
        $request->validate([
            'value' => 'required|string',
        ]);

        $imagePath = $request->input('value');

        // Cari post berdasarkan exact match image path
        $post = Post::where('image', $imagePath)->first();

        if (!$post) {
            return response()->json([
                'status'    => "failed",
                'message'   => 'data tidak ditemukan',
                'data'      => $post
            ], 404);
        }

        // Tambahkan URL agar bisa diakses
        $post->image_url = $post->image ? asset('storage/' . $post->image) : null;
        $respData = [
            'status'    => "success",
            'message'   => 'Fetch image by name berhasil',
            'data'      => $post
        ];
        
        return response()->json($respData);
    }

    //like some post
    public function likePost(Post $post)
    {
        $user = auth()->user();

        $like = $post->likes()->where('user_id', $user->id)->first();
        if ($like) {
            // jika sudah like, unlike
            $like->delete();
            return response()->json([
                'status'    => "success",
                'message'   => 'Posting Unliked',
                'liked'     => false 
            ]);
        }

        $post->likes()->create([
            'user_id' => $user->id,
        ]);

        return response()->json([
                'status'    => "success",
                'message'   => 'Posting liked',
                'liked'     => true 
            ]);
    }

    //comment some post
    public function commentPost(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        $respData = [
            'status'    => "success",
            'message'   => 'Comment posting berhasil.',
            'data'      => $comment
        ];
        return response()->json($respData);
    }

    // get all comments
    public function getComments(Post $post)
    {
        $comments = $post->comments()->with('user:id,name')->get();
        return response()->json([
            'status'    => "success",
            'message'   => 'Fetch comment berhasil.',
            'data'      => $comments
        ]);
    }

    // get all likes
    public function getLikes(Post $post)
    {
        $likes = $post->likes()->with('user:id,name')->get();
        return response()->json([
            'status'    => "success",
            'message'   => 'Fetch like berhasil.',
            'data'      => $likes
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //get by detail from post
    public function show(Post $post)
    {
        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // update post
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post); // pakai policy nanti

        $post->update($request->only(['caption', 'image']));

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //delete post
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(null, 204);
    }
}
