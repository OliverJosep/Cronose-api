<?php

require_once 'DAO.php';

require_once '../controllers/Media.controller.php';
require_once '../controllers/Address.controller.php';
require_once '../controllers/Seniority.controller.php';
require_once '../controllers/Achievement.controller.php';
require_once '../controllers/Image.controller.php';
require_once '../controllers/Token.controller.php';
require_once '../controllers/User.controller.php';

// Logger
require_once '../utilities/Logger.php';

class UserDAO extends DAO {

  private static $returnFields = "id, initials, tag, email, name, surname, surname_2, coins, registration_date, points, avatar_id as avatar, private, city_cp as city, province_id as province";

  public static function getUserCompleteData(&$user, $lang = null) {
    // Unset name in case of private user
    self::getFullName($user);

    if (!$user['private']) $user['full_name'] = "${user['name']} ${user['surname']} ${user['surname_2']}";
    $user['description'] = UserController::getUserDescription($user, $lang);
    $user['avatar'] = MediaController::getById($user['avatar']) ?? 'sample_avatar';
    $user['address'] =  AddressController::getUserAddress($user);
    $user['achievements'] = AchievementController::getAllByUser($user['id']);
    // $user['Seniority'] = SeniorityController::getRange($user);

    // Unset not necessary information
    unset($user['city'], $user['province'], $user['password'], $user['validated']);
    return $user;
  }

  private static function getUserBasicData(&$user, $lang, $avatar) {
    // Unset name in case of private user
    self::getFullName($user);

    if ($avatar) $user['avatar'] = MediaController::getById($user['avatar_id']);

    // Unset not necessary information
    unset( $user['email'] ,$user['avatar_id'], $user['private']);
  }

  public static function getFullName(&$user) {
    $user['full_name'] = ($user['private']) 
      ? substr($user['name'], 0, 1) . ' ' . substr($user['surname'], 0, 1) . ' ' . substr($user['surname_2'], 0, 1)
      : "${user['name']} ${user['surname']} ${user['surname_2']}";
    if ($user['private']) unset($user['name'], $user['surname'], $user['surname_2']);
  }
  public static function getAuthData($email, $password) {
    $sql = "SELECT id, initials, tag, email FROM User WHERE email = :email AND password = :password";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->bindParam(':password', $password, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);    
  }

  public static function getAll() {
    $fields = self::$returnFields;
    $sql = "SELECT ${fields} FROM User";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $users;
  }

  public static function getId($initials, $tag) {
    $sql = "SELECT id FROM User WHERE initials = :initials and tag = :tag";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':initials', $initials, PDO::PARAM_STR);
    $statement->bindParam(':tag', $tag, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public static function getUserByInitialsAndTag($initials, $tag) {
    $fields = self::$returnFields;
    $sql = "SELECT ${fields} FROM User WHERE initials = :initials and tag = :tag";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':initials', $initials, PDO::PARAM_STR);
    $statement->bindParam(':tag', $tag, PDO::PARAM_INT);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    return $user;
  }

