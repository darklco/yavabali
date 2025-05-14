<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Display a listing of settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $settings = Setting::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }

    /**
     * Store a newly created setting
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:settings',
            'value' => 'nullable',
            'description' => 'nullable|string',
            'type' => 'nullable|string|in:string,integer,float,boolean,array,json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validasi tambahan berdasarkan tipe data
        if ($request->filled('type') && $request->filled('value')) {
            if (!$this->validateValueByType($request->value, $request->type)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => ['value' => ['Value does not match the specified type']]
                ], 422);
            }
        }

        $setting = new Setting();
        $setting->title = $request->title;
        $setting->key = $request->key;
        $setting->value = $this->formatValueByType($request->value, $request->type ?? 'string');
        $setting->description = $request->description;
        $setting->type = $request->type ?? 'string';
        $setting->created_by = Auth::check() ? Auth::id() : null;
        $setting->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Setting created successfully',
            'data' => $setting
        ], 201);
    }

    /**
     * Display the specified setting
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $setting = Setting::findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $setting
        ]);
    }

    /**
     * Get setting by key
     *
     * @param  string  $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByKey($key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();
        
        return response()->json([
            'status' => 'success',
            'data' => $setting
        ]);
    }

    /**
     * Update the specified setting
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'key' => 'sometimes|string|max:255|unique:settings,key,' . $id,
            'value' => 'nullable',
            'description' => 'nullable|string',
            'type' => 'nullable|string|in:string,integer,float,boolean,array,json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validasi tambahan berdasarkan tipe data
        $type = $request->type ?? $setting->type;
        if ($request->filled('value')) {
            if (!$this->validateValueByType($request->value, $type)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => ['value' => ['Value does not match the specified type']]
                ], 422);
            }
        }

        if ($request->has('title')) {
            $setting->title = $request->title;
        }
        
        if ($request->has('key')) {
            $setting->key = $request->key;
        }
        
        if ($request->has('value')) {
            $setting->value = $this->formatValueByType($request->value, $type);
        }
        
        if ($request->has('description')) {
            $setting->description = $request->description;
        }
        
        if ($request->has('type')) {
            $setting->type = $request->type;
        }
        
        $setting->updated_by = Auth::check() ? Auth::id() : null;
        $setting->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Setting updated successfully',
            'data' => $setting
        ]);
    }

    /**
     * Remove the specified setting
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $setting = Setting::findOrFail($id);
        
        $setting->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Setting deleted successfully'
        ]);
    }

    /**
     * Validate value based on type
     *
     * @param  mixed  $value
     * @param  string  $type
     * @return bool
     */
    private function validateValueByType($value, $type)
    {
        switch ($type) {
            case 'string':
                return is_string($value);
            case 'integer':
                return is_numeric($value) && (int)$value == $value;
            case 'float':
                return is_numeric($value);
            case 'boolean':
                return is_bool($value) || in_array(strtolower($value), ['true', 'false', '0', '1', 0, 1]);
            case 'array':
                return is_array($value) || (is_string($value) && is_array(json_decode($value, true)));
            case 'json':
                if (!is_string($value)) {
                    return false;
                }
                json_decode($value);
                return json_last_error() === JSON_ERROR_NONE;
            default:
                return true;
        }
    }

    /**
     * Format value based on type
     *
     * @param  mixed  $value
     * @param  string  $type
     * @return mixed
     */
    private function formatValueByType($value, $type)
    {
        if ($value === null) {
            return null;
        }

        switch ($type) {
            case 'string':
                return (string)$value;
            case 'integer':
                return (int)$value;
            case 'float':
                return (float)$value;
            case 'boolean':
                if (is_bool($value)) {
                    return $value;
                }
                if (in_array(strtolower($value), ['true', '1', 1])) {
                    return true;
                }
                return false;
            case 'array':
                if (is_array($value)) {
                    return json_encode($value);
                }
                return $value; // Assume it's already a JSON string
            case 'json':
                if (is_array($value)) {
                    return json_encode($value);
                }
                return $value; // Assume it's already a JSON string
            default:
                return $value;
        }
    }
}