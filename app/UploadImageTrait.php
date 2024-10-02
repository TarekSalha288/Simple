<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
trait UploadImageTrait
{
   public function uploadImage(Request $request, $folderName) {
    if ($request->hasFile('image')) {
        $image = $request->file('image')->getClientOriginalName();
        $path = $request->file('image')->storeAs($folderName,$image, 'project');
     return $path;
    }

}

}
