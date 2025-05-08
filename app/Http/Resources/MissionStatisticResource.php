<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MissionStatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'total_raw_materials' => $this->total_raw_materials,
            'total_employees' => $this->total_employees,
            'female_leaders_percentage' => $this->female_leaders_percentage,
            'female_workers_percentage' => $this->female_workers_percentage,
            'glycemic_index' => $this->glycemic_index,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

