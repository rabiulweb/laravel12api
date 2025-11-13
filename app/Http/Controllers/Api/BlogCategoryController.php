<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = BlogCategory::get();
        return response()->json([
            'status' => 'success',
            'data'   => $categories,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:blog_categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'fail',
                'message' => $validator->errors(),
            ], 400);
        }

        $data         = $request->all();
        $data['slug'] = Str::slug($data['name']);
        BlogCategory::create($data);
        return response()->json([
            'status'  => 'success',
            'message' => 'Blog category created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = BlogCategory::find($id);
        if (! $category) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Blog category not found',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data'   => $category,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:blog_categories,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'fail',
                'message' => $validator->errors(),
            ], 400);
        }

        $category = BlogCategory::find($id);
        if (! $category) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Blog category not found',
            ], 404);
        }
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        $category->save();
        return response()->json([
            'status'  => 'success',
            'message' => 'Blog category updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = BlogCategory::find($id);
        if (! $category) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Blog category not found',
            ], 404);
        }
        $category->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Blog category deleted successfully',
        ], 200);
    }
}
