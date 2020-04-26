<?php

require_once '../dao/Valoration.dao.php';

class ValorationController {

  public static function getWorkerValorations($user_id, $specialization_id){
    return ValorationDAO::getWorkerValorations($user_id, $specialization_id);
  }

}