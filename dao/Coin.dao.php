<?php

require_once 'DAO.php';

class CoinDAO extends DAO {
  
  public static function updateCoins($user_id, $coins) {
    $sql = "UPDATE User SET coins=coins+:coins WHERE User.id = :user_id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':coins', $coins, PDO::PARAM_INT);
    $statement->execute();
  }

  public static function getCoins($user_id){
    $sql = "SELECT coins FROM User 
            WHERE id = :user_id";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC)['coins'];
  }

  public static function history($user_id, $limit) {
    $sql = "SELECT Card.id, DATE_FORMAT(Card.work_date, '%d-%m-%Y') as date, Demands.worker_id, Offer.coin_price 
            FROM Card, Demands, Offer WHERE Card.demand_id = Demands.id 
            AND Demands.specialization_id = Offer.specialization_id AND Demands.worker_id = Offer.user_id
            AND (Demands.client_id = :user_id OR Demands.worker_id = :user_id) 
            AND Card.status = 'done'
            ORDER BY Card.work_date DESC limit :limit";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

}