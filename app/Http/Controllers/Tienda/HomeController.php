<?php

namespace App\Http\Controllers\Tienda;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Models\CoursesStudent;
use App\Models\Course\Category;
use App\Models\Discount\Discount;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Ecommerce\Course\CourseHomeResource;
use App\Http\Resources\Ecommerce\Course\CourseHomeCollection;
use App\Http\Resources\Ecommerce\LandingCourse\LandingCourseResource;

class HomeController extends Controller
{
    public function home(Request $request)
    {

        $categories = Category::where("categorie_id", NULL)->withCount("courses")->orderBy("id", "desc")->get();

        $courses = Course::where("state", 2)->inRandomOrder()->limit(3)->get();

        $categorie_courses = Category::where("categorie_id", NULL)->withCount("courses")->having("courses_count", ">", 0)->orderBy("id", "desc")->take(5)->get();

        $group_courses_categories = collect([]);

        foreach ($categorie_courses as $category_course) {
            $group_courses_categories->push([
                "id" => $category_course->id,
                "name" => $category_course->name,
                "name_empty" => str_replace(" ", "", $category_course->name),
                "courses_count" => $category_course->courses_count,
                "courses" => CourseHomeCollection::make($category_course->courses),
            ]);
        }

        date_default_timezone_set("America/Lima");
        $DISCOUNT_BANNER = Discount::where([
            ['type_campaing', 3],
            ['state', 1],
            ['start_date', "<=", Carbon::now()],
            ['end_date', ">=", Carbon::now()],
        ])->first();

        $DISCOUNT_BANNER_COURSES = collect([]);

        if ($DISCOUNT_BANNER) {
            foreach ($DISCOUNT_BANNER->courses as $course_discount) {
                $DISCOUNT_BANNER_COURSES->push(CourseHomeResource::make($course_discount->course));
            }
        }


        date_default_timezone_set("America/Lima");
        $DISCOUNT_FLASH = Discount::where([
            ['type_campaing', 2],
            ['state', 1],
            ['start_date', "<=", Carbon::now()],
            ['end_date', ">=", Carbon::now()],
        ])->first();

        $DISCOUNT_FLASH_COURSES = collect([]);

        if ($DISCOUNT_FLASH) {
            $DISCOUNT_FLASH->end_date = Carbon::parse($DISCOUNT_FLASH->end_date)->addDays(1);
            foreach ($DISCOUNT_FLASH->courses as $course_discount) {
                $DISCOUNT_FLASH_COURSES->push(CourseHomeResource::make($course_discount->course));
            }
        }



        return response()->json([
            "categories" => $categories->map(function ($categorie) {
                return [
                    "id" => $categorie->id,
                    "name" => $categorie->name,
                    "imagen" => env("APP_URL") . "storage/" . $categorie->imagen,
                    "courses_count" => $categorie->courses_count,
                ];
            }),
            "courses" => CourseHomeCollection::make($courses),
            "group_courses_categories" => $group_courses_categories,
            "DISCOUNT_BANNER" => $DISCOUNT_BANNER,
            "DISCOUNT_BANNER_COURSES" => $DISCOUNT_BANNER_COURSES,
            "DISCOUNT_FLASH" => $DISCOUNT_FLASH ? [
                "id" => $DISCOUNT_FLASH->id,
                "discount" => $DISCOUNT_FLASH->discount,
                "code" => $DISCOUNT_FLASH->code,
                "type_campaing" => $DISCOUNT_FLASH->type_campaing,
                "type_discount" => $DISCOUNT_FLASH->type_discount,
                "end_date" => Carbon::parse($DISCOUNT_FLASH->end_date)->format('Y-m-d'),
                "start_date_d" => Carbon::parse($DISCOUNT_FLASH->start_date)->format('Y/m/d'),
                "end_date_d" => Carbon::parse($DISCOUNT_FLASH->end_date)->subDays(1)->format('Y/m/d'),
            ] : NULL,
            "DISCOUNT_FLASH_COURSES" => $DISCOUNT_FLASH_COURSES,
        ]);
    }

