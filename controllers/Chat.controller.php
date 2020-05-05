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
    // foreach ($chats as &$chat) {
    //   $chat['last'] = ChatDAO::getLastMessage($sender, $chat['receiver_id']);
    // }
    return $chats;
  }

}
