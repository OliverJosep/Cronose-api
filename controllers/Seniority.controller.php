<?php

require_once '../dao/Seniority.dao.php';

class SeniorityController {

  public static function getSeniority($id) {
  	return SeniorityModel::getSeniority($id);
  }

  public static function getRange($id) {
    return SeniorityDAO::getRange($id);
  }

}
