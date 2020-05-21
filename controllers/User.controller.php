<?php

require_once '../dao/User.dao.php';

// JWT
require_once '../utilities/JWTManager.php';

// Utilities
require_once '../utilities/Mailer.php';
require_once '../utilities/Logger.php';
require_once '../utilities/Language.php';

class UserController {

  // Get users
  public static function getAll($lang = null) {
    $users = UserDAO::getAll();
    foreach ($users as &$user) {
      UserDAO::getUserCompleteData($user, $lang);
    }
    return $users;
  }

  public static function getUserByInitialsAndTag($initials, $tag, $lang = null) {
    $user = UserDAO::getUserByInitialsAndTag($initials, $tag);
    return UserDAO::getUserCompleteData($user, $lang);
  }

  public static function getUserById($user_id) {
    return UserDAO::getUserById($user_id);
  }

  public static function getUsersBySearch($text) {
    return UserDAO::getUsersBySearch($text);
  }

  // Get information of the user
  public static function getAllWorksByUser($user_id) {
    return UserDAO::getAllWorksByUser($user_id);
  }

  public static function getBasicUserById($id, $lang = false, $avatar = false) {
    return UserDAO::getBasicUserById($id, $lang, $avatar);
  }

  public static function getUserDescription($user, $lang = null) {
    if (!is_array($user)) return self::getAllDescriptions($user);

    $user['description'] = UserDAO::getUserDescription($user['id']);
    if (!isset($user['description'][0])) return null;
    if ($lang) $user['description'] = Language::orderByLang($lang, $user['description']);
    return $user['description'];
  }

  public static function getAllDescriptions($user) {
    $descriptions = UserDAO::getUserDescription($user);
    if (count($descriptions) == 3) return $descriptions;
    $langs = [
      0 => 'es', 
      1 => 'en', 
      2 => 'ca'
    ];
    foreach ($langs as $key => $value) {
      foreach ($descriptions as $description) {
        if ($value === $description['language_id']) unset($langs[$key]);
      }
      if (isset($langs[$key])) array_push($descriptions, ['language_id' => $value, 'description' => '']);
    }
    return $descriptions;
  }

  public static function updateDescription($data) {
    $user_id = $data['user_id'];
    unset($data['user_id']);
    foreach ($data as $key => $value) {
      $description = UserDAO::getDescription($user_id, $key);
      if ($description['description'] != $value) UserDAO::updateDescription($user_id, $value, $key);
      if (!$description['description'] && $value != "") UserDAO::insertDescription($user_id, $value, $key);
      if ($value === "") UserDAO::deleteDescription($user_id, $key);
    }
  }

  public static function getAllDirections() {
    return UserDAO::getAllDirections();
  }

  public static function getIdByEmail($email){
    return UserDAO::getIdByEmail($email);
  }

  public static function getId($initials, $tag) {
    return UserDAO::getId($initials, $tag);
  }

  // Login / register
  public static function register($user, $files) {
    return $user = UserDAO::saveUser($user, $files);
  }

  public static function userLogin($email, $password) {
    $user = UserDAO::getPassword($email);
    if ($user['password'] != $password) {
      return ["message" => "Invalid email or password"];
    } else if ($user['validated'] != '1') {
      return ["message" => "You have to validate yout email!"];
    }
    return [
      "user" => UserDAO::getUserCompleteData($user),
      "jwt" => createJWT(["email" => $email, "password" => $password])
    ];
  }

  public static function getAuthData($email, $password) {
    return UserDAO::getAuthData($email, $password);
  }

  public static function validateUser($token) {
    UserDAO::validateUser($token);
  }

  public static function existsDNI($dni) {
    if (UserDAO::existsDNI($dni)) return true;
    return false;
  }

  public static function existsEmail($email) {
    if (UserDAO::existsEmail($email)) return true;
    return false;
  }

  public static function resetPasswordToken($token, $password) {
    return UserDAO::resetPasswordToken($token, $password);
  }

  public static function updateData($data) {
    $private = isset($data['private']) ? true : false;
    UserDAO::updateUser($data['email'], $data['city_cp'], $private, $data['user_id']);
    return $data;
  }

  public static function updatePassword($password, $user_id) {
    return UserDAO::updatePassword($password, $user_id);
  }

  public static function getPassword($user_id) {
    return UserDAO::getPasswordByID($user_id);
  }

}

?>
