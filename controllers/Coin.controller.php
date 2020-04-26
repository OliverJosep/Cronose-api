<?php

require_once '../controllers/User.controller.php';
require_once '../controllers/WorkDemand.controller.php';
require_once '../controllers/Category.controller.php';
require_once '../controllers/Specialization.controller.php';

class CoinController {

  public function getCoinHistory($user_id) {
    $actualCoin = UserController::getUserById($user_id);
    $actualCoin = $actualCoin['coins'];
    $jobs = WorkDemandController::getAllByStatus($user_id, 'done');
    $history = [];

    foreach ($jobs as $job) {
      $job['coin_price'] = categoryController::getPriceBySpecialization($job['specialization_id']);
      $history[$job['id']]['work_date'] = $job['work_date'];
      
      if($job['worker_id'] === $user_id){

        $history[$job['id']]['new_coins'] = $actualCoin + floatval($job['coin_price']['coin_price']);
        $history[$job['id']]['new_coins'] = number_format(floatval($history[$job['id']]['new_coins']), 2, '.', ',');
        $actualCoin += floatval($job['coin_price']['coin_price']);

      } else {
        $history[$job['id']]['new_coins'] = $actualCoin - $job['coin_price']['coin_price'];
        $history[$job['id']]['new_coins'] = number_format(floatval($history[$job['id']]['new_coins']), 2, '.', ',');
        $actualCoin -= floatval($job['coin_price']['coin_price']);
      }

      $client = UserController::getBasicUserById($job['client_id'], false);
      $worker = UserController::getBasicUserById($job['worker_id'], false);
      $specialization = SpecializationController::getAllByIDAndLang($job['id'], 'es');

      $demand['demand_id'] = $job['Demand_id'];
      $demand['demanded_at'] = $job['demanded_at'];
      $demand['client'] = $client;
      $demand['worker'] = $worker;
      $demand['specialization'] = $specialization;

      $work['id'] = $job['id'];
      $work['status'] = $job['status'];
      $work['work_date'] = $job['work_date'];
      $work['qr_code_id'] = $job['qr_code_id'];
      $work['cancelation_policy'] = $job['cancelation_policy_id'];

      $history[$job['id']]['demand'] = $demand;
      $history[$job['id']]['work_demand'] = $work;
    }

    return $history;
  }

}

