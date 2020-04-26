<?php

// CORE
require_once '../config/config.php';

// Controllers
require_once '../controllers/Language.controller.php';
require_once '../controllers/Work.controller.php';
require_once '../controllers/User.controller.php';
require_once '../controllers/Chat.controller.php';
require_once '../controllers/Achievement.controller.php';
require_once '../controllers/Category.controller.php';
require_once '../controllers/Specialization.controller.php';
require_once '../controllers/Province.controller.php';
require_once '../controllers/City.controller.php';
require_once '../controllers/Seniority.controller.php';
require_once '../controllers/Valoration.controller.php';
require_once '../controllers/WorkDemand.controller.php';
require_once '../controllers/Coin.controller.php';
require_once '../controllers/Image.controller.php';
require_once '../controllers/Token.controller.php';

// DAO
require_once '../dao/DAO.php';
new DAO();

// Logger
require_once '../utilities/Logger.php';

/** JWT */
require_once '../utilities/JWTManager.php';

/** ROUTER */
require_once '../libs/Router.php';
$router = new Router();

// Categories
$router->get('/categories', function() {
  echo json_encode(CategoryController::getAll());
});
$router->get('/categories/{lang}', function($lang) {
  echo json_encode(CategoryController::getAllByLang($lang));
});

// Specialization
$router->get('/specialization', function() {
  echo json_encode(SpecializationController::getAll());
});
$router->get('/specialization/{lang}/{category_id}', function($lang, $category_id) {
  echo json_encode(SpecializationController::getByLangAndCategory($lang, $category_id));
});
$router->get('/specialization/{lang}', function($lang) {
  echo json_encode(SpecializationController::getAllByLang($lang));
});

//Coins (wallet perquè a n'en Twaia li fa ilusiò)
$router->get('/wallet/{user_id}', function($user_id) {
  echo json_encode(CoinController::getCoinHistory($user_id));
});

// Provinces
$router->get('/provinces', function() {
  echo json_encode(ProvinceController::getAll());
});
$router->get('/province/{id}', function($id) {
  echo json_encode(ProvinceController::getById($id));
});

// Cities
$router->get('/cities/{province_id}', function($province_id) {
  echo json_encode(ProvinceController::getProvinceCities($province_id));
});
$router->get('/cities', function() {
  echo json_encode(CityController::getAll());
});
$router->get('/city/{cp}', function($cp) {
  echo json_encode(CityController::getByCp($cp));
});

// Reset password
$router->get('/reset_password/{email}', function($email) {
  TokenController::generateResetPassword($email);
});
$router->post('/reset_password', function() {
  echo json_encode(TokenController::resetPassword($_POST['password'], $_POST['token']));
});

// User
$router->get('/users', function() {
  echo json_encode(UserController::getAll());
});
          /***REVISAR***/
$router->get('/users/{search}', function($search) {
  echo json_encode(UserController::getUsersBySearch($search));
});
          /*************/
$router->get('/user/{initials}/{tag}/id', function($initial, $tag) {
  echo json_encode(UserController::getId($initial, $tag));
});
$router->get('/user/{initials}/{tag}', function($initial, $tag) {
  echo json_encode(UserController::getUserByInitialsAndTag($initial, $tag));
});
// Register
$router->post('/register', function() {
  echo json_encode(UserController::register($_POST, $_FILES));
});
$router->get('/validate/{token}', function($token) {
  UserController::validateUser($token);
  header('Location: https://www.cronose.dawman.info/userValidator');
});
// Login
$router->post('/login', function() {
  if (isset($_POST['jwt'])) echo decodeJWT($_POST['jwt'], function($data) {
    echo json_encode(UserController::userLogin($data['email'], $data['password']));
  });
  else echo json_encode(UserController::userLogin($_POST['email'], $_POST['password']));
});

// Works
$router->post('/work', function() {
  echo json_encode(WorkController::setNewWork($_REQUEST['data']));
});
$router->get('/works', function() {
  echo json_encode(WorkController::getAllWorks());
});
$router->get('/works/user/{user_id}', function($user_id) {
  echo json_encode(WorkController::getAllWorksByUser($user_id));
});
$router->post('/works/filter', function() {
  echo json_encode(WorkController::getFilteredWorks($_REQUEST['filter']));
});
$router->get('/works/{offset}/{limit}/default/{lang}', function($offset, $limit, $lang) {
  echo json_encode(WorkController::getWorksDefaultLang($limit, $offset, $lang));
});
$router->get('/works/{lang}/{offset}/{limit}', function($lang, $offset, $limit) {
  echo json_encode(WorkController::getWorksByLang($limit, $offset, $lang));
});
$router->get('/works/{offset}/{limit}', function($offset, $limit) {
  echo json_encode(WorkController::getWorks($limit, $offset));
});
$router->get('/work/{initials}/{tag}/{specialization}', function($initials, $tag, $specialization) {
  echo json_encode(WorkController::getWork($initials, $tag, $specialization));
});

// Cards
$router->get('/cards/{worker_id}/{client_id}/{specialization_id}', function($worker_id, $client_id, $specialization_id) {
  echo json_encode(WorkDemandController::getAllCards($worker_id, $client_id, $specialization_id));
});
$router->get('/card/{card_id}', function($card_id) {
  echo json_encode(WorkDemandController::getCard($card_id));
});
$router->get('/card/{status}/{user_id}', function($status, $user_id){
  echo json_encode(WorkDemandController::getAllByStatus($user_id, $status));
});
$router->get('/cards/{user_id}', function($user_id){
  echo json_encode(WorkDemandController::getAll($user_id));
});
// Demands
$router->post('/demand', function(){
  echo json_encode(WorkDemandController::createDemands($_POST['worker_id'], $_POST['client_id'], $_POST['specialization_id']));
});

// Chat
$router->get('/chats/{user_id}', function($user_id) {
  echo json_encode(ChatController::showChats($user_id));
});
$router->get('/chat/{sender_id}/{receiver_id}', function($sender_id, $receiver_id) {
  echo json_encode(ChatController::showChat($sender_id, $receiver_id));
});
$router->post('/chat/{sender_id}/{receiver_id}', function($sender_id, $receiver_id) {
  ChatController::sendMSG($sender_id, $receiver_id, $_POST['msg']);
});

// Achievements
$router->get('/achievement/{user_id}/{achievement}', function($user_id, $achievement) {
  echo json_encode(AchievementController::haveAchi($user_id, $achievement));
});
$router->post('/achievement/{user_id}/{achievement}', function($user_id, $achievement) {
  echo json_encode(AchievementController::setAchievement($user_id, $achievement));
});
$router->get('/achievements/{lang}', function($lang) {
  echo json_encode(AchievementController::getAllByLang($lang));
});
$router->get('/achievements', function() {
  echo json_encode(AchievementController::getAll());
});

// Seniority
$router->get('/seniority/range/{user_id}', function($user_id) {
  echo json_encode(SeniorityController::getRange($user_id));
});
$router->get('/seniority/{user_id}', function($user_id) {
  echo json_encode(SeniorityController::getVet($user_id));
});

// Valoration
$router->get('/valorations/{user_id}/{specialization_id}', function($user_id, $specialization_id) {
  echo json_encode(ValorationController::getWorkerValorations($user_id, $specialization_id));
});

// Error 404
$router->set404(function() {
  header('HTTP/1.1 404 Not Found');https://github.com/MarcJoan/cronose.git
  echo "Error 404, Not Found";
});

$router->run();

?>
