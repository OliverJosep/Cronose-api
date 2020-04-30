<?php

class Language{

  public static function orderByLang($lang, $array) {
    foreach ($array as $key => $value) {
      if ($value['language_id'] == $lang) {
        $aux = $value;
        unset($array[$key]);
        array_unshift($array, $aux);
      }
    }
    return $array;
  }

}