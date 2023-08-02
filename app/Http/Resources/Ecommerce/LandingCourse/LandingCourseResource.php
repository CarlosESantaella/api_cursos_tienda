<?php

namespace App\Http\Resources\Ecommerce\LandingCourse;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingCourseResource extends JsonResource
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

        if ($this->resource->discount_c && $this->resource->discount_ct) { // si tiene descuento a nivel de curso y a nivel de categoría
            $discount_g = $this->resource->discount_ct; //descuento de tipo curso
        } else {
            if ($this->resource->discount_c && !$this->resource->discount_ct) { //tiene descuento a nivel de curso y no a nivel de categoría
                $discount_g = $this->resource->discount_c;
            } else {
                if (!$this->resource->discount_c && $this->resource->discount_ct) { // no tiene descuento a nivel de curso pero si a nivel de categoría
                    $discount_g = $this->resource->discount_ct; // descuento de tipo categoría
                }
            }
        }

        return [
            "id" => $this->resource->id,
            "title" => $this->resource->title,
            "subtitle" => $this->resource->subtitle,
            "categorie_id" => $this->resource->categorie_id,
            "categorie" => [
                "id" => $this->resource->categorie->id,
                "name" => $this->resource->categorie->name
            ],
            "sub_categorie_id" => $this->resource->sub_categorie->id,
            "sub_categorie" => [
                "id" => $this->resource->sub_categorie->id,
                "name" => $this->resource->sub_categorie->name
            ],
            "level" => $this->resource->level,
            "idioma" => $this->resource->idioma,
            "vimeo_id" => $this->resource->vimeo_id ? 'https://player.vimeo.com/video/' . $this->resource->vimeo_id : NULL,
            "time" => $this->resource->time,
            "description" => $this->resource->description,
            "requirements" => json_decode($this->resource->requirements),
            "who_is_it_for" => json_decode($this->resource->who_is_it_for),
            "imagen" => env("APP_URL") . "storage/" . $this->resource->imagen,
            "precio_usd" => $this->resource->precio_usd,
            "precio_pen" => $this->resource->precio_pen,
            "count_class" => $this->resource->count_class,
            "time_course" => $this->resource->time_course,
            "files_count" => $this->resource->files_count,
            "count_students" => $this->resource->count_students,
            "avg_reviews" => $this->resource->avg_reviews ? round($this->resource->avg_reviews) : 0,
            "count_reviews" => $this->resource->count_reviews,
            "discount_g" => $discount_g,
            "discount_date" => $discount_g ? Carbon::parse($discount_g->end_date)->format('d/m') : NULL,
            "instructor" => $this->resource->instructor ? [
                "id" => $this->resource->instructor->id,
                "full_name" => $this->resource->instructor->name . ' ' . $this->resource->instructor->surname,
                "avatar" => env("APP_URL") . "storage/" . $this->resource->instructor->avatar,
                "profesion" => $this->resource->instructor->profesion,
                "courses_count" => $this->resource->instructor->courses_count,
                "description" => $this->resource->instructor->description,
                "avg_reviews" => round($this->resource->instructor->avg_reviews),
                "count_reviews" => $this->resource->instructor->count_reviews,
                "count_students" => $this->resource->instructor->count_students ?? 0,
            ] : NULL,
            //malla curricular
            "malla" => $this->resource->sections->map(function ($section) {
                return [
                    "id" => $section->id,
                    "name" => $section->name,
                    "time_section" => $section->time_section,
                    "clases" => $section->clases->map(function ($clase) {
                        return [
                            "id" => $clase->id,
                            "name" => $clase->name,
                            "time_clase" => $clase->time_clase,
                            "vimeo" => $clase->vimeo_id ? 'https://player.vimeo.com/video/'.$clase->vimeo_id : NULL,
                            "files" => $clase->files->map(function($file){
                                return [
                                    "name" => $file->name,
                                    "url" => env("APP_URL")."storage/".$file->file,
                                    "size" => $file->size,
                                ];
                            })
                        ];
                    }),
                ];
            }),
            "reviews" => $this->resource->reviews->map(function ($review) {
                return [
                    "message" => $review->message,
                    "rating" => $review->rating,
                    "user" => [
                        "full_name" => $review->user->name . ' ' . $review->user->surname,
                        "avatar" => env("APP_URL") . "storage/" . $review->user->avatar
                    ]
                ];
            }),
            "update_at" => $this->resource->updated_at->format("m/Y"),
        ];
    }
}
