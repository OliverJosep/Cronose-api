<?php

require_once '../dao/Offer.dao.php';
require_once '../controllers/User.controller.php';
require_once '../utilities/Language.php';

class OfferController {

// ----------------Get offers--------------------

  public static function getAllOffers($lang) {
    $offers[] = OfferDAO::getAllOffers();
    foreach ($offers as $key => $value) {
      $offers[$key]['translations'] = self::getOfferTranslations($value['user_id'], $value['specialization_id'], $lang);
    }
    return $offers;
  }
  
  public static function getOffersByUser($user_id, $lang, $visibility = true) {
    $offers = OfferDAO::getOffersByUser($user_id, $visibility);
    foreach ($offers as $key => $value) {
      $offers[$key]['user'] = UserController::getBasicUserById($value['user_id'], false, true);
      $offers[$key]['translations'] = self::getOfferTranslations($user_id, $value['specialization_id'], $lang);
      $offers[$key]['images'] = MediaDAO::getMedia($user_id, $value['specialization_id']);
      unset($offers[$key]['user_id']);
    }
    return $offers;
  }

  public static function getFilteredOffers() {
    $category = (isset($_GET['category'])) ? $_GET['category'] : null;
    $specialization = (isset($_GET['specialization'])) ? $_GET['specialization'] : null;
    $text = (isset($_GET['text'])) ? $_GET['text'] : null;
    $lang = (isset($_GET['lang'])) ? $_GET['lang'] : null;
    $offers = OfferDAO::getFilteredOffers($category, $specialization, $text, $lang, $_GET['offset'], $_GET['limit']);

    foreach ($offers as $key => $value) {
      $offers[$key]['user'] = UserController::getBasicUserById($value['user_id'], false , true);
      $offers[$key]['translations'] = self::getOfferTranslations($value['user_id'], $value['specialization_id'], $_GET['defaultLang']);
      $offers[$key]['images'] = MediaDAO::getMedia($value['user_id'], $value['specialization_id']);
      unset($offers[$key]['user_id']);
    }
    return $offers;
  }

  public static function getOffer($userInitials,$userTag,$offerEsp,$lang) {
    $offer = OfferDAO::getOffer($userInitials,$userTag,$offerEsp);
    $offer = array('user' => UserController::getBasicUserById($offer['user_id'], $lang, true)) + $offer;
    $offer['translations'] = self::getOfferTranslations($offer['user_id'], $offer['specialization_id'], $lang);
    $offer['images'] = MediaDAO::getMedia($offer['user_id'], $offer['specialization_id']);
    $offer['valorations'] = ValorationController::getOfferValorations($offer['user']['id'], $offerEsp);
    unset($offer['user_id']);
    return $offer;
  }

  public static function getOfferById($userId,$offerEsp,$lang, $user = true) {
    $offer = OfferDAO::getOfferById($userId,$offerEsp);
    if ($user) $offer = array('user' => UserController::getBasicUserById($offer['user_id'], $lang, true)) + $offer;
    $offer['translations'] = self::getOfferTranslations($offer['user_id'], $offer['specialization_id'], $lang);
    $offer['images'] = MediaDAO::getMedia($offer['user_id'], $offer['specialization_id']);
    unset($offer['user_id']);
    return $offer;
  }

  // Create new Offer
  public static function setNewOffer($lang, $user_id, $specialization_id, $personal_valoration, $offerTitle, $offerDescription,$files){
    OfferDAO::setNewOffer($user_id, $specialization_id, $personal_valoration);
    MediaController::insertOfferMedia($user_id,$specialization_id,$files);
    return OfferDAO::setNewOfferLang($lang, $user_id, $specialization_id, $offerTitle, $offerDescription);
  }

  // Get and update translations
  public static function getOfferTranslations($user_id, $specialization_id, $lang = null) {
    $translations = OfferDAO::getOfferTranslations($user_id, $specialization_id);
    if ($lang) $translations = Language::orderByLang($lang, $translations);
    return $translations;
  }

  public static function getTranslations($data) {
    $translations = self::getOfferTranslations($data['user_id'], $data['specialization_id']);
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
    $translations = OfferDAO::getTranslation($data['user_id'], $data['specialization_id'], $data['lang']);
    if (!$translations && $data['title'] != '') return OfferDAO::insertTranslation($data['user_id'], $data['specialization_id'], $data['lang'], $data['title'], $data['description']);
    if ($translations['title'] != $data['title'] || $translations['title'] != $data['description']) return OfferDAO::updateTranslation($data['user_id'], $data['specialization_id'], $data['lang'], $data['title'], $data['description']);
    return $translations;
  }

  // Visibility
  public static function getVisibility($data) {
    return OfferDAO::getVisibility($data['user_id'], $data['specialization_id']);
  }

  public static function updateVisibility($data) {
    $isVisible = self::getVisibility($data)['visibility'];
    $visible = (isset($data['visible'])) ? '1' : '0';
    if ($isVisible === $visible) return 'Equals';
    return OfferDAO::updateVisibility($data['user_id'], $data['specialization_id'], $visible);
  }

}