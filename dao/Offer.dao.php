<?php

require_once 'DAO.php';

class OfferDAO extends DAO {

  public static function getAllOffersByLang($lang) {
    $sql = "SELECT Offer.specialization_id,CONCAT(User.initials,User.tag)AS tag_user ,User.tag,User.initials,Offer.user_id,Offer_Language.language_id,User.name,Offer_Language.title,Offer_Language.description,Offer.personal_valoration,Offer.valoration_avg,Offer.coin_price
      FROM Offer,Offer_Language,User 
      WHERE Offer.user_id = Offer_Language.user_id
      AND Offer.specialization_id = Offer_Language.specialization_id 
      AND User.id = Offer.user_id
      AND Offer_Language.language_id='$lang'";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getOffersByIdANDLang($id, $lang) {
    $sql = "SELECT Offer.specialization_id,CONCAT(User.initials,User.tag)AS tag_user,User.tag,User.initials,Offer.user_id,Offer_Language.language_id,User.name,Offer_Language.title,Offer_Language.description,Offer.personal_valoration,Offer.valoration_avg,Offer.coin_price
      FROM Offer,Offer_Language,User 
      WHERE Offer.user_id = Offer_Language.user_id
      AND Offer.specialization_id = Offer_Language.specialization_id 
      AND User.id= Offer.user_id
      AND Offer_Language.language_id='$lang' 
      AND User.id = '$id'";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOffer($userInitials,$userTag,$offerEsp) {
    $sql = "SELECT Offer.user_id,Offer.specialization_id,Offer.personal_valoration,Offer.valoration_avg,Offer.coin_price
      FROM Offer,User
      WHERE User.id = Offer.user_id
      AND User.initials = :userInitials
      AND User.tag= :userTag
      AND Offer.specialization_id = :offerEsp";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':userInitials', $userInitials, PDO::PARAM_STR);
    $statement->bindParam(':userTag', $userTag, PDO::PARAM_INT);
    $statement->bindParam(':offerEsp', $offerEsp, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public static function getOfferById($userId,$offerEsp) {
  $sql = "SELECT Offer.user_id,Offer.specialization_id,Offer.personal_valoration,Offer.valoration_avg,Offer.coin_price
    FROM Offer,User
    WHERE User.id = Offer.user_id
    AND User.id = :userId
    AND Offer.specialization_id = :offerEsp";
  $statement = self::$DB->prepare($sql);
  $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
  $statement->bindParam(':offerEsp', $offerEsp, PDO::PARAM_INT);
  $statement->execute();
  return $statement->fetch(PDO::FETCH_ASSOC);
}


  // ---------------------------------

  public static function getAllOffers() {
    $sql = "SELECT Offer.user_id, Offer.specialization_id, User.initials, User.tag, User.name, User.surname, Offer.offered_at, Offer.coin_price, Offer.personal_valoration,Offer.valoration_avg, Offer.visibility 
      FROM User,Offer 
      WHERE User.id = Offer.user_id";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getOffersByUser($user_id, $visibility) {
    // if ($visibility) return 'hol';
    // return $visibility;
    $sql = "SELECT Offer.user_id, Offer.specialization_id, Offer.offered_at, Offer.coin_price, Offer.personal_valoration,Offer.valoration_avg, Offer.visibility 
      FROM Offer 
      WHERE Offer.user_id = :user_id";
    if ($visibility) $sql .= " AND Offer.visibility = 1";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getOffers($limit, $offset) {
    $sql = "SELECT Offer.user_id, Offer.specialization_id, Offer.offered_at, Offer.coin_price, Offer.personal_valoration,Offer.valoration_avg 
      FROM Offer 
      WHERE Offer.visibility = true 
      LIMIT :limit OFFSET :offset";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
    $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getOffersByLang($limit, $offset, $lang) {
    $sql = "SELECT Offer.user_id, Offer.specialization_id, User.initials, User.tag, User.name, User.surname, Offer.offered_at, Offer.coin_price, Offer.personal_valoration,Offer.valoration_avg 
      FROM Offer,Offer_Language,User 
      WHERE Offer.visibility = true 
      AND User.id = Offer.user_id 
      AND Offer.user_id = Offer_Language.user_id 
      AND Offer.specialization_id = Offer_Language.specialization_id 
      AND Offer_Language.language_id = :lang 
      LIMIT :limit OFFSET :offset";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
    $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getOfferLangs($user_id, $specialization_id) {
    $sql = "SELECT Offer_Language.language_id,Offer_Language.title,Offer_Language.description
      FROM Offer,Offer_Language 
      WHERE Offer.user_id = Offer_Language.user_id
      AND Offer.specialization_id = Offer_Language.specialization_id 
      AND Offer.user_id = :user_id 
      AND Offer.specialization_id = :specialization_id;";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $statement->bindParam(':specialization_id', $specialization_id, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getFilteredOffers($filter) {
    
    $sql = "SELECT Offer.user_id, Offer.specialization_id, User.initials, User.tag, User.name, User.surname, Offer.offered_at, Offer.coin_price, Offer.personal_valoration,Offer.valoration_avg
    FROM Offer, Offer_Language, User, Specialization 
    WHERE Offer.visibility = true 
    AND User.id = Offer.user_id 
    AND Offer.user_id = Offer_Language.user_id 
    AND Offer.specialization_id = Offer_Language.specialization_id 
    AND Offer.specialization_id = Specialization.id  ";

    if (isset($filter['langs'])) {
      $langs = "AND (";
      foreach ($filter['langs'] as $key => $value) {
        if ($key != 0) $langs .= "OR ";
        $langs .= "Offer_Language.language_id = '${value}' ";
      }
      $sql .= $langs . ") ";
    }
    if ($filter['category']) $sql .=  "AND Specialization.category_id = ${filter['category']} ";
    if ($filter['specialization']) $sql .=  "AND Specialization.id = ${filter['specialization']} ";
    if ($filter['string']) $sql .=  "AND Offer_Language.title LIKE '%${filter['string']}%' ";
    $sql .= "GROUP BY Offer.user_id,Offer.specialization_id
      LIMIT 10 OFFSET 0 ";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function setNewOffer($data){
    $coin = self::getCoinPrice($data['specialization_id']);
    $sql = "INSERT INTO `Offer` 
    VALUES (:user_id, :specialization_id, 90, 100, :coin_price, now(), 1) ";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
    $statement->bindParam(':specialization_id', $data['specialization_id'], PDO::PARAM_INT);
    $statement->bindParam(':coin_price', $coin['coin_price'], PDO::PARAM_STR);
    $statement->execute();
  }

  public static function setNewOfferLang($lang, $data){
    $sql = "INSERT INTO `Offer_Language` (`language_id`, `user_id`, `specialization_id`, `title`, `description`) VALUES 
            (:lang, :user_id, :specialization_id, :offerTitle, :offerDescription)";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    $statement->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
    $statement->bindParam(':specialization_id', $data['specialization_id'], PDO::PARAM_INT);
    $statement->bindParam(':offerTitle', $data['offerTitle'], PDO::PARAM_STR);
    $statement->bindParam(':offerDescription', $data['offerDescription'], PDO::PARAM_STR);
    $statement->execute();
  }

  public static function getCoinPrice($sp){
    $sql = "SELECT coin_price FROM Category, Specialization WHERE $sp = Specialization.id AND Specialization.category_id = Category.id;";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  // Translations

  public static function getTranslation($user_id, $specialization_id, $lang){
    $sql = "SELECT title, description FROM Offer_Language WHERE user_id = :user_id AND specialization_id = :specialization_id AND language_id = :lang";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':specialization_id', $specialization_id, PDO::PARAM_INT);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public static function insertTranslation($user_id, $specialization_id, $lang, $title, $description){
    $sql = "INSERT INTO Offer_Language(language_id, user_id, specialization_id, title, description) 
            VALUES(:lang, :user_id, :specialization_id, :title, :description)";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':specialization_id', $specialization_id, PDO::PARAM_INT);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    $statement->bindParam(':title', $title, PDO::PARAM_STR);
    $statement->bindParam(':description', $description, PDO::PARAM_STR);
    return $statement->execute();
  }

  public static function updateTranslation($user_id, $specialization_id, $lang, $title, $description){
    $sql = "UPDATE Offer_Language SET title = :title, description = :description 
            WHERE user_id = :user_id 
            AND specialization_id = :specialization_id
            AND language_id = :lang";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':specialization_id', $specialization_id, PDO::PARAM_INT);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    $statement->bindParam(':title', $title, PDO::PARAM_STR);
    $statement->bindParam(':description', $description, PDO::PARAM_STR);
    return $statement->execute();
  }

  public static function getVisibility($user_id, $specialization_id) {
    $sql = "SELECT visibility FROM Offer 
            WHERE user_id = :user_id AND specialization_id = :specialization_id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':specialization_id', $specialization_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);    
  }

  public static function updateVisibility($user_id, $specialization_id, $visibility){
    $sql = "UPDATE Offer SET visibility = :visibility 
            WHERE user_id = :user_id AND specialization_id = :specialization_id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':specialization_id', $specialization_id, PDO::PARAM_INT);
    $statement->bindParam(':visibility', $visibility, PDO::PARAM_INT);
    return $statement->execute();
  }
}