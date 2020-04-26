<?php

require_once '../dao/Category.dao.php';
require_once 'Language.controller.php';

class CategoryController {

  public static function getAll() {
    $categories = CategoryDAO::getAll();
    $langs = LanguageController::getLangs();
    foreach ($categories as &$category) {
      foreach($langs as $lang) {
        $category['translations'][$lang] = CategoryDAO::getByIdAndLang($category['id'], $lang);
      }
    }
    return $categories;
  }

  public static function getAllByLang($lang) {
  	return CategoryDAO::getAllByLang($lang);
  }

  public static function getCountSpecialization($lang) {
    return CategoryDAO::getCountSpecialization($lang);
  }

  public static function getPriceBySpecialization($id){
    return CategoryDAO::getPriceBySpecialization($id);
  }

}
