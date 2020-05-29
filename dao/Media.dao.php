<?php

require_once 'DAO.php';

class MediaDAO extends DAO {

  public static function getAll(){
    $sql = "SELECT * FROM Media";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getById($id) {
    $sql = "SELECT * FROM Media WHERE Media.id = :id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public static function loadMedia($user_id, $specialization_id, $media_id) {
    $sql = "INSERT INTO Load_Media VALUES(:user_id, :specialization_id, :media_id)";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':specialization_id', $specialization_id, PDO::PARAM_INT);
    $statement->bindParam(':media_id', $media_id, PDO::PARAM_INT);
    return $statement->execute();
  }

  public static function getMedia($user_id, $specialization_id) {
    $sql = "SELECT Media.url, Media.extension FROM Load_Media, Media 
            WHERE Media.id =  Load_Media.media_id
            AND user_id = :user_id AND specialization_id = :specialization_id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':specialization_id', $specialization_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }
  
}