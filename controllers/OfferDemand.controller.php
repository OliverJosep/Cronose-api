<?php

require_once '../dao/OfferDemand.dao.php';
require_once 'Offer.controller.php';

class OfferDemandController {

  public static function getCard($card_id) {
    return OfferDemandDAO::getCard($card_id);
  }

  public static function getAllCards($worker_id, $client_id, $lang) {
    $cards = OfferDemandDAO::getAllCards($worker_id, $client_id);
    foreach ($cards as $value => &$key) {
      $key['offer'] = OfferController::getOfferById($key['worker_id'],$key['specialization_id'],$lang, false);
    }
    return $cards;
  }

  public static function getAll($user_id) {
    return OfferDemandDAO::getAll($user_id);
  }

  public static function getAllByStatus($user_id, $status) {
    return OfferDemandDAO::getAllByStatus($user_id, $status);
  }

  // Demands 
  public static function createCard($worker_id, $client_id, $specialization_id, $work_date, $cancellation_policy, $qr_code = null) {
    if (!OfferDemandDAO::createDemands($worker_id, $client_id, $specialization_id)) return;
    $demand_id = OfferDemandDAO::getDemandsId($worker_id, $client_id, $specialization_id)['id'];
    return OfferDemandDAO::createCard($work_date, $cancellation_policy, $demand_id, $qr_code);
  }

}