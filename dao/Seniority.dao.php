<?php

require_once 'DAO.php';

class SeniorityDAO extends DAO {

  public static function getSeniority($id){
    $sql = "SELECT * FROM Change_Seniority 
      WHERE user_id = :id;";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public static function getRange($id){
    $sql = "SELECT Change_Seniority.seniority_level, Seniority.points 
      FROM Change_Seniority,Seniority 
      WHERE Change_Seniority.seniority_level = Seniority.level
      AND user_id = :id;";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

}
