<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Display a listing of news
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $news = News::paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $news->items(),
            'meta' => [
                'total' => $news->total(),
                'per_page' => $news->perPage(),
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created news
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'author' => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'is_highlight' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'meta_data' => 'nullable|array',
            'type' => 'nullable|string',
            'type_url' => 'nullable|string',
            'media_url' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $news = new News();
        $news->title = json_encode(['id' => $request->title]);
        $news->slug = Str::slug($request->title);
        $news->excerpt = $request->excerpt ? json_encode(['id' => $request->excerpt]) : null;
        $news->content = json_encode(['id' => $request->content]);
        $news->author = json_encode(['name' => $request->author ?? 'Yava']);
        $news->published_at = $request->published_at ? $request->published_at : null;
        $news->is_highlight = $request->is_highlight ?? false;
        $news->tags = $request->tags ? json_encode($request->tags) : null;
        $news->meta_data = $request->meta_data ? json_encode($request->meta_data) : null;
        $news->type = $request->type ?? null;
        $news->type_url = $request->type_url ?? null;
        $news->media_url = $request->media_url ?? null;
        
        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagesPaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('news', 'public');
                $imagesPaths[] = $path;
            }
            $news->images = json_encode($imagesPaths);
        }
        
        $news->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'News created successfully',
            'data' => $news
        ], 201);
    }

    /**
     * Display the specified news
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();
        
        return response()->json([
            'status' => 'success',
            'data' => $news
        ]);
    }

    /**
     * Update the specified news
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'sometimes|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'author' => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'is_highlight' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'meta_data' => 'nullable|array',
            'type' => 'nullable|string',
            'type_url' => 'nullable|string',
            'media_url' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('title')) {
            $news->title = json_encode(['id' => $request->title]);
            $news->slug = Str::slug($request->title);
        }
        if ($request->has('excerpt')) {
            $news->excerpt = json_encode(['id' => $request->excerpt]);
        }
        if ($request->has('content')) {
            $news->content = json_encode(['id' => $request->content]);
        }
        if ($request->has('author')) {
            $news->author = json_encode(['name' => $request->author]);
        }
        if ($request->has('published_at')) {
            $news->published_at = $request->published_at;
        }
        if ($request->has('is_highlight')) {
            $news->is_highlight = $request->is_highlight;
        }
        if ($request->has('tags')) {
            $news->tags = json_encode($request->tags);
        }
        if ($request->has('meta_data')) {
            $news->meta_data = json_encode($request->meta_data);
        }
        if ($request->has('type')) {
            $news->type = $request->type;
        }
        if ($request->has('type_url')) {
            $news->type_url = $request->type_url;
        }
        if ($request->has('media_url')) {
            $news->media_url = $request->media_url;
        }
        
        // Handle image uploads
        if ($request->hasFile('images')) {
            // Delete old images
            if ($news->images) {
                $oldImages = json_decode($news->images, true);
                if (is_array($oldImages)) {
                    foreach ($oldImages as $image) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }
            
            $imagesPaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('news', 'public');
                $imagesPaths[] = $path;
            }
            $news->images = json_encode($imagesPaths);
        }
        
        $news->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'News updated successfully',
            'data' => $news
        ]);
    }

    /**
     * Remove the specified news
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $news = News::findOrFail($id);
        
        // Delete related images
        if ($news->images) {
            $images = json_decode($news->images, true);
            if (is_array($images)) {
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
        }
        
        $news->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'News deleted successfully'
        ]);
    }
}