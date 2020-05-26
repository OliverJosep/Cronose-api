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

  public static function checkValorations($user_id) {
    return ValorationDAO::checkValorations($user_id);

  }

  public static function createOfferValoration($card_id, $valorated_by, $text = null, $puntuation = null, $date = null, $valoration_id = 1) {
    $sql = "INSERT INTO Card_Valoration VALUES (:valoration_id, :card_id, :valorated_by, :text, :puntuation, :date)";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':valoration_id', $valoration_id, PDO::PARAM_INT);
    $statement->bindParam(':card_id', $card_id, PDO::PARAM_INT);
    $statement->bindParam(':valorated_by', $valorated_by, PDO::PARAM_INT);
    $statement->bindParam(':text', $text, PDO::PARAM_STR);
    $statement->bindParam(':puntuation', $puntuation, PDO::PARAM_INT);
    $statement->bindParam(':date', $date, PDO::PARAM_STR);
    return $statement->execute();
  }

  public static function createUserValoration($user_id, $valorated_by, $roll, $text = null, $puntuation = null, $date = null, $valoration_id = 1) {
    $sql = "INSERT INTO User_Valoration VALUES (:valoration_id, :user_id, :valorated_by, :roll, :text, :puntuation, :date)";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':valoration_id', $valoration_id, PDO::PARAM_INT);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':valorated_by', $valorated_by, PDO::PARAM_INT);
    $statement->bindParam(':roll', $roll, PDO::PARAM_STR);
    $statement->bindParam(':text', $text, PDO::PARAM_STR);
    $statement->bindParam(':puntuation', $puntuation, PDO::PARAM_INT);
    $statement->bindParam(':date', $date, PDO::PARAM_STR);
    return $statement->execute();
  }


}