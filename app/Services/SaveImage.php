<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class SaveImage
{
  public function saveImage(string $image)
  {
    if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
      // Take out the base64 encoded text without mime type
      $image = substr($image, strpos($image, ',') + 1);
      // Get file extension
      $type = strtolower($type[1]); // jpg, png, gif

      // Check if file is an image
      if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
        throw new \Exception('invalid image type');
      }
      $image = str_replace(' ', '+', $image);
      $image = base64_decode($image);

      if ($image === false) {
        throw new \Exception('base64_decode failed');
      }
    } else {
      $filename = basename($image);
      return "images/" . $filename;
    }

    $dir = 'images/';
    $file = Str::random() . '.' . $type;
    $absolutePath = public_path($dir);
    $relativePath = $dir . $file;
    if (!File::exists($absolutePath)) {
      File::makeDirectory($absolutePath, 0755, true);
    }

    file_put_contents($relativePath, $image);

    return $relativePath;
  }
}