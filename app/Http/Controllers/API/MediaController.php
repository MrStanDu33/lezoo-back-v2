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
        $data = $request->all();

        $validator = Validator::make($data, [
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $file = $request->file("file");
        $ext = $file->extension();

        $filename = str_replace('.'.$ext, '', $file->getClientOriginalName());
        $date = date('Y-m-d_H-i-s');
        $completeFilename = "{$date}_{$filename}.{$ext}";

        $file->storeAs('/uploadedFiles', $completeFilename, ['disk' => 'public']);

        return response(['filename' => $completeFilename, 'message' => 'Created successfully'], 201);
    }
}
