<?php

namespace App\Http\Controllers\Api;

use App\Models\Informasi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\InformasiResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InformasiController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get posts
        $informasis = Informasi::skip(0)->take(3)->get();

        //return collection of posts as a resource
        return new InformasiResource(true, 'List Data Posts', $informasis);
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
        $image->storeAs('public/informasi', $image->hashName());

        //create post
        $informasis = Informasi::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
        ]);

        //return response
        return new InformasiResource(true, 'Data Post Berhasil Ditambahkan!', $informasis);
    }

    /**
     * show
     *
     * @param  mixed $post
     * @return void
     */
    public function show(Informasi $info)
    {
        //return single post as a resource
        return new InformasiResource(true, 'Data Post Ditemukan!', $info);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(Request $request, Informasi $info)
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
            $image->storeAs('public/informasi', $image->hashName());

            //delete old image
            Storage::delete('public/informasi/'.$info->image);

            //update post with new image
            $info->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
            ]);

        } else {

            //update post without image
            $info->update([
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        }

        //return response
        return new InformasiResource(true, 'Data Home Berhasil Diubah!', $info);
    }

    /**
     * destroy
     *
     * @param  mixed $post
     * @return void
     */
    public function destroy(Informasi $info)
    {
        //delete image
        Storage::delete('public/informasi/'.$info->image);

        //delete post
        $info->delete();

        //return response
        return new InformasiResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}
