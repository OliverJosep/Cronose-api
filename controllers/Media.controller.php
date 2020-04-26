<?php

require_once '../dao/Media.dao.php';

class MediaController {

  public static function getAll() {
  	return MediaDAO::getAll();
  }

  public static function getById($id) {
    return MediaDAO::getById($id);
  }

}