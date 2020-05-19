<?php

require_once 'DAO.php';
require_once '../controllers/User.controller.php';

class OfferDemandDAO extends DAO {

  public static function getCard($card_id) {
    $sql = "SELECT *
            FROM Card, Demands 
            WHERE Card.demand_id = Demands.id
            AND Card.id = :card_id;";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':card_id', $card_id, PDO::PARAM_INT);
    $statement->execute();
    $card = $statement->fetch(PDO::FETCH_ASSOC);
    return $card;
  }

  public static function getAllCards($worker_id, $client_id) {
    $sql = "SELECT Card.id FROM Card,Demands 
            WHERE Demands.id = Card.demand_id
            AND Demands.client_id = :client_id
            AND Demands.worker_id = :worker_id
            ORDER BY Demands.demanded_at DESC";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':client_id', $client_id, PDO::PARAM_INT);
    $statement->bindParam(':worker_id', $worker_id, PDO::PARAM_INT);
    $statement->execute();
    $cards = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cards as $value => $key) {
      $cards[$value] = self::getCard($cards[$value]['id']);
    }
    return $cards;
  }

  public static function getAll($user_id) {

    $sql = "SELECT * From Card WHERE (client_id = :user_id) OR (worker_id = :user_id);";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);

  }

  public static function getAllByStatus($user_id, $status) {
    $sql = "SELECT *, Demands.id as Demand_id From Card INNER JOIN Demands ON demands.id = Card.demand_id AND (demands.worker_id = :user_id OR demands.client_id = :user_id) AND card.status = :status;";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':status', $status, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function createCard($work_date, $cancelation_policy, $demand_id, $qr_code) {
    $sql = "INSERT INTO `Card` (`id`, `status`, `work_date`, `qr_code_id`, `cancelation_policy_id`, `demand_id`) 
            VALUES (NULL, 'pending', :work_date, :qr_code, :cancelation_policy, :demand_id)";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':work_date', $work_date, PDO::PARAM_STR);
    $statement->bindParam(':qr_code', $qr_code, PDO::PARAM_INT);
    $statement->bindParam(':cancelation_policy', $cancelation_policy, PDO::PARAM_INT);
    $statement->bindParam(':demand_id', $demand_id, PDO::PARAM_INT);
    return $statement->execute();
  }

  // Demands
  public static function createDemands($worker_id, $client_id, $specialization_id) {
    $sql = "INSERT INTO Demands VALUES (NULL, :client_id, :worker_id, :specialization_id, CURRENT_TIMESTAMP)";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':client_id', $client_id, PDO::PARAM_INT);
    $statement->bindParam(':worker_id', $worker_id, PDO::PARAM_INT);
    $statement->bindParam(':specialization_id', $specialization_id, PDO::PARAM_INT);
    return $statement->execute();
  }

  public static function getDemandsId($worker_id, $client_id, $specialization_id) {
    $sql = "SELECT id FROM Demands
            WHERE worker_id = :worker_id
            AND client_id = :client_id
            AND specialization_id = :specialization_id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':worker_id', $worker_id, PDO::PARAM_INT);
    $statement->bindParam(':client_id', $client_id, PDO::PARAM_INT);
    $statement->bindParam(':specialization_id', $specialization_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

}