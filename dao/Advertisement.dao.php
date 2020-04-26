<?php

class AdvertisementDAO {

  public static function getAll() {
    $sql = "SELECT * FROM Advertisement";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll();
  }

  public static function getById($company, $specialization) {
    $sql = "SELECT * FROM Advertisement WHERE company_id = " . $company . " AND specialization_id = " . $specialization . ";";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll();
  }

  public static function getAllByLanguage($language) {
    $sql = "SELECT * FROM advertisement_language WHERE language_id = " . $language . ";";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll();
  }

  public static function getAllBySpecialization($specialization) {
    $sql = "SELECT * FROM advertisement_language WHERE specialization_id = " . $specialization . ";";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll();
  }

}