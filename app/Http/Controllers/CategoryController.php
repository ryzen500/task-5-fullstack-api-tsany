<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::latest()->paginate(4);
        return response()->json([
            'status' => true,
            'message' => "List Categories",
            'categories' => $categories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required',
        ]);
         //response error validation
         if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $category = Category::create([
            'name' => $request->name,
            'user_id' => auth()->user()->id
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Category has been created',
            'date' => $category
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $category = Category::find($id);
        if($category)
        {
            return response()->json([
                'status' => true,
                'message' => 'Category Details',
                'data' => $category
            ], 200);
        }
        return response()->json([
            'success' => false,
            'mesaage' => "Category not found",
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required',
        ]);
         //response error validation
         if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $category = Category::find($id);
        if(!$category)
        {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
            ], 404);
        }
        if(auth()->user()->id != $category->user_id)
        {
            return response()->json([
                'success' => false,
                'message' => 'Not permmited, this is not yours',
                'data'    => $category  
            ], 403);
        }
        $category->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Category Updated',
            'data'    => $category  
        ], 200);
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if(!$category)
        {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
            ], 404);
        }
        if(auth()->user()->id != $category->user_id)
        {
            return response()->json([
                'success' => false,
                'message' => 'Not permmited, this is not yours',
                'data'    => $category  
            ], 403);
        }
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Category Deleted',
        ], 200);
    }
}
