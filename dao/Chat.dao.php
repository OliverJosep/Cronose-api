<?php

require_once 'DAO.php';
require_once '../controllers/User.controller.php';

class ChatDAO extends DAO {

  public static function showAllChat($sender, $receiver) {
    $chat['sender'] = self::getUserData($sender);
    $chat['receiver'] = self::getUserData($receiver);
    $sql = "SELECT (select COUNT(*) > 0 from User where sender_id = id and id = :sender) as sended,sended_date,message 
            FROM Message 
            WHERE (sender_id = :sender AND receiver_id = :receiver) OR (sender_id = :receiver AND receiver_id = :sender) 
            ORDER BY sended_date DESC";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':sender', $sender, PDO::PARAM_INT);
    $statement->bindParam(':receiver', $receiver, PDO::PARAM_INT);
    $statement->execute();
    $chat['messages'] = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $chat;
  }

  public static function showChat($sender, $receiver, $offset, $limit) {
    $chat['sender'] = self::getUserData($sender);
    $chat['receiver'] = self::getUserData($receiver);
    $sql = "SELECT (select COUNT(*) > 0 from User where sender_id = id and id = :sender) as sended,sended_date,message 
            FROM Message 
            WHERE (sender_id = :sender AND receiver_id = :receiver) OR (sender_id = :receiver AND receiver_id = :sender) 
            ORDER BY sended_date DESC
            LIMIT :limit OFFSET :offset";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':sender', $sender, PDO::PARAM_INT);
    $statement->bindParam(':receiver', $receiver, PDO::PARAM_INT);
    $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
    $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
    $statement->execute();
    $chat['messages'] = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $chat;
  }

  public static function sendMSG($sender, $receiver, $msg) {
    $sql = "INSERT INTO Message VALUE(:sender, :receiver, now(), :msg);";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':sender', $sender, PDO::PARAM_INT);
    $statement->bindParam(':receiver', $receiver, PDO::PARAM_INT);
    $statement->bindParam(':msg', $msg, PDO::PARAM_STR);
    $statement->execute();
  }

  public static function showChats($user_id) {
    $sql = "select distinct sender_id, receiver_id from Message where receiver_id = :user_id or sender_id = :user_id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getLastMessage($sender, $receiver) {
    $sql = "SELECT (SELECT COUNT(*) > 0 FROM User WHERE sender_id = :sender) AS sended, Message.message, Message.sended_date, Message.satus
            FROM Message WHERE (sender_id = :sender AND receiver_id = :receiver) 
            OR (receiver_id = :sender AND sender_id = :receiver) 
            ORDER BY sended_date DESC LIMIT 1";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':sender', $sender, PDO::PARAM_INT);
    $statement->bindParam(':receiver', $receiver, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);

  }

  public static function getUserData($id, $avatar = true) {
    return UserController::getBasicUserById($id, false, $avatar);
  }

}
