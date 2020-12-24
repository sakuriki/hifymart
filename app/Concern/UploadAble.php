<?php

namespace App\Concern;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadAble
{
  /**
   * @param UploadedFile $file
   * @param null $folder
   * @param string $disk
   * @param null $filename
   * @return false|string
   */
  public function uploadOne(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
  {
    $name = !is_null($filename)
      ? $filename
      : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

    return $file->storeAs(
      $folder,
      $name . "_" . date('mdYHis') . "_" . uniqid() . "." . $file->getClientOriginalExtension(),
      $disk
    );
  }

  /**
   * @param null $path
   * @param string $disk
   */
  public function deleteOne($path = null, $disk = 'public')
  {
    Storage::disk($disk)->delete($path);
  }
}
