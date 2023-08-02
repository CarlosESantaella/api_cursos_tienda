<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Role;
use App\Models\Course\Course;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'surname',
        'email',
        'password',
        'avatar',
        'role_id',
        'state', // 1 es activo y 2 es inactivo
        'type_user', // 1 es de tipo cliente y 2 es de tipo admin
        'profesion',
        'is_instructor',
        'description',
        'phone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

         /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function coursesCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->courses->count(),
        );
    }

    public function avgReviews(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->courses->avg("avg_reviews"),
        );
    }

    public function countReviews(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->courses->sum("avg_reviews"),
        );
    }

    public function countStudents(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->courses->sum("count_students"),
        );
    }

    function scopeFilterAdvance($query, $search, $state)
    {
        if($search){
            $query->where("email", "like", "%".$search."%");
        }
        if($state){
            $query->where("state", $state);
        }

        return $query;
    }
}