  public static function getUserByDni($dni) {
    $fields = self::$returnFields;
    $sql = "SELECT ${fields} FROM User WHERE dni = :dni";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':dni', $dni, PDO::PARAM_STR);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    self::getUserCompleteData($user);
    return $user;
  }

  public static function getUserByEmail($email) {
    $fields = self::$returnFields;
    $sql = "SELECT ${fields}, password FROM User WHERE email = :email";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    self::getUserCompleteData($user);
    return $user;
  }

  public static function getIdByEmail($email) {
    $sql = "SELECT id FROM User WHERE email = :email;";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public static function getUserById($id) {
    $fields = self::$returnFields;
    $sql = "SELECT ${fields} FROM User WHERE id = :id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    self::getUserCompleteData($user);
    return $user;
  }

  public static function getBasicUserById($id, $lang, $avatar) {
    $fields = self::$returnFields;
    $sql = "SELECT id,initials,tag,name,surname,surname_2,avatar_id,private FROM User WHERE id = :id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    self::getUserBasicData($user, $lang, $avatar);
    return $user;
  }

  public static function getUsersBySearch($text) {
    $text = '%'.$text.'%';
    $fields = self::$returnFields;
    $sql = "SELECT ${fields} FROM User WHERE initials LIKE :text or tag LIKE :text or name LIKE :text or surname LIKE :text;";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':text', $text, PDO::PARAM_STR);
    $statement->execute();
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as &$user) {
      self::getUserCompleteData($user);
    }
    return $users;
  }

  // Create / update user
  public static function saveUser($user, $fiels) {

    /* NAME */
    $name = ucfirst($user['name']);
    $surname = ucfirst($user['surname']);
    $surname_2 = ucfirst($user['surname_2']) ?? null;
    /* DEFAULT VALUES */
    $user['private'] = (isset($user['private'])) ? 1 : 0;
    $user['avatar'] = $user['avatar'] ?? 'null';
    $date = date("Y-m-d H:i:s");
    $coins = 0;
    $points = 0;
    $validated = 1;
    /* SAVE FILES */

    /* INITIALS ANS TAG GENERATE */
    $words = preg_split("/\s+/", "${name} ${surname} ${surname_2}");
    $initials = "";
    foreach ($words as $w) {
      if (isset($w[0])) $initials .= $w[0];
    };
    do{
    $tag = mt_rand(1000, 9999);
    } while(self::getUserByInitialsAndTag($initials, $tag));

    $img = ImageController::saveImages($initials, $tag, $fiels);
    if (!isset($img['dni_img'])) $img['dni_img']['id'] = 1;

    /* SQL BEGIN CONSTRUCTION */
    $fields =  "dni, name, surname, surname_2, email, password, 
                tag, initials, coins, registration_date, points, private, validated,
                city_cp, province_id, avatar_id, dni_photo_id";
    $values =  ":dni, :name, :surname, :surname_2, :email, :password, 
                :tag, :initials, :coins, :registration_date, :points, :private, :validated,
                :city_cp, :province_id, :avatar_id, :dni_photo_id";

    $sql = "INSERT INTO User (${fields}) VALUES (${values})";
    /* SQL END CONSTRUCTION */

    $statement = self::$DB->prepare($sql);

    $statement->bindParam(':dni', $user['dni'], PDO::PARAM_STR);
    $statement->bindParam(':name', $name, PDO::PARAM_STR);
    $statement->bindParam(':surname', $surname, PDO::PARAM_STR);
    $statement->bindParam(':surname_2', $surname_2, PDO::PARAM_STR);
    $statement->bindParam(':email', $user['email'], PDO::PARAM_STR);
    $statement->bindParam(':password', $user['password'], PDO::PARAM_STR);
    $statement->bindParam(':tag', $tag, PDO::PARAM_INT);
    $statement->bindParam(':initials', $initials, PDO::PARAM_STR);
    $statement->bindParam(':coins', $coins, PDO::PARAM_INT);
    $statement->bindParam(':registration_date', $date, PDO::PARAM_STR);
    $statement->bindParam(':points', $points, PDO::PARAM_INT);
    $statement->bindParam(':private', $user['private'], PDO::PARAM_INT);
    $statement->bindParam(':validated', $validated, PDO::PARAM_INT);
    $statement->bindParam(':city_cp', $user['city_cp'], PDO::PARAM_INT);
    $statement->bindParam(':province_id', $user['province_id'], PDO::PARAM_INT);
    $statement->bindParam(':avatar_id', $img['avatar']['id'], PDO::PARAM_INT);
    $statement->bindParam(':dni_photo_id', $img['dni_img']['id'], PDO::PARAM_INT);

    try {
      $statement->execute();
      $errors = $statement->errorInfo();
      if ($errors[1]) return Logger::log("ERROR", $errors);
      $userId = self::getId($initials, $tag);
      TokenController::createNewUser($userId['id'], $user['email']);
      Logger::log("INFO", "New User saved with dni = ${user['dni']}");
      return self::getUserByDni($user['dni']);
    } catch (PDOException $e) {
      var_dump($statement->columnCount());
      Logger::log("ERROR", $e->getMessage());
      return null;
    }
  }

  public static function validateUser($token) {
    $sql = "UPDATE User,Token 
            SET User.validated=1 
            WHERE User.id = Token.user_id 
            AND Token.token = :token";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':token', $token, PDO::PARAM_STR);
    $statement->execute();
  }

  public static function updateUser($email, $city_cp, $private, $user_id) {
    $sql = "UPDATE User 
            SET email = :email, city_cp = :city_cp, private = :private 
            WHERE User.id = :user_id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->bindParam(':city_cp', $city_cp, PDO::PARAM_INT);
    $statement->bindParam(':private', $private, PDO::PARAM_INT);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
  }

  // Exists
  public static function existsDNI($dni) {
    $sql = "SELECT DNI FROM User 
            WHERE User.dni = :dni";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':dni', $dni, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }
  
  public static function existsEmail($email) {
    $sql = "SELECT email FROM User 
            WHERE User.email = :email";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  // User description
  public static function getUserDescription($user_id) {
    $sql = "SELECT language_id, description FROM User_Language WHERE user_id = :user_id ";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);    
  }

  public static function getDescription($user_id, $lang) {
    $sql = "SELECT description FROM User_Language WHERE user_id = :user_id AND language_id = :lang";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public static function insertDescription($user_id, $description, $lang) {
    $sql = "INSERT INTO User_Language(language_id, user_id, description) VALUES(:lang, :user_id, :description)";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':description', $description, PDO::PARAM_STR);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    return $statement->execute();
  }

  public static function updateDescription($user_id, $description, $lang) {
    $sql = "UPDATE User_Language SET description = :description WHERE user_id = :user_id AND language_id = :lang";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':description', $description, PDO::PARAM_STR);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    return $statement->execute();
  }

  public static function deleteDescription($user_id, $lang) {
    $sql = "DELETE FROM User_Language WHERE user_id = :user_id AND language_id = :lang";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    return $statement->execute();
  }

  // PASSWORD
  public static function getPassword($email) {
    $fields = self::$returnFields;
    $sql = "SELECT password,validated,${fields} FROM User WHERE email = :email";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->execute();
    $password = $statement->fetch(PDO::FETCH_ASSOC);
    return $password;
  }

  public static function getPasswordByID($user_id) {
    $sql = "SELECT password FROM User WHERE id = :user_id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public static function updatePassword($password, $user_id) {
    $sql = "UPDATE User
            SET password = :password
            WHERE id = :user_id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':password', $password, PDO::PARAM_STR);
    return $statement->execute();
  }

  public static function resetPasswordToken($password, $token) {
    $sql = "UPDATE User,Token 
            SET User.password = :password
            WHERE User.id = Token.user_id 
            AND Token.token = :token";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':password', $password, PDO::PARAM_STR);
    $statement->bindParam(':token', $token, PDO::PARAM_STR);
    $statement->execute();
  }
}
