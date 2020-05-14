<?php
require_once '../config/config.php';
require_once '../libs/JWT/BeforeValidException.php';
require_once '../libs/JWT/ExpiredException.php';
require_once '../libs/JWT/SignatureInvalidException.php';
require_once '../libs/JWT/JWT.php';

function createJWT($data = []) {
  global $config;
  $payload = [
    "iss" => $config['jwt_iss'],
    "aud" => $config['jwt_aud'],
    "iat" => $config['jwt_iat'],
    "nbf" => $config['jwt_nbf'],
    "data" => $data
  ];

  return JWT::encode($payload, $config['jwt_key']);
}

function decodeJWT($jwt, $callback) {
  global $config;
  try {
    $decoded = JWT::decode($jwt, $config['jwt_key'], array('HS256'));
    $decoded_array = (array) $decoded;
    call_user_func($callback, $decoded_array);
  } catch(Exception $e) {
    http_response_code(401);
    return json_encode(array(
        "message" => "Access denied.",
        "error" => $e->getMessage()
    ));
  }
}

?>