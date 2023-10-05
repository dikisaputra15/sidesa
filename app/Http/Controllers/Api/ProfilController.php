<?php

namespace App\Http\Controllers\Api;

use App\Models\Profil;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfilResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfilController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get posts
        $profils = Profil::skip(0)->take(3)->get();

        //return collection of posts as a resource
        return new ProfilResource(true, 'List Data Posts', $profils);
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
        $image->storeAs('public/profils', $image->hashName());

        //create post
        $profils = Profil::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
        ]);

        if($profils) {
            //return success with Api Resource
            return new ProfilResource(true, 'Data Category Berhasil Disimpan!', $profils);
        }

        //return failed with Api Resource
        return new ProfilResource(false, 'Data Category Gagal Disimpan!', null);
    }

   /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $profiles = Profil::whereId($id)->first();

        if($profiles) {
            //return success with Api Resource
            return new ProfilResource(true, 'Detail Data Category!', $profiles);
        }

        //return failed with Api Resource
        return new ProfilResource(false, 'Detail Data Category Tidak DItemukan!', null);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(Request $request, Profil $profile)
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
            $image->storeAs('public/profils', $image->hashName());

            //delete old image
            Storage::delete('public/profils/'.$profile->image);

            //update post with new image
            $profile->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
            ]);

        } else {

            //update post without image
            $profile->update([
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        }

        //return response
        return new ProfilResource(true, 'Data Home Berhasil Diubah!', $profile);
    }

    /**
     * destroy
     *
     * @param  mixed $post
     * @return void
     */
    public function destroy(Profil $profile)
    {
        //delete image
        Storage::delete('public/profils/'.$profile->image);

        //delete post
        $profile->delete();

        //return response
        return new ProfilResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}
