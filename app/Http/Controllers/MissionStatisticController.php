<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MissionStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MissionStatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $missionStatistics = MissionStatistic::all();
        
        return response()->json([
            'success' => true,
            'data' => $missionStatistics
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'total_raw_materials' => 'required|integer|min:0',
            'total_employees' => 'required|integer|min:0',
            'female_leaders_percentage' => 'required|numeric|min:0|max:100',
            'female_workers_percentage' => 'required|numeric|min:0|max:100',
            'glycemic_index' => 'required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $missionStatistic = MissionStatistic::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mission statistic created successfully',
            'data' => $missionStatistic
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $missionStatistic = MissionStatistic::find($id);

        if (!$missionStatistic) {
            return response()->json([
                'success' => false,
                'message' => 'Mission statistic not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $missionStatistic
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $missionStatistic = MissionStatistic::find($id);

        if (!$missionStatistic) {
            return response()->json([
                'success' => false,
                'message' => 'Mission statistic not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'total_raw_materials' => 'sometimes|required|integer|min:0',
            'total_employees' => 'sometimes|required|integer|min:0',
            'female_leaders_percentage' => 'sometimes|required|numeric|min:0|max:100',
            'female_workers_percentage' => 'sometimes|required|numeric|min:0|max:100',
            'glycemic_index' => 'sometimes|required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $missionStatistic->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mission statistic updated successfully',
            'data' => $missionStatistic
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $missionStatistic = MissionStatistic::find($id);

        if (!$missionStatistic) {
            return response()->json([
                'success' => false,
                'message' => 'Mission statistic not found'
            ], 404);
        }

        $missionStatistic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mission statistic deleted successfully'
        ]);
    }

    /**
     * Get statistics by year.
     *
     * @param  int  $year
     * @return \Illuminate\Http\Response
     */
    public function getByYear($year)
    {
        $missionStatistics = MissionStatistic::where('year', $year)->get();

        if ($missionStatistics->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No mission statistics found for the specified year'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $missionStatistics
        ]);
    }
}


