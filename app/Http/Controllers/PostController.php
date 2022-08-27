<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get data from table posts
        $posts = Post::paginate(4);
        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'List Data Post',
            'posts'    => $posts  
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
        //set validation
        $validator = Validator::make($request->all(), [
            'title'   => 'required',
            'content' => 'required',
            'image' => 'required',
            'category_id' => 'required'
        ]);
        $kategori = Category::where('id', $request->category_id)->first();
        if(!$kategori){
            return response()->json([
                'success' => false,
                'message' => 'Kategori yang dimasukan tidak sesuai',
            ], 400);
        }
        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

      
        $user = auth()->user();
        //save to database
        $post = new Post();
        $post->title = $request->title;
        $post->content = $request->content;
        if($request->file('image') != null) //upload image
        {
            $imgName = time().'.'.$request->image->extension(); 
            $request->image->move(public_path('gambar'), $imgName);
            $post->image = $imgName;
        }else{ //mencocokkan ke faker karna fakernya pakai imaageUrl 
            $post->image = $request->image;
        }
        $post->user_id = $user->id;
        $post->category_id = $request->category_id;
        $post->save();
   
        if($post) {
            return response()->json([
                'success' => true,
                'message' => 'Post Created',
                'data'    => $post  
            ], 201);

        } 

        //failed save to database
        return response()->json([
            'success' => false,
            'message' => 'Post Failed to Save',
        ], 409);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        if($post){
            return response()->json([
                'success' => true,
                'mesaage' => 'Detail data post ' . $post->title,
                'data' => $post
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'mesaage' => "Post tidak ditemukan",
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'title'   => 'required',
            'content' => 'required',
        ]);
        
        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

      
        //find post by ID
        $post = Post::find($id);
        if($post) {
            $user = auth()->user();
            if ($post->user->id != $user->id) //cek apakah yang sedang mengubah post adalah user yg sebelumnya membuat post ini
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Post tidak bisa diubah. Anda bukan pemilik post ini',
                    'data'    => $post  
                ], 200);
            }
            if($request->has('image'))
            {
                $imgName = time().'.'.$request->image->extension();   //membuat nama file unik
                $request->image->move(public_path('gambar'), $imgName);
                //update post
                $post -> update([
                    'title'     => $request->title,
                    'content'   => $request->content,
                    'image'     => $imgName,
                ]);
            }else{
                $post -> update($request->all());
            }
       
            return response()->json([
                'success' => true,
                'message' => 'Post Updated',
                'data'    => $post  
            ], 200);

        }

        //data post not found
        return response()->json([
            'success' => false,
            'message' => 'Post Not Found',
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //find post by ID
        $post = post::find($id);

        if($post) {
            $user = auth()->user();
            if ($post->user->id != $user->id) //jika yang mau hapus post bukan pemiliknya
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal dihapus karna post bukan milik anda',
                    'data'    => $post  
                ], 400);
            }
            //delete post
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post Deleted',
            ], 200);

        }
        //data post not found
        return response()->json([
            'success' => false,
            'message' => 'Post Tidak ditemukan',
        ], 404);
    }
}
