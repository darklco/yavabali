<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    /**
     * Display a listing of the videos.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $videos = Video::latest()->paginate(10);
        
        return VideoResource::collection($videos)
            ->additional(['status' => 'success']);
    }

    /**
     * Store a newly created video.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Resources\VideoResource
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:podcast,testimonial',
            'type_url' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'published_at' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        $video = Video::create($request->all());

        return new VideoResource($video);
    }

    /**
     * Display the specified video.
     *
     * @param  string  $id
     * @return \App\Http\Resources\VideoResource
     */
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

    /**
     * Update the specified video.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \App\Http\Resources\VideoResource
     */
    public function update(Request $request, $id)
    {
        $video = Video::find($id);
        
        if (!$video) {
            return response()->json([
                'status' => 'error',
                'message' => 'Video not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:podcast,testimonial',
            'type_url' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'published_at' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        $video->update($request->all());

        return new VideoResource($video);
    }

    /**
     * Remove the specified video.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Get videos by type.
     *
     * @param  string  $type
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getByType($type)
    {
        $videos = Video::ofType($type)->latest()->paginate(10);
        
        return VideoResource::collection($videos)
            ->additional(['status' => 'success']);
    }

    /**
     * Get recommended videos - 8 latest videos.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function recommended()
    {
        $videos = Video::latest()->limit(8)->get();
        
        return VideoResource::collection($videos)
            ->additional([
                'status' => 'success',
                'message' => 'Recommended videos'
            ]);
    }

    /**
     * Get recommended videos by type - 8 latest videos of a specific type.
     *
     * @param  string  $type
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function recommendedByType($type)
    {
        $videos = Video::ofType($type)->latest()->limit(8)->get();
        
        return VideoResource::collection($videos)
            ->additional([
                'status' => 'success',
                'message' => "Recommended {$type} videos"
            ]);
    }

    /**
     * Restore a soft-deleted video.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Get videos with specific tags.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getByTags(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tags' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        $tags = $request->tags;
        
        // This is a simplified approach. In a production environment,
        // you might want to use a more sophisticated query for JSON arrays
        $videos = Video::where(function($query) use ($tags) {
            foreach ($tags as $tag) {
                $query->orWhereJsonContains('tags', $tag);
            }
        })->latest()->paginate(10);
        
        return VideoResource::collection($videos)
            ->additional(['status' => 'success']);
    }
}