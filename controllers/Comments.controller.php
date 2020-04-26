<?php

require_once '../dao/Comments.dao.php';

class CommentsController {

  public static function getComments($id){
    return CommentsDAO::getComments($id);
  }

}