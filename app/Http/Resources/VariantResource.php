<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariantResource extends JsonResource
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
            'product_id' => $this->product_id,
            'size' => $this->size,
            'image_front' => $this->image_front,
            'image_back' => $this->image_back,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'ecommerces' => EcommerceResource::collection($this->whenLoaded('ecommerces')),
        ];
    }
}