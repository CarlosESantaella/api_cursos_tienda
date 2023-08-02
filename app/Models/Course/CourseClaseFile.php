<?php

namespace App\Models\Course;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseClaseFile extends Model
{
    use HasFactory;

    protected $guarded = [];

    
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            set: function (mixed $value, array $attributes){
                date_default_timezone_set("America/Lima");
                return $attributes["create_at"] = Carbon::now();
            }
        );
    }

    protected function updateAt(): Attribute
    {
        return Attribute::make(
            set: function (mixed $value, array $attributes){
                date_default_timezone_set("America/Lima");
                return $attributes["update_at"] = Carbon::now();
            }
        );
    }

    protected function size(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes){
                $size = (int)$value;
                $base = log($size) / log(1024);
                $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
                return round(pow(1024, $base - floor($base)), 2) . $suffixes[floor($base)];

            } 
        );
    }

}
