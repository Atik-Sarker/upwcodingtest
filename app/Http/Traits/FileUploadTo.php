<?php
namespace App\Http\Traits;

use Illuminate\Support\Str;


trait FileUploadTo
{

    function UploadImage($image, $folder = 'demo', $oldImage = null){
        if ($oldImage !== null){
            if (file_exists(storage_path("app/public/{$oldImage}")))
                unlink(storage_path("app/public/{$oldImage}"));
        }
        $originalExtension = $image->getClientOriginalExtension();
        $originalName = Str::slug(rtrim($image->getClientOriginalName(),$originalExtension));
        $customImageName = strtolower($originalName).'-'.time().'.'.$originalExtension;
        $path = $image->storeAs( $folder, $customImageName );
        return $path;
    }

}