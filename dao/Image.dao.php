<?php

require_once 'DAO.php';

class ImageDAO extends DAO {

  public static function getId($url){
    $sql = "SELECT id 
      FROM Media
      WHERE url = :url";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':url', $url, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public static function insertImage($url, $extension) {
    $sql = "INSERT INTO Media (extension, url) VALUES(:extension, :url);";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':url', $url, PDO::PARAM_STR);
    $statement->bindParam(':extension', $extension, PDO::PARAM_STR);
    $statement->execute();
  }

  public static function insertDNI($id){
    $sql = "INSERT INTO `DNI_Photo` (`id`, `status`, `media_id`) VALUES (NULL, 'accepted', :id)";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $sql = "SELECT id 
            FROM DNI_Photo
            WHERE media_id = :id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $id = $statement->fetch(PDO::FETCH_ASSOC);
    return $id;
  }

  public static function active($media_id, $visible) {
    $sql = "UPDATE Media SET visible = :visible WHERE id = :media_id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':media_id', $media_id, PDO::PARAM_INT);
    $statement->bindParam(':visible', $visible, PDO::PARAM_INT);
    return $statement->execute();
  }

  public static function deleteMedia($media_id) {
    $sql = "DELETE FROM Media WHERE Media.id = :media_id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':media_id', $media_id, PDO::PARAM_INT);
    return $statement->execute();
  }


}