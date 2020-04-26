<?php

require_once '../dao/Achievement.dao.php';

class AchievementController {

  public static function getAll() {
  	$achievements = AchievementDAO::getAll();
    $langs = LanguageController::getLangs();
    foreach ($achievements as &$achievement) {
      foreach($langs as $lang) {
        $achievement['translations'][$lang] = AchievementDAO::getByIdAndLang($achievement['id'], $lang);
      }
    }
    return $achievements;
  }

  public static function getAllByLang($lang) {
  	return AchievementDAO::getAllByLang($lang);
  }

  public static function getById($id, $lang) {
    return AchievementDAO::getById($id, $lang);
  }

  public static function getAllByUser($id) {
  	return AchievementDAO::getAllByUser($id);
  }

  public static function getDescription($lang) {
    return AchievementDAO::getDescription($lang);
  }

  public static function setAchievement($user_id, $achi_id) {
    return AchievementDAO::setAchievement($user_id, $achi_id);
  }

  public static function haveAchi($user_id, $achi_id) {
    $achievements = AchievementDAO::haveAchi($user_id, $achi_id);
    if ($achievements) return true;
    return false;
  }

}