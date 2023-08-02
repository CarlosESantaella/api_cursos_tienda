<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use App\Http\Resources\Course\CourseGResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CourseGCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "data" => CourseGResource::collection($this->collection)
        ];
    }
}
