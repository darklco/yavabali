<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\VideoRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::latest()->paginate(10);
        return VideoResource::collection($videos)
            ->additional(['status' => 'success']);
    }

    public function store(VideoRequest $request)
    {
        $video = Video::create($request->validated());
        return new VideoResource($video);
    }

    public function show($id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json([
                'status' => 'error',
                'message' => 'Video not found',
            ], 404);
        }

        return new VideoResource($video);
    }

    public function update(VideoRequest $request, $id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json([
                'status' => 'error',
                'message' => 'Video not found',
            ], 404);
        }

        $video->update($request->validated());

        return new VideoResource($video);
    }

    public function destroy($id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json([
                'status' => 'error',
                'message' => 'Video not found',
            ], 404);
        }

        $video->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Video deleted successfully',
        ]);
    }

    public function getByType($type)
    {
        $videos = Video::ofType($type)->latest()->paginate(10);
        return VideoResource::collection($videos)
            ->additional(['status' => 'success']);
    }

    public function recommended()
    {
        $videos = Video::latest()->limit(8)->get();
        return VideoResource::collection($videos)
            ->additional([
                'status' => 'success',
                'message' => 'Recommended videos',
            ]);
    }

    public function recommendedByType($type)
    {
        $videos = Video::ofType($type)->latest()->limit(8)->get();
        return VideoResource::collection($videos)
            ->additional([
                'status' => 'success',
                'message' => "Recommended {$type} videos",
            ]);
    }

    public function restore($id)
    {
        $video = Video::withTrashed()->find($id);

        if (!$video) {
            return response()->json([
                'status' => 'error',
                'message' => 'Video not found',
            ], 404);
        }

        if (!$video->trashed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Video is not deleted',
            ], 400);
        }

        $video->restore();

        return response()->json([
            'status' => 'success',
            'message' => 'Video restored successfully',
        ]);
    }

    public function getByTags(Request $request)
    {
        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'string'
        ]);

        $tags = $request->tags;

        $videos = Video::where(function ($query) use ($tags) {
            foreach ($tags as $tag) {
                $query->orWhereJsonContains('tags', $tag);
            }
        })->latest()->paginate(10);

        return VideoResource::collection($videos)
            ->additional(['status' => 'success']);
    }
}
