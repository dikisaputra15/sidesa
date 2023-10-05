<?php

namespace App\Http\Controllers\Api;

use App\Models\Statistik;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StatistikResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StatistikController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get posts
        $statistiks = Statistik::skip(0)->take(3)->get();

        //return collection of posts as a resource
        return new StatistikResource(true, 'List Data Posts', $statistiks);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/statistiks', $image->hashName());

        //create post
        $statistiks = Statistik::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
        ]);

        //return response
        return new StatistikResource(true, 'Data Post Berhasil Ditambahkan!', $statistiks);
    }

    /**
     * show
     *
     * @param  mixed $post
     * @return void
     */
    public function show(Statistik $statistik)
    {
        //return single post as a resource
        return new StatistikResource(true, 'Data Post Ditemukan!', $statistik);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(Request $request, Statistik $statistik)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/statistiks', $image->hashName());

            //delete old image
            Storage::delete('public/statistiks/'.$statistik->image);

            //update post with new image
            $statistik->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
            ]);

        } else {

            //update post without image
            $statistik->update([
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        }

        //return response
        return new StatistikResource(true, 'Data Home Berhasil Diubah!', $statistik);
    }

    /**
     * destroy
     *
     * @param  mixed $post
     * @return void
     */
    public function destroy(Statistik $Statistik)
    {
        //delete image
        Storage::delete('public/statistiks/'.$Statistik->image);

        //delete post
        $Statistik->delete();

        //return response
        return new StatistikResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}
