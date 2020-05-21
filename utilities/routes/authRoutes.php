<?php

class AuthorizedRoutes {

  public static function AuthRoutes($router, $url, $view, $auth) {

    // Password
    $router->post('/reset_password', function() use ($view, $auth) {
      if ($_POST['user_id'] !== $auth['id']) return $view::json(array('Error' => 'Invalid user!'));
      return $view::json(UserController::updatePassword($_POST['password'], $_POST['user_id']));
    });

    // Offers
    $router->mount('/offer', function() use ($router, $view, $auth) {
      $router->post('/translations', function() use ($view, $auth) {
        if ($_POST['user_id'] !== $auth['id']) return $view::json(array('Error' => 'Invalid user!'));
        $view::json(OfferController::updateTranslations($_POST));
      });
      $router->post('/switch', function() use ($view, $auth) {
        if ($_POST['user_id'] !== $auth['id']) return $view::json(array('Error' => 'Invalid user!'));
        $view::json(OfferController::updateVisibility($_POST));
      });
    });

    // Demand card
    $router->post('/demand', function() use ($view, $auth) {
      if ($_POST['user_id'] !== $auth['id']) return $view::json(array('Error' => 'Invalid user!'));
      $view::json(OfferDemandController::createCard($_POST['worker_id'], $_POST['user_id'], $_POST['specialization_id'], $_POST['work_date'], $_POST['cancellation_policy']));
    });

    // Chat
    $router->get('/chats/{user_id}', function($user_id) use ($view, $auth) {
      if ($user_id !== $auth['id']) return $view::json(array('Error' => 'Invalid user!'));
      $view::json(ChatController::showChats($user_id));
    });
    $router->mount('/chat', function() use ($router, $view, $auth) {
      $router->get('/last/{sender_id}/{receiver_id}', function($sender_id, $receiver_id) use ($view) {
        if ($sender_id !== $auth['id']) return $view::json(array('Error' => 'Invalid user!'));
        $view::json(ChatController::showChat($sender_id, $receiver_id, 0, 2, false));
      });
      $router->get('/{sender_id}/{receiver_id}', function($sender_id, $receiver_id) use ($view, $auth) {
        if ($sender_id !== $auth['id']) return $view::json(array('Error' => 'Invalid user!'));
        $view::json(ChatController::showChat($sender_id, $receiver_id));
      });
      $router->post('/{sender_id}/{receiver_id}', function($sender_id, $receiver_id) use ($view, $auth) {
        if ($sender_id !== $auth['id']) return $view::json(array('Error' => 'Invalid user!'));
        ChatController::sendMSG($sender_id, $receiver_id, $_POST['message'], $_POST['sended_date']);
      });
    });
  }

  public static function AuthRoutesLang($router, $url, $lang, $view, $auth) {

    $router->post('/offer', function() use ($view, $lang, $auth) {
      if ($_POST['user_id'] !== $auth['id']) return $view::json(array('Error' => 'Invalid user!'));
      return $view::json(OfferController::setNewOffer($lang, $_POST['user_id'], $_POST['specialization_id'], $_POST['offerTitle'], $_POST['offerDescription']));
    });

  }

}