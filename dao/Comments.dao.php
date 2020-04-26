<?php

require_once 'DAO.php';

class CommentsDAO extends DAO {

  public static function getComments($id) {
    $sql = "SELECT language_id, text 
            From Comment_Language 
            WHERE comment_id = :id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

}