    public function course_detail(Request $request, $slug)
    {
        $campaing_discount = $request->get("campaing_discount");
        $discount = null;
        if ($campaing_discount) {
            $discount = Discount::findOrFail($campaing_discount);
        }
        $course = Course::where("slug", $slug)->first();

        $have_course = false;


        if (!$course) {
            return abort(404);
        }
        error_log(Auth::check() . 'hola mundo');
        if (Auth::check()) {
            $course_student = CoursesStudent::where("user_id", Auth::user()->id)->where("course_id", $course->id)->first();
            if ($course_student) {
                $have_course = true;
            }
        }

        $courses_related_instructor = Course::where("id", '<>', $course->id)->where("user_id", $course->user_id)->inRandomOrder()->take(2)->get();

        $courses_related_categories = Course::where("id", '<>', $course->id)->where("categorie_id", $course->categorie_id)->inRandomOrder()->take(3)->get();

        return response()->json([
            "course" => LandingCourseResource::make($course),
            "courses_related_instructor" => $courses_related_instructor->map(function ($course) {
                return CourseHomeResource::make($course);
            }),
            "courses_related_categories" => $courses_related_categories->map(function ($course) {
                return CourseHomeResource::make($course);
            }),
            "DISCOUNT" => $discount,
            "have_course" => $have_course
        ]);
    }

    public function course_leason($slug)
    {
        $course = Course::where("slug", trim($slug))->first();

        if (!$course) {
            return response()->json([
                "message" => 403,
                "message_text" => "EL CURSO NO EXISTE",
            ]);
        }

        $course_student = CoursesStudent::where("course_id", $course->id)
            ->where("user_id", Auth::user()->id)
            ->first();

        if (!$course_student) {
            return response()->json([
                "message" => 403,
                "message_text" => "NO ESTAS INSCRITO EN ESTE CURSO",
            ]);
        }

        return response()->json([
            "course" => LandingCourseResource::make($course),
        ]);
    }

    public function listCourses(Request $request)
    {
        $search = $request->search;
        $selected_categories = $request->selected_categories ?? [];
        $instructores_selected = $request->instructores_selected ?? [];
        $min_price = $request->min_price;
        $max_price = $request->max_price;

        $idiomas_selected = $request->idiomas_selected ?? [];
        $levels_selected = $request->levels_selected ?? [];

        $rating_selected = $request->rating_selected ?? [];

        $courses_a = [];

        if ($rating_selected) {
            $rating_selected = floor($rating_selected);
            $courses_query = Course::where("state", 2)
                ->join("reviews", "reviews.course_id", "=", "courses.id")
                ->select("courses.id as courseId", DB::raw("AVG(reviews.rating) as rating_reviews"))
                ->groupBy("courseId")
                ->having("rating_reviews", ">=", $rating_selected)
                ->having("rating_reviews", "<", $rating_selected + 1)
                ->get();

            $courses_a = $courses_query->pluck("courseId")->toArray();
        }

        // if (!$search) {
        //     return response()->json([
        //         "courses" => []
        //     ]);
        // }

        $courses = Course::filterAdvanceEcommerce(
            $search,
            $selected_categories,
            $instructores_selected,
            $min_price,
            $max_price,
            $idiomas_selected,
            $levels_selected,
            $rating_selected,
            $courses_a,
        )
            ->orderBy("id", "desc")->get();

        return response()->json([
            "courses" => CourseHomeCollection::make($courses)
        ]);
    }

    public function config_all()
    {
        $categories = Category::where("categorie_id", NULL)
            ->withCount("courses")
            ->orderBy("id", "desc")
            ->get();

        $instructores = User::where("is_instructor", 1)->orderBy("id", "desc")->get();

        return response()
            ->json([
                "categories" => $categories,
                "instructores" => $instructores->map(function ($user) {
                    return [
                        "id" => $user->id,
                        "courses_count" => $user->courses_count,
                        "full_name" => $user->name . ' ' . $user->surname
                    ];
                }),
                "levels" => [
                    "Básico",
                    "Intermedio",
                    "Avanzado"
                ],
                "idiomas" => [
                    "Español",
                    "Ingles",
                    "Portugues"
                ],

            ])
            ->withHeaders([
                "Status", 200
            ]);
    }
}
