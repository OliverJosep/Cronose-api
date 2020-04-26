<?php

require_once 'DAO.php';
require_once '../controllers/User.controller.php';
require_once '../controllers/Comments.controller.php';

class ValorationDAO extends DAO {

  public static function getWorkerValorations($user_id, $specialization_id) {
    $valorations['worker'] = UserController::getBasicUserById($user_id);
    $sql = "SELECT Demands.client_id, Worker_Valoration.puntuation, Worker_Valoration.comment_id 
            FROM Worker_Valoration,Card,Demands
            WHERE Worker_Valoration.card_id = Card.id
            AND Card.demand_id = Demands.id
            AND Demands.worker_id = :user_id
            AND Demands.specialization_id = :specialization_id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':specialization_id', $specialization_id, PDO::PARAM_INT);
    $statement->execute();
    $valorations['valorations'] = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($valorations['valorations'] as &$valoration) {
      $valoration['client'] = UserController::getBasicUserById($valoration['client_id']);
      $valoration['comments'] = CommentsController::getComments($valoration['comment_id']);
      unset($valoration['client_id'],$valoration['comment_id']);
    }
    return $valorations;
  }

}