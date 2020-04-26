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
    $sql = "SELECT * FROM Media WHERE Media.id = ${id}";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }
  
}