<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Controller;
use App\Models\Course\CourseSection;
use Illuminate\Http\Request;

class SeccionGController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sections = CourseSection::withCount("clases")->where("course_id". $request->course_id)->orderBy("id", "desc")->get();

        return response()->json(["sections" => $sections]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $section = CourseSection::create($request->all());

        return response()->json([
            "section" => $section
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $section = CourseSection::findOrFail($id);
        $section->update($request->all());

        return response()->json([
            "section" => $section
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $section = CourseSection::findOrFail($id);
        if($section->clases->count() > 0){
            return response()->json([
                "message" => 403,
                "message_text" => "NO PUEDES ELIMINAR ESTA SECCIÓN PORQUE TIENE CLASES DENTRO"
            ]);
        }
        $section->delete();

        return response()->json([
            "message" => 200
        ]);
    }
}
