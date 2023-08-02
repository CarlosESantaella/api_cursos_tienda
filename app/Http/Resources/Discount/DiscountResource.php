<?php

namespace App\Http\Resources\Discount;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
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
            "code" => $this->resource->code,
            "type_discount" => $this->resource->type_discount,
            "discount" => $this->resource->discount,
            "start_date" => Carbon::parse($this->resource->start_date)->format("Y-m-d"),
            "end_date" => Carbon::parse($this->resource->end_date)->format("Y-m-d"),
            "discount_type" => $this->resource->discount_type,
            "type_campaing" => $this->resource->type_campaing,
            "state" => $this->resource->state,
            "courses" => $this->resource->courses?->map(function ($course) {
                return [
                    "id" => $course->course->id,
                    "title" => $course->course->title,
                    "imagen" => env("APP_URL") . "storage/" . $course->course->imagen,
                    "aux_id" => $course->id,
                ];
            }),
            "categories" => $this->resource->categories?->map(function ($category) {
                return [
                    "id" => $category->categorie->id,
                    "name" => $category->categorie->name,
                    "imagen" => env("APP_URL") . "storage/" . $category->categorie->imagen,
                    "aux_id" => $category->id,
                ];
            }),
        ];
    }
}
