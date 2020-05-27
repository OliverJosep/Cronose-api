<?php

require_once '../dao/Valoration.dao.php';

class ValorationController {

  public static function getWorkerValorations($user_id, $specialization_id){
    return ValorationDAO::getWorkerValorations($user_id, $specialization_id);
  }

  public static function createValoration($card, $lang) {
    $card = OfferDemandController::getCard($card['id'], $lang);
    ValorationDAO::createValoration($card['id'], $card['client']['id']);
  }

  public static function updateValoration() {
    $valoration = ValorationDAO::updateValoration($_POST['card_id'], $_POST['valorated_by'], $_POST['offer-valoration-text'], $_POST['offer_puntuation']);
    if ($valoration) OfferDemandController::updateCard($_POST['card_id'], 'done');
  }

  public static function getOfferValorations($user_id, $specialization_id) {
    $valorations = ValorationDAO::getOfferValorations($user_id, $specialization_id);
    foreach ($valorations as &$valoration) {
      $valoration['valorated_by'] = UserController::getBasicUserById($valoration['valorated_by'], false, true);
    }
    return $valorations;
  }

}