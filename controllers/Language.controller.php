<?php
require_once '../dao/Language.dao.php';

class LanguageController {

  private static $langAvailable = ['en','es','ca'];
  private static $defaultLang = 'es';

  public static function getAll($lang) {
    return LanguageDAO::getAll($lang);
  }

  public static function getOfferLangs() {
    return LanguageDAO::getOfferLangs();
  }

  public function getLangs() {
    return self::$langAvailable;
  }

  public function setLang($language) {
    $lang = in_array($language, self::$langAvailable) ? $language : self::$defaultLang;
    $_SESSION['displayLang'] = $lang;
  }

  public static function langExist($language) {
    if( in_array($language, self::$langAvailable) ) return true;
    return false;
  }

}
