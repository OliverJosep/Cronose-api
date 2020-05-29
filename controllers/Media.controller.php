<?php

require_once '../dao/Media.dao.php';

class MediaController {

  public static function getAll() {
    return MediaDAO::getAll();
  }

  public static function getById($id) {
    return MediaDAO::getById($id);
  }

  public static function insertOfferMedia($user_id, $specialization_id, $files) {
    foreach ($files as $key => &$file) {
      if ($file['tmp_name'] === '') {
        $files[$key] = null;
        continue;
      }
      $extension = "." . substr($file['type'], strpos($file['type'], "/") + 1);
      $dir = "images/" . strval($key);
      $url = "${user_id}_${specialization_id}";
      $fullUrl = $key.'/'.$url;
      move_uploaded_file ( $file['tmp_name'] , $dir.'/'.$url . $extension );
      ImageDAO::insertImage($fullUrl, $extension);
      $file = ImageDAO::getId($fullUrl);
      self::loadMedia($user_id, $specialization_id,$file['id']);
    }
    return $files;
  }

  public static function loadMedia($user_id, $specialization_id, $media_id) {
    return MediaDAO::loadMedia($user_id, $specialization_id, $media_id);
  }

  public static function getMedia($user_id, $specialization_id) {
    return MediaDAO::getMedia($user_id, $specialization_id);
  }



}