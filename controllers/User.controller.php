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

  public static function getBasicUserById($id, $lang, $avatar = false) {
    return UserDAO::getBasicUserById($id, $lang, $avatar);
  }

  public static function getUserDescription($user, $lang) {
    $user['description'] = UserDAO::getUserDescription($user);
    if (!isset($user['description'][0])) return null;
    if ($lang) $user['description'] = Language::orderByLang($lang, $user['description']);
    return $user['description'];
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
      http_response_code(400);
      return ["message" => "Invalid email or password"];
    } else if ($user['validated'] != '1') {
      http_response_code(400);
      return ["message" => "You have to validate yout email!"];
    }
    return [
      "user" => UserDAO::getUserCompleteData($user),
      "jwt" => createJWT(["email" => $email, "password" => $password])
    ];
  }

  public static function validateUser($token) {
    UserDAO::validateUser($token);
  }

  public static function resetPassword($token, $password) {
    return UserDao::resetPassword($token, $password);
  }

}

?>
