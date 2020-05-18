<?php

require_once '../dao/WorkDemand.dao.php';

class WorkDemandController {

  public static function getCard($card_id) {
    return WorkDemandDAO::getCard($card_id);
  }

  public static function getAllCards($worker_id, $client_id) {
    return WorkDemandDAO::getAllCards($worker_id, $client_id);
  }

  public static function getAll($user_id) {
    return WorkDemandDAO::getAll($user_id);
  }

  public static function getAllByStatus($user_id, $status) {
    return WorkDemandDAO::getAllByStatus($user_id, $status);
  }

  // Demands ---- 
  // 2020-05-19 15:00:00
  public static function createCard($worker_id, $client_id, $specialization_id, $work_date, $cancelation_policy, $qr_code = null) {
    if (!WorkDemandDAO::createDemands($worker_id, $client_id, $specialization_id)) return;
    $demand_id = WorkDemandDAO::getDemandsId($worker_id, $client_id, $specialization_id)['id'];
    WorkDemandDAO::createCard($work_date, $cancelation_policy, $demand_id, $qr_code);
    return $demand_id;
  }

}