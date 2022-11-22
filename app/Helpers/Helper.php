<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

if (!function_exists('pushFileToStorage')) {
    /**
     * this function helps to upload files into aws s3 bucket
     * @param  string $image
     * @param  string $foldername: the desired folder for which the image is to be stored in the storage
     * @return string the image path or url
     */
    function pushFileToStorage($image, $folder_name)
    {
        $name = Str::random(20);
        $filePath = $folder_name . '/' . $name;
        Storage::disk('public')->put($filePath, file_get_contents($image));
        return $filePath;
    }
}
