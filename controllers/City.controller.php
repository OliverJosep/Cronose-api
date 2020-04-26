<?php

require_once '../dao/City.dao.php';

class CityController {

  public static function getAll() {
    $cities = CityDAO::getAll();
    foreach($cities as &$city) {
      $city['province'] = ProvinceController::getById($city['province']);
    }
    return $cities;
  }

  public static function getByCp($cp) {
    $city = CityDAO::getByCp($cp);
    $city['province'] = ProvinceController::getById($city['province']);
    return $city;
  }

  public static function getByProvinceId($province) {
    return CityDAO::getByProvinceId($province);
  }

}