<?php

require_once '../dao/Token.dao.php';
require_once '../utilities/Mailer.php';

class TokenController {

  public static function createNewUser($userId, $email) {
    
    $title = "User validate";
    $subject = "User_validate";
    $message = "Haga click en el siguiente enlace para validar su cuenta: ";

    self::sendToken($userId, $email, $subject, $message, $title);
  }

  public static function generateResetPassword($email) {
    $userId = UserController::getIdByEmail($email);
    
    $title = "Password reset";
    $subject = "Restore_pswd";
    $message = "Haga click en el siguiente enlace para resetear su contraseña: ";

    self::sendToken($userId['id'], $email, $subject, $message, $title);
  }

  public static function resetPassword($password, $token) {

    UserController::resetPassword($password, $token);
    self::deleteToken($token);

  }

  public static function createToken($userId, $type) {
    return TokenDAO::createToken($userId, $type);
  }

  public static function sendToken($userId, $email, $subject, $message, $title) {
    $token = self::createToken($userId, $subject);

    if ($subject == 'User_validate') $completeMessage = $message . "http://devapi.cronose.dawman.info/validate/${token}";
    if ($subject == 'Restore_pswd') $completeMessage = $message . "http://dev.cronose.dawman.info/resetPassword?token=${token}";

    Mailer::sendMailTo($title, $completeMessage, $email, $from = null, $headers = "");
  }

  public static function deleteToken($token) {
    TokenDAO::deleteToken($token);
  }

}