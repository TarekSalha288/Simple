<?php

namespace App;
use Illuminate\Http\Request;
trait UploadImageTrait
{
    public function uploadImage(Request $request,$folderName){
      $image=$request->file('photo')->getClientOrginalImage();
      $path=$request->file('photo')->storeAs($folderName,$image);
      //return response()->json('Image Uploaded ');
    }
}
