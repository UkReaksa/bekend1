<?php

namespace App\Http\Controllers;

use App\Models\PostCategory;
use Illuminate\Http\Request;

class PostCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $post_categories = PostCategory::all();
        return response()->json([
            'status' => 'success',
            'data' => $post_categories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $post_category = PostCategory::create($validatedData);
        return response()->json([
            'status' => 'success',
            'data' => $post_category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post_category = PostCategory::find($id);

        if (!$post_category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post category not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $post_category
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $post_category = PostCategory::find($id);
        
        if (!$post_category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post category not found'
            ], 404);
        }

        $post_category->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $post_category
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post_category = PostCategory::find($id);
        if (!$post_category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post category not found'
            ], 404);
        }

        $post_category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Post category deleted successfully'
        ], 200);
    }
}
