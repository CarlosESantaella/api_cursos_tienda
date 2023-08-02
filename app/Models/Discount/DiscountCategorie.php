<?php

namespace App\Models\Discount;

use Carbon\Carbon;
use App\Models\Course\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountCategorie extends Model
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

    public function categorie()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'course_id');
    }
}
