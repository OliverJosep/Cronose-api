<?php

require_once 'DAO.php';

class LanguageDAO extends DAO {

  public static function getAll($lang){
    $sql = "SELECT * FROM Languages_Translation WHERE language_id = '${lang}';";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getOfferLangs() {
    $sql = "select language_id from Offer_Language group by language_id";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }


}
