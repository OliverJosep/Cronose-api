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

}