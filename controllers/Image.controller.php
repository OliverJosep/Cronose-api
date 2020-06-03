<?php

require_once '../dao/Image.dao.php';

class ImageController {

  public static function saveImages($user_initials, $user_tag, $files){
    foreach ($files as $key => &$file) {
      if ($file['tmp_name'] === '') {
        $files[$key] = null;
        continue;
      }
      $extension = "." . substr($file['type'], strpos($file['type'], "/") + 1);
      $dir = "images/" . strval($key);
      $name = "${user_initials}_${user_tag}";
      $fullUrl = $key.'/'.$name;
      move_uploaded_file ( $file['tmp_name'] , $dir.'/'.$name . $extension );
      ImageDAO::insertImage($fullUrl, $extension);
      $file = ImageDAO::getId($fullUrl);
      if($key == 'dni_img') $file = ImageDAO::insertDNI($file['id']);
    }
    return $files;
  }

  public static function updateAvatar($user_initials, $user_tag, $img, $dir){
    $extension = "." . substr($img['type'], strpos($img['type'], "/") + 1);
    $media = UserController::haveAvatar($user_initials, $user_tag);
    if ($media) {
      move_uploaded_file ("images/$dir/".$media['url'].$media['extension'], "");
      UserController::setAvatar($user_initials, $user_tag, null);
      self::deleteMedia($media['avatar_id']);
    }
    $files = ["avatar" => $img];
    $media = self::saveImages($user_initials, $user_tag, $files);
    UserController::setAvatar($user_initials, $user_tag, $media['avatar']['id']);
    
    self::active($media['avatar']['id'], 1);
  }

  public static function active($media_id, $visible) {
    return ImageDAO::active($media_id, $visible);
  }

  public static function deleteMedia($media_id) {
    return ImageDAO::deleteMedia($media_id);
  }

}