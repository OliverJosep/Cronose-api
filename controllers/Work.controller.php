<?php

require_once '../dao/Work.dao.php';
require_once '../controllers/User.controller.php';
require_once '../utilities/Language.php';

class WorkController {

// ----------------Get works--------------------

  public static function getAllWorks($lang) {
    $works = WorkDAO::getAllWorks();
    foreach ($works as $key => $value) {
      $works[$key]['translations'] = self::getWorkLangs($value['user_id'], $value['specialization_id']);
    }
    foreach ($works as $key => $value) {
      $works[$key]['translations'] = Language::orderByLang($lang, $value['translations']);
    }
    return $works;
  }

  public static function getAllWorksByUser($user_id, $lang) {
    $works = WorkDAO::getAllWorksByUser($user_id);
    foreach ($works as $key => $value) {
      $works[$key]['translations'] = self::getWorkLangs($value['user_id'], $value['specialization_id']);
    }
    foreach ($works as $key => $value) {
      $works[$key]['translations'] = Language::orderByLang($lang, $value['translations']);
    }
    return $works;
  }

  // public static function getWorks($limit, $offset) {
  //   $works = WorkDAO::getWorks($limit, $offset);
  //   foreach ($works as $key => $value) {
  //     $works[$key]['translations'] = self::getWorkLangs($value['user_id'], $value['specialization_id']);
  //   }
  //   return $works;
  // }

  public static function getWorksByLang($limit, $offset, $lang) {
    $works = WorkDAO::getWorksByLang($limit, $offset, $lang);

    foreach ($works as $key => $value) {
      $works[$key]['translations'] = self::getWorkLangs($value['user_id'], $value['specialization_id']);
    }

    foreach ($works as $key => $value) {
      $works[$key]['translations'] = Language::orderByLang($lang, $value['translations']);
    }
    
    return $works;
  }

  public static function getWorksDefaultLang($limit, $offset, $lang) {
    $works = WorkDAO::getWorks($limit, $offset);

    foreach ($works as $key => $value) {
      $works[$key]['translations'] = self::getWorkLangs($value['user_id'], $value['specialization_id']);
    }

    foreach ($works as $key => $value) {
      $works[$key]['translations'] = Language::orderByLang($lang, $value['translations']);
    }

    return $works;
  }

  public static function getFilteredWorks($filter) {
    $works = WorkDAO::getFilteredWorks($filter);

    foreach ($works as $key => $value) {
      $works[$key]['translations'] = self::getWorkLangs($value['user_id'], $value['specialization_id']);
    }

    foreach ($works as $key => $value) {
      $works[$key]['translations'] = Language::orderByLang($filter['defaultLang'], $value['translations']);
    }

    return $works;
  }

  public static function getWorksByIdAndLang($id, $lang) {
    return WorkDAO::getWorksByIdAndLang($id, $lang);
  }

  public static function getWork($userInitials,$userTag,$workEsp,$lang) {
    $work = WorkDAO::getWork($userInitials,$userTag,$workEsp);
    $work = array('user' => UserController::getBasicUserById($work['user_id'], $lang, true)) + $work;
    $work['translations'] = self::getWorkLangs($work['user_id'], $work['specialization_id']);
    $work['translations'] = Language::orderByLang($lang, $work['translations']);
    unset($work['user_id']);
    return $work;
  }

  public static function setNewWork($lang, $data){
    WorkDAO::setNewWork($data);
    WorkDAO::setNewWorkLang($lang, $data);
  }

  public static function getNewWork(){
    return $work;
  }

  // --------------------------------

  public static function getWorkLangs($user_id, $specialization_id) {
    return WorkDAO::getWorkLangs($user_id, $specialization_id);
  }

  public static function getTranslations($data) {
    $translations = self::getWorkLangs($data['user_id'], $data['specialization_id']);
    if (count($translations) == 0) return null;
    if (count($translations) == 3) return $translations;
    $langs = [
      0 => 'es', 
      1 => 'en', 
      2 => 'ca'
    ];
    foreach ($langs as $key => $value) {
      foreach ($translations as $translation) {
        if ($value === $translation['language_id']) unset($langs[$key]);
      }
      if (isset($langs[$key])) array_push($translations, ['language_id' => $value, 'title' => '', 'description' => '']);
    }
    return $translations;
  }

  public static function updateTranslations($data) {
    $translations = WorkDAO::getTranslation($data['user_id'], $data['specialization_id'], $data['lang']);
    if (!$translations && $data['title'] != '') return WorkDAO::insertTranslation($data['user_id'], $data['specialization_id'], $data['lang'], $data['title'], $data['description']);
    if ($translations['title'] != $data['title'] || $translations['title'] != $data['description']) return WorkDAO::updateTranslation($data['user_id'], $data['specialization_id'], $data['lang'], $data['title'], $data['description']);
    return $translations;
  }
// -----------------------------------------------------
}
