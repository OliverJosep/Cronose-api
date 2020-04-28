<?php

require_once '../utilities/View.php';
$view = new View();

// Categories
$router->mount('/categories', function() use ($router, $view) {
  $router->get('/', function() use ($view) {
    $view::json(CategoryController::getAll());
  });
  $router->get('/{lang}', function($lang) use ($view) {
    $view::json(CategoryController::getAllByLang($lang));
  });
});

// Specialization
$router->mount('/specialization', function() use ($router, $view) {
  $router->get('/', function() use ($view) {
    $view::json(SpecializationController::getAll());
  });
  $router->get('/{lang}/{category_id}', function($lang, $category_id) use ($view) {
    $view::json(SpecializationController::getByLangAndCategory($lang, $category_id));
  });
  $router->get('/{lang}', function($lang) use ($view) {
    $view::json(SpecializationController::getAllByLang($lang));
  });
});

//Coins
$router->get('/wallet/{user_id}', function($user_id) use ($view) {
  $view::json(CoinController::getCoinHistory($user_id));
});

// Provinces
$router->get('/provinces', function() use ($view){
  $view::json(ProvinceController::getAll());
});
$router->get('/province/{id}', function($id) use ($view) {
  $view::json(ProvinceController::getById($id));
});

// Cities

$router->mount('/cities', function() use ($router, $view) {
  $router->get('/', function() use ($view) {
    $view::json(CityController::getAll());
  });
  $router->get('/{province_id}', function($province_id) use ($view) {
    $view::json(ProvinceController::getProvinceCities($province_id));
  });
});
$router->get('/city/{cp}', function($cp) use ($view) {
  $view::json(CityController::getByCp($cp));
});

// Reset password
$router->mount('/reset_password', function() use ($router, $view) {
  $router->get('/{email}', function($email) {
    TokenController::generateResetPassword($email);
  });
  $router->post('/', function() use ($view) {
    $view::json(TokenController::resetPassword($_POST['password'], $_POST['token']));
  });
});

// User
$router->mount('/users', function() use ($router, $view) {
  $router->get('/', function() use ($view) {
    $view::json(UserController::getAll());
  });          
  $router->get('/{search}', function($search) use ($view) {
    $view::json(UserController::getUsersBySearch($search));/***REVISAR***/
  });
});
$router->mount('/user', function() use ($router, $view) {
  $router->get('/{initials}/{tag}/id', function($initial, $tag) use ($view) {
    $view::json(UserController::getId($initial, $tag));
  });
  $router->get('/{initials}/{tag}', function($initial, $tag) use ($view) {
    $view::json(UserController::getUserByInitialsAndTag($initial, $tag));
  });
});

// Register
$router->post('/register', function() use ($view) {
  $view::json(UserController::register($_POST, $_FILES));
});
$router->get('/validate/{token}', function($token) {
  UserController::validateUser($token);
  header('Location: https://www.cronose.dawman.info/userValidator');
});

// Login
$router->post('/login', function()  use ($view){
  if (isset($_POST['jwt'])) echo decodeJWT($_POST['jwt'], function($data) use ($view) {
    $view::json(UserController::userLogin($data['email'], $data['password']));
  });
  else $view::json(UserController::userLogin($_POST['email'], $_POST['password']));
});

// Works
$router->post('/work', function() use ($view) {
  $view::json(WorkController::setNewWork($_REQUEST['data']));
});
$router->mount('/works', function() use ($router, $view) {
  $router->get('/', function() use ($view) {
    $view::json(WorkController::getAllWorks());
  });
  $router->get('/user/{user_id}', function($user_id) use ($view) {
    $view::json(WorkController::getAllWorksByUser($user_id));
  });
  $router->post('/filter', function() use ($view) {
    $view::json(WorkController::getFilteredWorks($_REQUEST['filter']));
  });
  $router->get('/{offset}/{limit}/default/{lang}', function($offset, $limit, $lang) use ($view) {
    $view::json(WorkController::getWorksDefaultLang($limit, $offset, $lang));
  });
  $router->get('/{lang}/{offset}/{limit}', function($lang, $offset, $limit) use ($view) {
    $view::json(WorkController::getWorksByLang($limit, $offset, $lang));
  });
  $router->get('/{offset}/{limit}', function($offset, $limit) use ($view) {
    $view::json(WorkController::getWorks($limit, $offset));
  });
  $router->get('/{initials}/{tag}/{specialization}', function($initials, $tag, $specialization) use ($view) {
    $view::json(WorkController::getWork($initials, $tag, $specialization));
  });
});


// Cards
$router->mount('/cards', function() use ($router, $view) {
  $router->get('/{worker_id}/{client_id}/{specialization_id}', function($worker_id, $client_id, $specialization_id) use ($view) {
    $view::json(WorkDemandController::getAllCards($worker_id, $client_id, $specialization_id));
  });
  $router->get('/{user_id}', function($user_id) use ($view) {
    $view::json(WorkDemandController::getAll($user_id));
  });
});
$router->mount('/card', function() use ($router, $view) {
  $router->get('/{card_id}', function($card_id) use ($view) {
    $view::json(WorkDemandController::getCard($card_id));
  });
  $router->get('/{status}/{user_id}', function($status, $user_id) use ($view) {
    $view::json(WorkDemandController::getAllByStatus($user_id, $status));
  });
});
// Demands
$router->post('/demand', function() use ($view) {
  $view::json(WorkDemandController::createDemands($_POST['worker_id'], $_POST['client_id'], $_POST['specialization_id']));
});

// Chat
$router->get('/chats/{user_id}', function($user_id) use ($view) {
  $view::json(ChatController::showChats($user_id));
});
$router->mount('/chat', function() use ($router, $view) {
  $router->get('/{sender_id}/{receiver_id}', function($sender_id, $receiver_id) use ($view) {
    $view::json(ChatController::showChat($sender_id, $receiver_id));
  });
  $router->post('/{sender_id}/{receiver_id}', function($sender_id, $receiver_id) use ($view) {
    ChatController::sendMSG($sender_id, $receiver_id, $_POST['msg']);
  });
});

// Achievements
$router->mount('/achievements', function() use ($router, $view) {
  $router->get('/', function() use ($view) {
    $view::json(AchievementController::getAll());
  });
  $router->get('/{lang}', function($lang) use ($view) {
    $view::json(AchievementController::getAllByLang($lang));
  });
});
$router->mount('/achievement', function() use ($router, $view) {
  $router->get('/{user_id}/{achievement}', function($user_id, $achievement) use ($view) {
    $view::json(AchievementController::haveAchi($user_id, $achievement));
  });
  $router->post('/{user_id}/{achievement}', function($user_id, $achievement) use ($view) {
    $view::json(AchievementController::setAchievement($user_id, $achievement));
  });
});

// Seniority
$router->mount('/seniority', function() use ($router, $view) {
  $router->get('/range/{user_id}', function($user_id) use ($view) {
    $view::json(SeniorityController::getRange($user_id));
  });
  $router->get('/{user_id}', function($user_id) use ($view) {
    $view::json(SeniorityController::getVet($user_id));
  });
});

// Valoration
$router->get('/valorations/{user_id}/{specialization_id}', function($user_id, $specialization_id) use ($view) {
  $view::json(ValorationController::getWorkerValorations($user_id, $specialization_id));
});

// Error 404
$router->set404(function() {
  header('HTTP/1.1 404 Not Found');
  echo "Error 404, Not Found";
});

$router->run();