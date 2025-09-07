<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with(['videos', 'images', 'categories'])->get();

        $postsData = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'post_status' => $post->post_status,
                'event_status' => $post->event_status,
                'videos' => $post->videos,
                'images' => $post->images,
                'categories' => $post->categories
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $postsData
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'post_status' => 'nullable|boolean',
            'event_status' => 'nullable|boolean',
            'videos' => 'nullable|array',
            'videos.*' => 'nullable|url',
            'images' => 'nullable|array',
            'images.*' => 'nullable|url',
            'categories' => 'nullable|array',
            'categories.*' => 'nullable|integer|exists:post_categories,id',
        ]);

        $post = Post::create($validatedData);

        if (!empty($validatedData['videos'])) {
            foreach ($validatedData['videos'] as $videoUrl) {
                $post->videos()->create(['video_url' => $videoUrl]);
            }
        }

        // Attach images
        if (!empty($validatedData['images'])) {
            foreach ($validatedData['images'] as $imageUrl) {
                $post->images()->create(['image_url' => $imageUrl]);
            }
        }

        // Sync categories
        if (!empty($validatedData['categories'])) {
            $post->categories()->sync($validatedData['categories']);
        }

        $post->load('videos', 'images', 'categories');

        return response()->json([
            'status' => 'success',
            'data' => $post
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::with(['videos', 'images', 'categories'])->find($id);

        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $post
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|nullable|string',
            'post_status' => 'sometimes|nullable|boolean',
            'event_status' => 'sometimes|nullable|boolean',
            'videos' => 'sometimes|nullable|array',
            'videos.*' => 'sometimes|nullable|url',
            'images' => 'sometimes|nullable|array',
            'images.*' => 'sometimes|nullable|url',
            'categories' => 'sometimes|nullable|array',
            'categories.*' => 'sometimes|nullable|integer|exists:post_categories,id',
        ]);

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found'
            ], 404);
        }

        $post->update($validatedData);

        if (!empty($validatedData['videos'])) {
            foreach ($validatedData['videos'] as $videoUrl) {
                $post->videos()->create(['video_url' => $videoUrl]);
            }
        }

        // Attach images
        if (!empty($validatedData['images'])) {
            foreach ($validatedData['images'] as $imageUrl) {
                $post->images()->create(['image_url' => $imageUrl]);
            }
        }

        // Sync categories
        if (!empty($validatedData['categories'])) {
            $post->categories()->sync($validatedData['categories']);
        }

        $post->load('videos', 'images', 'categories');

        return response()->json([
            'status' => 'success',
            'data' => $post
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found'
            ], 404);
        }

        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted successfully'
        ]);
    }
}

//mock data
// {
//     "title": "Sample Post",
//     "content": "This is a sample post content.",
//     "post_status": "published",
//     "event_status": "upcoming",
//     "videos": [
//         "https://example.com/video1.mp4",
//         "https://example.com/video2.mp4"
//     ],
//     "images": [
//         "https://example.com/image1.jpg",
//         "https://example.com/image2.jpg"
//     ],
//     "categories": [1, 2, 3]
// }
