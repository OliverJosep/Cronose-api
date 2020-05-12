<?php

require_once '../dao/Specialization.dao.php';

class SpecializationController {

  public static function getAllByLang($lang) {
  	return $specializations = SpecializationDAO::getAllByLang($lang);
  }

  public static function getAllByIDAndLang($id, $lang) {
    return $specializations = SpecializationDAO::getAllByIDAndLang($id, $lang);
  }

  public static function getByLang($lang, $specialization_id) {
    return $specializations = SpecializationDAO::getByLang($lang, $specialization_id);
  }

  public static function getByLangAndCategory($lang, $category_id) {
    return $specializations = SpecializationDAO::getByLangAndCategory($lang, $category_id);
  }

}
