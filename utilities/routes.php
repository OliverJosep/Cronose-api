<?php

/* ROUTER */
require_once '../libs/Router.php';
$router = new Router();

require_once '../utilities/View.php';
$view = new View();

$avaliable_langs = ['ca','es','en','it'];
$url = explode("/", trim($_SERVER['REQUEST_URI'], "/"));

# Url with a language
if (in_array($lang = $url[0], $avaliable_langs)) {

  unset($url[0]);
  $_SERVER['REQUEST_URI'] = '/'.implode('/', $url);

  // Categories
  $router->get('/categories', function() use ($view, $lang) {
    $view::json(CategoryController::getAllByLang($lang));
  });
  $router->get('/category/{category_id}', function($category_id) use ($view, $lang) {
    $view::json(SpecializationController::getByLangAndCategory($lang, $category_id));
  });

  // Specializations
  $router->mount('/specialization', function() use ($router, $view, $lang) {
    $router->get('/', function() use ($view, $lang) {
      $view::json(SpecializationController::getAllByLang($lang));
    });
    $router->get('/{specialization_id}', function($specialization_id) use ($view, $lang) {
      $view::json(SpecializationController::getByLang($lang, $specialization_id));
    });
  });

  // Users
  $router->get('/users', function() use ($view, $lang) {
    $view::json(UserController::getAll($lang)); // Get all users information with a default lang.
  });
  $router->get('/user/{initials}/{tag}', function($initial, $tag) use ($view, $lang) {
    $view::json(UserController::getUserByInitialsAndTag($initial, $tag, $lang)); // Get all user information with a default lang.
  });

  // Offer
  $router->get('/offer/{initials}/{tag}/{specialization}', function($initials, $tag, $specialization) use ($view, $lang) {
    $view::json(OfferController::getOffer($initials, $tag, $specialization, $lang));
  });
  $router->post('/offer', function() use ($view, $lang) {
    $view::json(OfferController::setNewOffer($lang, $_REQUEST['data']));
  });
  $router->mount('/offers', function() use ($router, $view, $lang) {
    $router->get('/', function() use ($view, $lang) {
      $view::json(OfferController::getAllOffers($lang));
    });
    $router->get('/user/{user_id}/all', function($user_id) use ($view, $lang) {
      $view::json(OfferController::getOffersByUser($user_id, $lang, false));
    });  
    $router->get('/user/{user_id}', function($user_id) use ($view, $lang) {
      $view::json(OfferController::getOffersByUser($user_id, $lang));
    });  
    $router->get('/all/{offset}/{limit}', function($offset, $limit) use ($view, $lang) {
      $view::json(OfferController::getOffersDefaultLang($limit, $offset, $lang));
    });
    $router->get('/{offset}/{limit}', function($offset, $limit) use ($view, $lang) {
      $view::json(OfferController::getOffersByLang($limit, $offset, $lang));
    });
  });

  // Achievements
  $router->get('/achievements', function() use ($view, $lang) {
    $view::json(AchievementController::getAllByLang($lang));
  });

  // Cards
  $router->get('/cards/{worker_id}/{client_id}', function($worker_id, $client_id) use ($view, $lang) {
    $view::json(OfferDemandController::getAllCards($worker_id, $client_id, $lang));
  });

  // Cancelation
  $router->get('/cancellations', function() use ($view, $lang) {
    $view::json(CancellationController::getAll($lang));
  });
};

// All Categories
$router->get('/categories', function() use ($view) {
  $view::json(CategoryController::getAll());
});

