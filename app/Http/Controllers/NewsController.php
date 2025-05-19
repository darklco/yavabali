<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Display a listing of the news.
     */
    public function index()
    {
        $news = News::latest()->paginate(10);

        return NewsResource::collection($news)->additional([
            'status' => 'success',
        ]);
    }

    /**
     * Store a newly created news item.
     */
    public function store(NewsRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']['en'] ?? '');

        $news = News::create($data);

        return (new NewsResource($news))->additional([
            'status' => 'success',
            'message' => 'News created successfully',
        ]);
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
    public function update(NewsRequest $request, $id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'status' => 'error',
                'message' => 'News not found',
            ], 404);
        }

        $data = $request->validated();

        if (isset($data['title']['en'])) {
            $data['slug'] = Str::slug($data['title']['en']);
        }

        $news->update($data);

        return (new NewsResource($news))->additional([
            'status' => 'success',
            'message' => 'News updated successfully',
        ]);
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

        return NewsResource::collection($news)->additional([
            'status' => 'success',
        ]);
    }

    /**
     * Get "You May Like" news - 8 latest news items.
     */
    public function youMayLike()
    {
        $news = News::latest()
            ->limit(8)
            ->get();

        return NewsResource::collection($news)->additional([
            'status' => 'success',
            'message' => 'You may like these news',
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

        return NewsResource::collection($news)->additional([
            'status' => 'success',
            'message' => 'Related news',
        ]);
    }
}
