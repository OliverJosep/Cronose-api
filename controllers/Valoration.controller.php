<?php

require_once '../dao/Valoration.dao.php';

class ValorationController {

  public static function getWorkerValorations($user_id, $specialization_id){
    return ValorationDAO::getWorkerValorations($user_id, $specialization_id);
  }

  public static function checkValorations($user_id) {
    return ValorationDAO::checkValorations($user_id);

  }

  public static function createValorations($card, $lang) {
    // return $card;
    $card = OfferDemandController::getCard($card['id'], $lang);
    ValorationDAO::createOfferValoration($card['id'], $card['client']['id']);
    ////ValorationDAO::createUserValoration($card['worker']['id'], $card['client']['id'], 'worker');
    ////ValorationDAO::createUserValoration($card['client']['id'], $card['worker']['id'], 'client');
    // return $card;
    // if (isset($_POST['offer-valoration-text'])) {
    //   return ValorationDAO::createOfferValoration($_POST['card_id'], $_POST['valorated_by'], $_POST['offer-valoration-text'], $_POST['offer_puntuation']);
    // };
    // return $_POST;
  }

}