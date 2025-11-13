<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs = BlogPost::get();
        return response()->json([
            'status' => 'success',
            'data'   => $blogs,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'     => 'required|exists:users,id',
            'category_id' => 'required|exists:blog_categories,id',
            'title'       => 'required',
            'content'     => 'required',
            'excerpt'     => 'required',
            'thumbnail'   => 'nullable|file|image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'fail',
                'message' => $validator->errors(),
            ], 400);
        }

        $data         = $request->all();
        $data['slug'] = Str::slug($data['title']);

        $user = Auth::user();

        if ($user->id != $data['user_id']) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'You can only create posts for your own user ID',
            ], 403);
        }

        $category = BlogCategory::find($data['category_id']);
        if (! $category) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Blog category not found',
            ], 404);
        }

        if ($user->role === 'admin') {
            $data['status']       = 'published';
            $data['published_at'] = now();
        } else {
            $data['status']       = 'draft';
            $data['published_at'] = now();
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $thumbnail     = $request->file('thumbnail');
            $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();
            $thumbnail->move(public_path('uploads/blog/'), $thumbnailName);
            $thumbnailPath = 'uploads/blog/' . $thumbnailName;
        }
        $data['thumbnail'] = $thumbnailPath;

        BlogPost::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Blog post created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $blog = BlogPost::find($id);
        if (! $blog) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Blog post not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $blog,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id'     => 'required|exists:users,id',
            'category_id' => 'required|exists:blog_categories,id',
            'title'       => 'required',
            'content'     => 'required',
            'excerpt'     => 'required',
            'thumbnail'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'fail',
                'message' => $validator->errors(),
            ], 400);
        }

        $blog = BlogPost::find($id);
        if (! $blog) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Blog post not found',
            ], 404);
        }

        $data = $request->all();

        $user = Auth::user();

        if ($user->id != $data['user_id']) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'You can only create posts for your own user ID',
            ], 403);
        }

        $category = BlogCategory::find($data['category_id']);
        if (! $category) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Blog category not found',
            ], 404);
        }

        if ($user->role === 'admin') {
            $data['status']       = 'published';
            $data['published_at'] = now();
        } else {
            $data['status']       = 'draft';
            $data['published_at'] = now();
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $thumbnail     = $request->file('thumbnail');
            $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();
            $thumbnail->move(public_path('uploads/post/'), $thumbnailName);
            $thumbnailPath = 'uploads/post/' . $thumbnailName;
        }
        $data['thumbnail'] = $thumbnailPath;

        $blog->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Blog post updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blog = BlogPost::find($id);
        if (! $blog) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Blog post not found',
            ], 404);
        }

        $blog->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Blog post deleted successfully',
        ], 200);
    }
}
