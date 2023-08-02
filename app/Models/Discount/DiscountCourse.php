<?php

namespace App\Models\Discount;

use Carbon\Carbon;
use App\Models\Course\Course;
use App\Models\Discount\Discount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountCourse extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            set: function(mixed $value, array $attributes){
                date_default_timezone_set("America/Lima");
                $this->attributes["created_at"] = Carbon::now();
            }
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            set: function(mixed $value, array $attributes){
                date_default_timezone_set("America/Lima");
                $this->attributes["updated_at"] = Carbon::now();
            }
        );
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'course_id');
    }
}
