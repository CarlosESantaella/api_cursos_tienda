<?php

namespace App\Http\Resources\Ecommerce\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            "user_id" => $this->resource->user_id,
            "course_id" => $this->resource->course_id,
            "course" => [
                "title" => $this->resource->course->title,
                "imagen" => env("APP_URL")."storage/".$this->resource->course->imagen,
                "subtitle" => $this->resource->course->subtitle,
            ],
            "type_discount" => $this->resource->type_discount,
            "discount" => $this->resource->discount,
            "code_coupon" => $this->resource->code_coupon,
            "code_discount" => $this->resource->code_discount,
            "type_campaing" => $this->resource->type_campaing,
            "precio_unitario" => $this->resource->precio_unitario,
            "total" => $this->resource->total,
        ];
    }
}
