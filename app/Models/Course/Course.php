<?php

namespace App\Models\Course;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Sale\Review;
use App\Models\CoursesStudent;
use App\Models\Discount\DiscountCourse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    // protected $appends = ['count_class'];


    protected function createdAt(): Attribute
    {
        return Attribute::make(
            set: function (mixed $value, array $attributes) {
                date_default_timezone_set("America/Lima");
                return $attributes["create_at"] = Carbon::now();
            }
        );
    }

    protected function updateAt(): Attribute
    {
        return Attribute::make(
            set: function (mixed $value, array $attributes) {
                date_default_timezone_set("America/Lima");
                return $attributes["update_at"] = Carbon::now();
            }
        );
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }
    public function sub_categorie()
    {
        return $this->belongsTo(Category::class, 'sub_categorie_id');
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class);
    }

    public function discount_courses()
    {
        return $this->hasMany(DiscountCourse::class, 'course_id');
    }

    public function courses_students()
    {
        return $this->hasMany(CoursesStudent::class, 'course_id');
    }

    public function discountC()
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                date_default_timezone_set("America/Lima");
                $discount = null;
                foreach ($this->discount_courses as $dc) {
                    if ($dc->discount->type_campaing == 1 && $dc->discount->state == 1) {
                        if (Carbon::now()->between($dc->discount->start_date, Carbon::parse($dc->discount->end_date)->addDays(1))) {
                            // Existe una campaña de descuento con el curso
                            $discount = $dc->discount;
                            break;
                        }
                    }
                }
                return $discount;
            }
        );
    }

    public function discountCt()
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                date_default_timezone_set("America/Lima");
                $discount = null;
                foreach ($this->categorie->discount_categories as $dc) {
                    if ($dc->discount->type_campaing == 1 && $dc->discount->state == 1) {
                        if (Carbon::now()->between($dc->discount->start_date, Carbon::parse($dc->discount->end_date)->addDays(1))) {
                            // Existe una campaña de descuento con el curso
                            $discount = $dc->discount;
                            break;
                        }
                    }
                }
                return $discount;
            }
        );
    }

    public function countReviews(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->reviews->count(),
        );
    }

    public function avgReviews(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->reviews?->avg("rating"),
        );
    }

    public function filesCount(): Attribute
    {
        return Attribute::make(
            get: function () {
                $files_count = 0;

                foreach($this->sections as $section){
                    foreach($section->clases as $clase){
                        $files_count += $clase->files->count();
                    }
                }
                return $files_count;
            },
        );
    }

    public function countClass(): Attribute
    {
        return Attribute::make(
            get: function () {
                $num = 0;

                foreach ($this->sections as $section) {
                    $num += $section->clases->count();
                }

                return $num;
            }
        );
    }

    public function timeCourse(): Attribute
    {
        return Attribute::make(
            get: function () {
                $times = [];

                foreach ($this->sections as $section) {
                    foreach ($section->clases as $clase) {
                        array_push($times, $clase->time);
                    }
                }

                return $this->AddTimes($times);
            }
        );
    }

    public function countStudents()
    {
        return Attribute::make(
            get: fn () => $this->courses_students->count(),
        );
    }

    function AddTimes($horas)
    {
        $total = 0;
        foreach ($horas as $h) {
            $parts = explode(":", $h);
            $total += $parts[2] + $parts[1] * 60 + $parts[0] * 3600;
        }
        $hours = floor($total / 3600);
        $minutes = floor(($total / 60) % 60);
        $seconds = $total % 60;

        return $hours . " hrs " . $minutes . " mins";
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    function scopeFilterAdvance($query, $search, $state)
    {
        if ($search) {
            $query->where("title", "like", "%" . $search . "%");
        }
        if ($state) {
            $query->where("state", $state);
        }

        return $query;
    }

    function scopeFilterAdvanceEcommerce($query, $search, $selected_categories = [], $instructores_selected = [], $min_price = 0, $max_price = 0, $idiomas_selected = [], $levels_selected = [], $rating_selected = 0, $courses_a = [])
    {
        if ($search) {
            $query->where("title", "like", "%" . $search . "%");
        }

        if(sizeof($selected_categories) > 0){
            $query->whereIn('categorie_id', $selected_categories);
        }

        if(sizeof($instructores_selected) > 0){
            $query->whereIn('user_id', $instructores_selected);
        }

        if($min_price > 0 && $max_price > 0){
            $query->whereBetween("precio_usd", [$min_price, $max_price]);
        }

        if(sizeof($idiomas_selected) > 0){
            $query->whereIn('idioma', $idiomas_selected);
        }

        if(sizeof($levels_selected) > 0){
            $query->whereIn('level', $levels_selected);
        }

        if($courses_a || $rating_selected){
            $query->whereIn('id', $courses_a);
        }

        return $query;
    }
}
