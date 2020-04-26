<?php

require_once 'DAO.php';

class CityDAO extends DAO {

  private static $returnFields = "cp, province_id as province, name, longitude, latitude";

  public static function getAll(){
    $fields = self::$returnFields;
    $sql = "SELECT ${fields} FROM City";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getByCp($cp) {
    $fields = self::$returnFields;
    $sql = "SELECT ${fields} FROM City WHERE City.cp = :cp";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':cp', $cp, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public static function getByProvinceId($id) {
    $fields = self::$returnFields;
    $sql = "SELECT ${fields} FROM City WHERE City.province_id = :id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

}