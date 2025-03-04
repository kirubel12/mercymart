<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index(){

        $categories = Category::all();

        if($categories->isEmpty()){
            return response([
                'message' => 'No categories found',
            ],404);
        }

        return response([
            'data' => $categories,
        ],200);
    }

    /** creating a category */
    public function store(Request $request){

        $fields = $request->validate([
            'name' => 'required|string',
        ]);

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|unique:categories'
            ]);

            $category = Category::create($validatedData);
            return response([
                'data' => $category,
                'message' => 'Category created successfully',
            ],201);
        } catch (\Throwable $th) {
            return response([
                'message' => 'Failed to create category',
                'error' => $th->getMessage(),
            ],500);
        }
    }

    /** updating a category */
    public function update(Request $request, $id){
        $category = Category::find($id);
        if(!$category){
            return response([
                'message' => 'Category not found',
            ],404);
        }
        if(! Gate::allows('manage-categories', $category)){
            return response([
                'message' => 'You are not allowed to update this category',
            ],403);
        }

        $category = Category::find($id);
        if(!$category){
            return response([
                'message' => 'Category not found',
            ],404);
        }

        $fields = $request->validate([
            'name' => 'required|string',
        ]);

        $category->update($fields);

        return response([
            'data' => $category,
            'message' => 'Category updated successfully',
        ],200);
    }

    /** deleting a category */
    public function destroy($id){
        $category = Category::find($id);
        if(!$category){
            return response([
                'message' => 'Category not found',
            ],404);
        }
        if(Gate::denies('manage-categories', $category)){
            return response([
                'message' => 'You are not allowed to delete this category',
            ],403);
        }

        $category = Category::find($id);
        if($category){
            $category->delete();
        }else{
            return response([
                'message' => 'Category not found',
            ],404);
        }
        return response([
            'message' => 'Category deleted successfully',
        ],200);
    }

    /** getting a category */
    public function show($category){
        $category = Category::with('products')->find($category);

        if($category->products_count > 0){
            return response([
                'message' => 'Category has products',
            ],400);
        }

        if(is_numeric($category)){
            $category = Category::withCount('products')->find($category);
        }
        if(!$category){
            return response([
                'message' => 'Category not found',
            ],404);
        }

        return response([
            'data' => $category,
            'products' => $category->products,
            'message' => 'Category retrieved successfully',
        ],200);
    }
}
