<?php

namespace App\Models\Sale;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sale_details()
    {
        return $this->hasMany(SaleDetail::class);
    }
}
