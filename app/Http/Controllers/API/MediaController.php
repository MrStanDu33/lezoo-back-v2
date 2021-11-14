<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    /**
     * Store a media in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd(public_path());
        $data = $request->all();

//        $validator = Validator::make($data, [
//            'file' => 'required|string|max:255',
//        ]);

//        if ($validator->fails()) {
//            return response(['error' => $validator->errors(), 'Validation Error']);
//        }

        $file = $request->file("file");
        $ext = $file->extension();

        $filename = str_replace('.'.$ext, '', $file->getClientOriginalName());
        $date = date('Y-m-d_H-i-s');
        $completeFilename = "{$date}_{$filename}.{$ext}";

        $fileURL = $file->storeAs('/uploadedFiles', $completeFilename, ['disk' => 'public']);

        return response(['fileURL' => $fileURL, 'message' => 'Created successfully'], 201);
    }
}
