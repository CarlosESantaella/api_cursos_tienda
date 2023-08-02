<?php

namespace App\Models\Discount;

use Carbon\Carbon;
use App\Models\Coupon\CouponCourse;
use App\Models\Coupon\CouponCategory;
use App\Models\Discount\DiscountCourse;
use Illuminate\Database\Eloquent\Model;
use App\Models\Discount\DiscountCategorie;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
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
        return $this->hasMany(DiscountCourse::class, 'discount_id');
    }

    public function categories()
    {
        return $this->hasMany(DiscountCategorie::class, 'discount_id');
    }

    function scopeFilterAdvance($query, $state)
    {
        // if($search){
        //     $query->where("code", "like", "%".$search."%");
        // }
        if($state){
            $query->where("state", $state);
        }
        return $query;
    }
}
