<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'types' => $this->types,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'nutrient' => $this->nutrient,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'variants' => VariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}