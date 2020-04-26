<?php

require_once 'DAO.php';

class SpecializationDAO extends DAO {

  public static function getAll() {
    $sql = "SELECT id, category_id FROM Specialization";
    $statement = self::$DB->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getAllByLang($lang) {
    $sql = "SELECT Specialization.id, category_id, Specialization_Language.name 
      FROM Specialization, Specialization_Language 
      WHERE Specialization.id = Specialization_Language.specialization_id
      AND language_id = :lang";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getAllByIDAndLang($id, $lang) {
    $sql = "SELECT Specialization.id, Specialization_Language.name as Specialization_name, Category_Language.name as Category_name, Category.id as Category_id
      FROM Specialization_Language, Category_Language, Specialization, Category
      WHERE Specialization_Language.language_id = :lang AND Specialization_Language.specialization_id = :id 
      AND Specialization.id = Specialization_Language.specialization_id AND Specialization.Category_id = Category.id 
      AND Category.id = Category_Language.category_id AND Category_Language.language_id = :lang;";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public static function getByLangAndCategory($lang, $category_id) {
    $sql = "SELECT Specialization.id, Specialization_Language.name 
      FROM Specialization, Specialization_Language
      WHERE Specialization.id = Specialization_Language.specialization_id
      AND Specialization.category_id = :category_id
      AND language_id = :lang";
    $statement = self::$DB->prepare($sql);
    $statement->bindParam(':lang', $lang, PDO::PARAM_STR);
    $statement->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

}