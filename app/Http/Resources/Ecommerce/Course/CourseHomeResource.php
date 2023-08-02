<?php

namespace App\Http\Resources\Ecommerce\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseHomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //Es la campaña de descuento con la que esta relacionada
        $discount_g = null;

        if($this->resource->discount_c && $this->resource->discount_ct){ // si tiene descuento a nivel de curso y a nivel de categoría
           $discount_g = $this->resource->discount_ct;//descuento de tipo curso
        }else{
            if($this->resource->discount_c && !$this->resource->discount_ct){ //tiene descuento a nivel de curso y no a nivel de categoría
                $discount_g = $this->resource->discount_c;
            }else{
                    if(!$this->resource->discount_c && $this->resource->discount_ct){ // no tiene descuento a nivel de curso pero si a nivel de categoría
                        $discount_g = $this->resource->discount_ct; // descuento de tipo categoría
                    }
            }
        }

        return [
            "id" => $this->resource->id,
            "title" => $this->resource->title,
            "slug" => $this->resource->slug,
            "subtitle" => $this->resource->subtitle,
            "imagen" => env("APP_URL")."storage/".$this->resource->imagen,
            "precio_usd" => $this->resource->precio_usd,
            "precio_pen" => $this->resource->precio_pen,
            "count_class" => $this->resource->count_class,
            "time_course" => $this->resource->time_course,
            "discount_g" => $discount_g,
            "count_students" => $this->resource->count_students,
            "avg_reviews" => $this->resource->avg_reviews ? round($this->resource->avg_reviews) : 0,
            "count_reviews" => $this->resource->count_reviews,

            "instructor" => $this->resource->instructor ? [
                "id" => $this->resource->instructor->id,
                "full_name" => $this->resource->instructor->name.' '.$this->resource->instructor->surname,
                "avatar" => env("APP_URL")."storage/".$this->resource->instructor->avatar,
                "profesion" => $this->resource->instructor->profesion,
            ] : NULL,
        ];
    }
}
