<?php

class AdvertisementDAO {

  public static function getAll() {
    $sql = "SELECT * FROM Advertisement";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll();
  }

  public static function getById($company, $specialization) {
    $sql = "SELECT * FROM Advertisement WHERE company_id = :company AND specialization_id = :specialization";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':company', $company, PDO::PARAM_INT);
    $statement->bindParam(':specialization', $specialization, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll();
  }

  public static function getAllByLanguage($lang) {
    $sql = "SELECT * FROM advertisement_language WHERE language_id = :lang";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetchAll();
  }

  public static function getAllBySpecialization($specialization) {
    $sql = "SELECT * FROM advertisement_language WHERE specialization_id = :specialization";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':specialization', $specialization, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll();
  }

}