<?php

namespace App\Models\Course;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseClase extends Model
{
    use HasFactory, SoftDeletes;

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

    public function course_section()
    {
        return $this->belongsTo(CourseSection::class);
    }

    // public function section()
    // {
    //     return $this->belongsTo(CourseSection::class, 'course_section_id');
    // }
    public function files()
    {
        return $this->hasMany(CourseClaseFile::class, 'course_clase_id');
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

    public function timeClase(): Attribute
    {
        return Attribute::make(
            get: function () {
                $times = [];
                array_push($times, $this->time);
                return $this->AddTimes($times);
            }
        );
    }
}
