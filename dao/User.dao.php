<?php

require_once 'DAO.php';

require_once '../controllers/Media.controller.php';
require_once '../controllers/Address.controller.php';
require_once '../controllers/Seniority.controller.php';
require_once '../controllers/Achievement.controller.php';
require_once '../controllers/Image.controller.php';
require_once '../controllers/Token.controller.php';

// Logger
require_once '../utilities/Logger.php';

class UserDAO extends DAO {

  private static $returnFields = "id, initials, tag, email, name, surname, surname_2, coins, registration_date, points, avatar_id as avatar, private, city_cp as city, province_id as province";

  public static function getUserCompleteData(&$user) {
    // Unset name in case of private user
    $user['full_name'] = "${user['name']} ${user['surname']} ${user['surname_2']}";
    if ($user['private']) unset($user['name'], $user['surname'], $user['surname_2'], $user['full_name']);

    $user['avatar'] = MediaController::getById($user['avatar']) ?? 'sample_avatar';
    $user['address'] =  AddressController::getUserAddress($user);
    $user['achievements'] = AchievementController::getAllByUser($user['id']);
    $user['full_name'] = "${user['name']} ${user['surname']} ${user['surname_2']}";
    // $user['Seniority'] = SeniorityController::getRange($user);

    // Unset not necessary information
    unset($user['city'], $user['province'], $user['private'], $user['password'], $user['validated']);
    return $user;
  }

  private static function getUserBasicData(&$user, $avatar) {
    // Unset name in case of private user
    $user['full_name'] = "${user['name']} ${user['surname']} ${user['surname_2']}";
    if ($user['private']) unset($user['name'], $user['surname'], $user['surname_2'], $user['full_name']);

    if ($avatar) $user['avatar'] = MediaController::getById($user['avatar_id']);

    // Unset not necessary information
    unset($user['id'], $user['avatar_id'], $user['private']);
  }

  public static function getAll() {
    $fields = self::$returnFields;
    $sql = "SELECT ${fields} FROM User";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as &$user) {
      self::getUserCompleteData($user);
    }
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

  public static function getPassword($email) {
    $fields = self::$returnFields;
    $sql = "SELECT password,validated,${fields} FROM User WHERE email = :email";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->execute();
    $password = $statement->fetch(PDO::FETCH_ASSOC);
    return $password;
  }

  public static function getBasicUserById($id, $avatar) {
    $fields = self::$returnFields;
    $sql = "SELECT initials,tag,name,surname,surname_2,avatar_id,private FROM User WHERE id = :id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    self::getUserBasicData($user, $avatar);
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

  public static function saveUser($user, $fiels) {

    /* DEFAULT VALUES */
    $surname = ucfirst($user['surname_2']) ?? "";
    $user['private'] = (isset($user['private'])) ? 1 : 0;
    $user['avatar'] = $user['avatar'] ?? 'null';
    /* SAVE FILES */

    /* INITIALS ANS TAG GENERATE */
    $words = preg_split("/\s+/", "${user['name']} ${user['surname']} ${user['surname_2']}");
    $initials = "";
    foreach ($words as $w) {
      $initials .= $w[0];
    };
    do{
    $tag = mt_rand(1000, 9999);
    } while(self::getUserByInitialsAndTag($initials, $tag));

    $img = ImageController::saveImages($initials, $tag, $fiels);
    $name = ucfirst($user['name']);
    $surname = ucfirst($user['surname']);

    /* SQL BEGIN CONSTRUCTION */
    $fields = "dni, name, surname, surname_2, email, password, tag, initials, coins, registration_date, points, private, city_cp, province_id, avatar_id, dni_photo_id";
    $values = "'${user['dni']}', '${name}', '${surname}', '${surname_2}', '${user['email']}', '${user['password']}', ";
    
    $date = date("Y-m-d H:i:s");
    $values = $values."${tag}, '${initials}', 0, '${date}', 0, ${user['private']}, ${user['city_cp']}, ${user['province_id']}, ";
    $values .= $img['avatar']['id'] . ', ' . $img['dni_img']['id'];
    $sql = "INSERT INTO User (${fields}) VALUES (${values})";
    /* SQL END CONSTRUCTION */
    $statement = self::$DB->prepare($sql);
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

  public static function getUserById($user_id) {
    $sql = "SELECT * FROM User where id = :user_id;";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public static function getIdByEmail($email) {
    $sql = "SELECT id FROM User WHERE email = :email;";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public static function resetPassword($password, $token) {
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
