<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Display a listing of the news.
     */
    public function index()
    {
        $news = News::latest()->paginate(10);
        return NewsResource::collection($news)
            ->additional(['status' => 'success']);
    }

    /**
     * Store a new news item.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|array',
            'excerpt' => 'required|array',
            'content' => 'required|array',
            'media_url' => 'nullable|string',
            'author' => 'required|array',
            'is_highlight' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        $news = News::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title['en'] ?? ''),
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'media_url' => $request->media_url,
            'author' => $request->author,
            'is_highlight' => $request->is_highlight ?? false,
        ]);

        return new NewsResource($news);
    }

    /**
     * Display the specified news item.
     */
    public function show($id)
    {
        $news = News::find($id);
        
        if (!$news) {
            return response()->json([
                'status' => 'error',
                'message' => 'News not found',
            ], 404);
        }

        return new NewsResource($news);
    }

    /**
     * Show news by slug.
     */
    public function showBySlug($slug)
    {
        $news = News::where('slug', $slug)->first();
        
        if (!$news) {
            return response()->json([
                'status' => 'error',
                'message' => 'News not found',
            ], 404);
        }

        return new NewsResource($news);
    }

    /**
     * Update the specified news item.
     */
    public function update(Request $request, $id)
    {
        $news = News::find($id);
        
        if (!$news) {
            return response()->json([
                'status' => 'error',
                'message' => 'News not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'array',
            'excerpt' => 'array',
            'content' => 'array',
            'media_url' => 'nullable|string',
            'author' => 'array',
            'is_highlight' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        // Update slug only if title is provided
        $data = $request->all();
        if (isset($data['title']) && isset($data['title']['en'])) {
            $data['slug'] = Str::slug($data['title']['en']);
        }

        $news->update($data);

        return new NewsResource($news);
    }

    /**
     * Remove the specified news item.
     */
    public function destroy($id)
    {
        $news = News::find($id);
        
        if (!$news) {
            return response()->json([
                'status' => 'error',
                'message' => 'News not found',
            ], 404);
        }

        $news->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'News deleted successfully',
        ]);
    }

    /**
     * Get highlighted news.
     */
    public function highlights()
    {
        $news = News::where('is_highlight', true)
            ->latest()
            ->paginate(10);
            
        return NewsResource::collection($news)
            ->additional(['status' => 'success']);
    }

    /**
     * Get "You May Like" news - 8 latest news items.
     */
    public function youMayLike()
    {
        $news = News::latest()
            ->limit(8)
            ->get();
            
        return NewsResource::collection($news)
            ->additional([
                'status' => 'success',
                'message' => 'You may like these news'
            ]);
    }

    /**
     * Get "You May Like" news excluding current news.
     */
    public function youMayLikeNews($id)
    {
        $news = News::where('id', '!=', $id)
            ->latest()
            ->limit(8)
            ->get();
            
        return NewsResource::collection($news)
            ->additional([
                'status' => 'success',
                'message' => 'Related news'
            ]);
    }
}