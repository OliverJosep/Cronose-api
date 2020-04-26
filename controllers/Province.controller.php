<?php

require_once '../dao/Province.dao.php';
require_once 'City.controller.php';

class ProvinceController {

  public static function getAll() {
  	return ProvinceDAO::getAll();
  }

  public static function getById($id) {
    return ProvinceDAO::getById($id);
  }

  public static function getProvinceCities($id) {
    $cities = CityController::getByProvinceId($id);
    return $cities;
  }

}