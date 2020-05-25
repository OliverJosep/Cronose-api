<?php

require_once '../dao/OfferDemand.dao.php';
require_once 'Offer.controller.php';
require_once 'Cancellation.controller.php';

class OfferDemandController {

  public static function getCard($card_id) {
    $card = OfferDemandDAO::getCard($card_id);
    $card['worker'] = UserController::getBasicUserById($card['worker_id']);
    $card['client'] = UserController::getBasicUserById($card['client_id']);
    unset($card["worker_id"], $card["client_id"]);
    return $card;
  }

  public static function getAllCards($worker_id, $client_id, $lang) {
    $cards = OfferDemandDAO::getAllCards($worker_id, $client_id);
    foreach ($cards as $value => &$key) {
      $key['worker'] = UserController::getBasicUserById($key['worker_id'], $lang);
      $key['client'] = UserController::getBasicUserById($key['client_id'], $lang);
      $key['cancellation_policy'] = CancellationController::get($key["cancellation_policy_id"], $lang);
      $key['offer'] = OfferController::getOfferById($key['worker_id'],$key['specialization_id'],$lang, false);
      unset($key["cancellation_policy_id"], $key["worker_id"], $key["client_id"]);
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

  public static function updateCard($card_id, $status) {
    return OfferDemandDAO::updateCard($card_id, $status);
  }

  public static function checkCards($user_id) {
    $cards = OfferDemandDAO::checkCards($user_id);
    foreach ($cards as $value => $key) {
      if ($key['status'] === "accepted") {
        $cards[$value] = self::getCard($key['id']);
      } else {
        OfferDemandDAO::updateCard($key['id'], "rejected");
        unset($cards[$value]);
      }
    }
    return $cards;
  }
  
}