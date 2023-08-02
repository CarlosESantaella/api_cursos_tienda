<?php

namespace App\Models\Coupon;

use Carbon\Carbon;
use App\Models\Coupon\CouponCourse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];



    public function createdAt(): Attribute
    {
        return Attribute::make(
            set: function (mixed $value, array $attributes){
                date_default_timezone_set("America/Lima");
                $this->attributes["created_at"] = Carbon::now();
            }
        );
    }
    public function updatedAt(): Attribute
    {
        return Attribute::make(
            set: function (mixed $value, array $attributes){
                date_default_timezone_set("America/Lima");
                $this->attributes["updated_at"] = Carbon::now();
            }
        );
    }

    public function courses()
    {
        return $this->hasMany(CouponCourse::class, 'coupon_id');
    }

    public function categories()
    {
        return $this->hasMany(CouponCategory::class, 'coupon_id');
    }

    function scopeFilterAdvance($query, $search, $state)
    {
        if($search){
            $query->where("code", "like", "%".$search."%");
        }
        if($state){
            $query->where("state", $state);
        }
        return $query;
    }

}
