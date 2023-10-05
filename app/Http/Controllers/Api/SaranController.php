<?php

namespace App\Http\Controllers\Api;

use App\Models\Saran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SaranResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SaranController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get posts
        $sarans = Saran::skip(0)->take(3)->get();

        //return collection of posts as a resource
        return new SaranResource(true, 'List Data Posts', $sarans);
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
            'name'     => 'required',
            'content'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image

        //create post
        $sarans = Saran::create([
            'name'     => $request->name,
            'content'   => $request->content,
        ]);

        //return response
        return new SaranResource(true, 'Data Post Berhasil Ditambahkan!', $sarans);
    }

    /**
     * show
     *
     * @param  mixed $post
     * @return void
     */
    public function show(Saran $saran)
    {
        //return single post as a resource
        return new SaranResource(true, 'Data Post Ditemukan!', $saran);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */

    public function destroy(Saran $saran)
    {
        //delete image

        //delete post
        $saran->delete();

        //return response
        return new SaranResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}
