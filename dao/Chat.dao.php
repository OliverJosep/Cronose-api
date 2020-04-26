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

  public static function showChats($receiver) {
    $sql = "SELECT User.name,User.initials,User.tag 
            FROM User,Message where User.id = sender_id 
            AND receiver_id = :receiver 
            GROUP BY sender_id;";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':receiver', $receiver, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getUserData($id) {
    return UserController::getBasicUserById($id);
  }

}
