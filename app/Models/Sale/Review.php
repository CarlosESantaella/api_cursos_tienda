<?php

namespace App\Models\Sale;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course\Course;
use App\Models\Sale\SaleDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
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
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sale_detail()
    {
        return $this->belongsTo(SaleDetail::class);
    }
}
