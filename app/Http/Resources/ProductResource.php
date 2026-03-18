<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'variant_name' => $this->variant_name,
            'description' => $this->description,
            'image' => $this->image,
            'barcode' => $this->barcode,
            'price' => $this->price,
            'quantity' => $this->total_quantity,
            'status' => $this->status,
            'category' => $this->category,
            'created_at' => $this->created_at,
            'image_url' => $this->getImageUrl(),
            'variants' => ProductResource::collection($this->whenLoaded('variants')),
        ];
    }
}
