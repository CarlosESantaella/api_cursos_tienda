<?php

namespace App\Http\Resources\Course\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->resource->id,
            "name" => $this->resource->name,
            "imagen" => $this->resource->imagen ? env("APP_URL")."storage/".$this->resource->imagen : NULL,
            "category_id" => $this->resource->category_id,
            "categorie" => $this->resource->father ? [
                "name" => $this->resource->father->name,
                "imagen" => $this->resource->father->imagen ? env("APP_URL")."storage/".$this->resource->father->imagen : NULL
            ] : NULL,
            "state" => $this->resource->state ?? 1,
        ];
    }
}
