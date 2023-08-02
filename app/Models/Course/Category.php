<?php

namespace App\Models\Course;

use App\Models\Discount\DiscountCategorie;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
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

    public function children()
    {
        return $this->hasMany(Category::class, "category_id", "id");
    }

    public function father()
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'categorie_id');
    }

    public function discount_categories()
    {
        return $this->hasMany(DiscountCategorie::class, 'categorie_id');
    }

    function scopeFilterAdvance($query, $search, $state)
    {
        if($search){
            $query->where("name", "like", "%".$search."%");
        }
        if($state){
            $query->where("state", $state);
        }

        return $query;
    }
}
