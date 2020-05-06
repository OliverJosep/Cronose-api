<?php

require_once '../dao/Chat.dao.php';
require_once 'Achievement.controller.php';
require_once 'User.controller.php';

class ChatController {

  public static function showAllChat($sender, $receiver) {
    return ChatDAO::showAllChat($sender, $receiver);
  }

  public static function showChat($sender, $receiver, $offset = 0, $limit = 25, $user = true) {
    $chat = ChatDAO::showChat($sender, $receiver, $offset, $limit, $user);
    $chat['messages'] = array_reverse($chat['messages']);
    return $chat;
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
    $chats = array_values($chats);
    
    foreach ($chats as &$user) {
      $user['reciver'] = ($sender === $user["sender_id"]) ? ChatDAO::getUserData($user["receiver_id"]) : ChatDAO::getUserData($user["sender_id"]) ;
      $user['last'] = ChatDAO::getLastMessage($sender,$user["reciver"]['id']);
      unset($user["sender_id"], $user["receiver_id"]);
    }
    $chats = self::sortLastMessage($chats);
    $chats = array('user' => UserController::getBasicUserById($sender, false, true)) + array('chats' => $chats);
    return $chats;
  }

  public static function sortLastMessage($chats) {
    do {
      for ($i = 0; $i < count($chats) - 1; $i++) {
        $sorted = false;
        $date1 = explode(' ', $chats[$i]['last']['sended_date']);
        $date2 = explode(' ', $chats[$i+1]['last']['sended_date']);
        if (strtotime($date1[0]) < strtotime($date2[0]) || ($date1[0] === $date2[0] && $date1[1] < $date2[1]) ){
          $aux = $chats[$i];
          $chats[$i] = $chats[$i+1];
          $chats[$i+1] = $aux;
          $sorted = true;
        }
      };
    } while ($sorted);
    return $chats;
  }
  

}
