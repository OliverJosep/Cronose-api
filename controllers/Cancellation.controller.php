<?php

require_once '../dao/Cancellation.dao.php';

class CancellationController {

  public static function getAll($lang) {
    return CancellationDAO::getAll($lang);
  }

}