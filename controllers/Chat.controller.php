<?php

require_once '../dao/Chat.dao.php';
require_once 'Achievement.controller.php';
require_once 'User.controller.php';

class ChatController {

  public static function showAllChat($sender, $receiver) {
    return ChatDAO::showAllChat($sender, $receiver);
  }

  public static function showChat($sender, $receiver, $offset = 0, $limit = 25) {
    return ChatDAO::showChat($sender, $receiver, $offset, $limit);
  }

  public static function sendMSG($sender, $receiver, $msg) {
    ChatDAO::sendMSG($sender, $receiver, $msg);
    $achi_id = 2;
    if ( !AchievementController::haveAchi($sender, $achi_id) ) {
      AchievementController::setAchievement($sender, $achi_id);
    }
  }

  public static function showChats($sender) {
    $chats = ChatDAO::showChats($sender);
    foreach ($chats as $value => $key) {
      foreach ($chats as $val){
        if ($val["sender_id"] == $key["receiver_id"] && $val["receiver_id"] == $key["sender_id"]) {
          unset($chats[$value]);
        }
      }
    }
    
    foreach ($chats as &$user) {
      $user['reciver'] = ($sender === $user["sender_id"]) ? ChatDAO::getUserData($user["receiver_id"]) : ChatDAO::getUserData($user["sender_id"]) ;
      $user['last'] = ChatDAO::getLastMessage($user["sender_id"],$user["receiver_id"]);
    }
    $chats = array('user' => UserController::getBasicUserById($sender, false, true)) + $chats;
    return $chats;
  }

}
