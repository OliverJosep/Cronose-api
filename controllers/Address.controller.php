<?php

require_once '../controllers/City.controller.php';

class AddressController {

  public static function getUserAddress($user) {
    $address['city'] = CityController::getByCp($user['city']);
    $address['province'] = $address['city']['province'];
    unset($address['city']['province']);
    return $address;
  }

}