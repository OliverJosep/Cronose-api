<?php

require_once 'DAO.php';

class CancellationDAO extends DAO {

  public static function getAll($lang){
    $sql = "SELECT * FROM Cancellation_Language WHERE language_id = :lang";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function get($id, $lang){
    $sql = "SELECT cancellation_policy_id AS id, name, description FROM Cancellation_Language WHERE cancellation_policy_id = :id AND language_id = :lang";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_STR);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

}