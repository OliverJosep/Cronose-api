<?php

/* ROUTER */
require_once '../libs/Router.php';
$router = new Router();

require_once '../utilities/View.php';
$view = new View();

// API authentication
require_once 'authRoutes.php';
$auth = (isset($_REQUEST['jwt'])) ? validateJWT($_REQUEST['jwt']) :  false;

// Langs
$avaliable_langs = ['ca','es','en'];
$url = explode("/", trim($_SERVER['REQUEST_URI'], "/"));

# Url with a language
if (in_array($lang = $url[0], $avaliable_langs)) {

  unset($url[0]);
  $_SERVER['REQUEST_URI'] = '/'.implode('/', $url);

  // Authenticated routes with lang
  if (!isset($auth['error'])) echo AuthorizedRoutes::AuthRoutesLang($router, $url, $lang, $view, $auth);

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

  // Authenticated routes
  if (!isset($auth['error'])) echo AuthorizedRoutes::AuthRoutes($router, $url, $view, $auth);

// All Categories
$router->get('/categories', function() use ($view) {
  $view::json(CategoryController::getAll());
});

// All Specializations
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
    $view::json(UserController::getUsersBySearch($search));
  });
});
$router->mount('/user', function() use ($router, $view) {
  $router->get('/id/{id}', function($id) use ($view) {
    $view::json(UserController::getUserById($id));
  });
  $router->get('/description/{id}', function($user_id) use ($view) {
    $view::json(UserController::getUserDescription($user_id));
  });
  $router->get('/{initials}/{tag}/id', function($initial, $tag) use ($view) {
    $view::json(UserController::getId($initial, $tag));
  });
  $router->get('/{initials}/{tag}', function($initial, $tag) use ($view) {
    $view::json(UserController::getUserByInitialsAndTag($initial, $tag)); 
  });
});

// ValidateJWT
$router->post('/validateJWT', function() use ($view) {
  echo validateJWT($_POST['jwt']);
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
    $view::json(UserController::userLogin($data['email'], $data['password']));
  });
  else $view::json(UserController::userLogin($_POST['email'], $_POST['password']));
});

// Job Offers
$router->mount('/offer', function() use ($router, $view) {
  $router->get('/translations', function() use ($view) {
    $view::json(OfferController::getTranslations($_GET));
  });
  $router->get('/visible', function() use ($view) {
    $view::json(OfferController::getVisibility($_GET));
  });
});
$router->get('/offers/filter', function() use ($view) {
  $view::json(OfferController::getFilteredOffers());
});

// Cards
$router->mount('/cards', function() use ($router, $view) {
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

// Valorations
$router->get('/valorations/{user_id}/{specialization_id}', function($user_id, $specialization_id) use ($view) {
  $view::json(ValorationController::getWorkerValorations($user_id, $specialization_id));
});
$router->post('/valoration', function() use ($view) {
  $view::json(ValorationController::updateValoration());
});

// Error 404
$router->set404(function() {
  header('HTTP/1.1 404 Not Found');
  echo "Error 404, Not Found";
});

$router->run();