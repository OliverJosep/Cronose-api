<?php

require_once '../dao/Image.dao.php';

class ImageController {

  public static function saveImages($user_initials, $user_tag, $files){
    foreach ($files as $key => &$file) {
      if ($file['tmp_name'] === '') {
        $files[$key] = null;
        continue;
      }
      $dir = "images/" . strval($key);
      $name = "${user_initials}_${user_tag}";
      $fullUrl = $key.'/'.$name;
      move_uploaded_file ( $file['tmp_name'] , $dir.'/'.$name );
      ImageDAO::insertImage($fullUrl);
      $file = ImageDAO::getId($fullUrl);
      if($key == 'dni_img') $file = ImageDAO::insertDNI($file['id']);
    }
    return $files;
  }

  // TODO Arreglar imatges
  public static function updateImages($user_initials, $user_tag, $img, $dir){
    // if (!isset($media_id)) saveImages($user_initials, $user_tag, $img);
    move_uploaded_file ($img['tmp_name'], "images/${dir}/${user_initials}_${user_tag}");
  }

  public static function active($media_id, $visible) {
    return ImageDAO::active($media_id, $visible);
  }

}