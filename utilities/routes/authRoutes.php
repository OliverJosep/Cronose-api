<?php

class AuthRoutes {

  public static function AuthRoutesLang($router, $url, $lang, $view, $auth) {
    $router->post('/offer', function() use ($view, $lang, $auth) {
      if ($_POST['user_id'] !== $auth['id']) return $view::json(array('Error' => 'Invalid user!'));
      return $view::json(OfferController::setNewOffer($lang, $_POST['user_id'], $_POST['specialization_id'], $_POST['offerTitle'], $_POST['offerDescription']));
    });

  }

}