// All Specializations ------- Not working
$router->get('/specialization', function() use ($view) {
  $view::json(SpecializationController::getAll());
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
    $view::json(UserController::updatePassword($_POST));
  });
  $router->post('/token', function() use ($view) {
    $view::json(TokenController::resetPassword($_POST['password'], $_POST['token']));
  });
});
$router->get('/password/{user_id}', function($user_id) use ($view) {
  $view::json(UserController::getPassword($user_id));
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
  $router->get('/id/{id}', function($id) use ($view) {
    $view::json(UserController::getUserById($id));
  });
  $router->get('/description/{id}', function($user_id) use ($view) {
    $view::json(UserController::getUserDescription($user_id));
  });
  $router->post('/description', function() use ($view) {
    echo json_encode(UserController::updateDescription($_POST));
  });
  $router->get('/{initials}/{tag}/id', function($initial, $tag) use ($view) {
    $view::json(UserController::getId($initial, $tag));
  });
  $router->get('/{initials}/{tag}', function($initial, $tag) use ($view) {
    $view::json(UserController::getUserByInitialsAndTag($initial, $tag)); // Falta la descripció de l'usuari.
  });
  // $router->post('/update/description', function() {
  //   // echo json_encode(UserController::updateData($_POST));
  // });
  $router->post('/update', function() {
    echo json_encode(UserController::updateData($_POST));
  });
  $router->post('/avatar/update', function() {
    echo json_encode(ImageController::updateImages($_POST['user_initials'], $_POST['user_tag'], $_FILES['avatar'], 'avatar'));
  });
  $router->post('/avatar/visible', function() {
    echo json_encode(ImageController::active($_POST['media_id'], $_POST['visible']));
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
$router->get('/dni', function() use ($view){
  $view::json(UserController::existsDNI($_GET['dni']));
});
$router->get('/email', function() use ($view){
  $view::json(UserController::existsEmail($_GET['email']));
});

// Login
$router->post('/login', function()  use ($view){
  if (isset($_POST['jwt'])) echo decodeJWT($_POST['jwt'], function($data) use ($view) {
    $view::json(UserController::userLogin($data['data']->email, $data['data']->password));
  });
  else $view::json(UserController::userLogin($_POST['email'], $_POST['password']));
});

// Job Offers
$router->mount('/offer', function() use ($router, $view) {
  $router->get('/translations', function() use ($view) {
    $view::json(OfferController::getTranslations($_GET));
  });
  $router->post('/translations', function() use ($view) {
    $view::json(OfferController::updateTranslations($_POST));
  });
  $router->get('/visible', function() use ($view) {
    $view::json(OfferController::getVisibility($_GET));
  });
  $router->post('/switch', function() use ($view) {
    $view::json(OfferController::updateVisibility($_POST));
  });
});
$router->post('/job-offers/filter', function() use ($view) {
  $view::json(OfferController::getFilteredOffers($_REQUEST['filter']));
});

// Cards
$router->mount('/cards', function() use ($router, $view) {
  // $router->get('/{worker_id}/{client_id}', function($worker_id, $client_id) use ($view) {
  //   $view::json(OfferDemandController::getAllCards($worker_id, $client_id));
  // });
  $router->get('/{user_id}', function($user_id) use ($view) {
    $view::json(OfferDemandController::getAll($user_id));
  });
});
$router->mount('/card', function() use ($router, $view) {
  $router->get('/{card_id}', function($card_id) use ($view) {
    $view::json(OfferDemandController::getCard($card_id));
  });
  $router->get('/{status}/{user_id}', function($status, $user_id) use ($view) {
    $view::json(OfferDemandController::getAllByStatus($user_id, $status));
  });
});
// Demands
$router->post('/demand', function() use ($view) {
  $view::json(OfferDemandController::createCard($_POST['worker_id'], $_POST['client_id'], $_POST['specialization_id'], $_POST['work_date'], $_POST['cancelation_policy']));
});

// Chat
$router->get('/chats/{user_id}', function($user_id) use ($view) {
  $view::json(ChatController::showChats($user_id));
  // ChatControkller::showChats($user_id);
});
$router->mount('/chat', function() use ($router, $view) {
  $router->get('/last/{sender_id}/{receiver_id}', function($sender_id, $receiver_id) use ($view) {
    $view::json(ChatController::showChat($sender_id, $receiver_id, 0, 2, false));
  });
  $router->get('/{sender_id}/{receiver_id}', function($sender_id, $receiver_id) use ($view) {
    $view::json(ChatController::showChat($sender_id, $receiver_id));
  });
  $router->post('/{sender_id}/{receiver_id}', function($sender_id, $receiver_id) use ($view) {
    ChatController::sendMSG($sender_id, $receiver_id, $_POST['message'], $_POST['sended_date']);
    // var_dump($_POST);
  });
});

// Achievements
$router->get('/achievements', function() use ($view) {
  $view::json(AchievementController::getAll());
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