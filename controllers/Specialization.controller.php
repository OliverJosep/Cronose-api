<?php

require_once '../dao/Specialization.dao.php';

class SpecializationController {

  public static function getAll() {
    $specializations = SpecializationDAO::getAll();
    $langs = LanguageController::getLangs();
    foreach ($specializations as &$specialization) {
      foreach($langs as $lang) {
        $specialization['translations'][$lang] = SpecializationDAO::getByIDAndLang($specialization['id'], $lang);
      }
    }
    return $specializations;
  }

  public static function getAllByLang($lang) {
  	return $specializations = SpecializationDAO::getAllByLang($lang);
  }

  public static function getAllByIDAndLang($id, $lang) {
    return $specializations = SpecializationDAO::getAllByIDAndLang($id, $lang);
  }

  public static function getByLangAndCategory($lang, $category_id) {
    return $specializations = SpecializationDAO::getByLangAndCategory($lang, $category_id);
  }

}
