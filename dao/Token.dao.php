<?php

require_once 'DAO.php';


class TokenDAO extends DAO {

  public static function createToken($userId, $type) {
    $token = bin2hex(random_bytes(16));
    $sql = "INSERT into Token (user_id, token, name) values (:userId, :token, :type)";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
    $statement->bindParam(':token', $token, PDO::PARAM_STR);
    $statement->bindParam(':type', $type, PDO::PARAM_STR);
    $statement->execute();
    return $token;
  }

  public static function deleteToken($token) {
    $sql = "DELETE FROM Token WHERE Token.token = :token";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':token', $token, PDO::PARAM_STR);
    $statement->execute();
  }

}
