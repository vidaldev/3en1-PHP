<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use Kreait\Firebase\Exception;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

require './middleware/validator.php';

//$app = AppFactory::create();

// Get: Login
$app->get('/login', function(Request $request, Response $response){  
  $response_arg = array(
    "response" => "Loguea con exito"
  );

  $response_arg = json_encode($response_arg);
  $response->getBody()->write($response_arg);
  return $response;
})->add(new validator());

//Post: Forgot Password
$app->post('/forgotPassword', function(Request $request, Response $response){
  $serviceAccount = ServiceAccount::fromJsonFile(getenv('GOOGLE_APPLICATION_CREDENTIALS'));

  $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->create();

  $auth = $firebase->getAuth();

  $data_body = json_decode($request->getBody(), true);
  $email = $data_body['email'] ? $data_body['email'] : '';

  if ($email == '') {
    $fail = array(
      'message' => 'Los parametros estan incompletos, revise la informacion suministrada antes de enviar nuevamente.',
      'reason'=> 'missing parameters'
    );

    $fail = json_encode($fail);

    $response->getBody()->write($fail);
    return $response;
  }

  try {
    $auth->sendPasswordResetEmail($email);

    $arg = array(
      'message'=> 'Correo de recuperacion exitoso',
      'alert' => "Correo de recuperacion enviado a la direccion: $email"
    );

    $arg = json_encode($arg);
    $response->getBody()->write($arg);
    return $response;

  } catch (\Exception $e) {
    $fail = array (
      'message' => 'Ha ocurrido un problema, verifique la informacion suministrada. Si el error persiste comuniquese con el administrador del servicio',
      'email' => 'xtrate@protonmail.com',
      'reason' => 'invalid'
    );

    $fail = json_encode($fail);

    $response->getBody()->write($fail);
    return $response;
  }
});

//Post: Create User
$app->post('/createUser', function(Request $request, Response $response){
  $serviceAccount = ServiceAccount::fromJsonFile(getenv('GOOGLE_APPLICATION_CREDENTIALS'));

  $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->create();

  $auth = $firebase->getAuth();

  $data_body = json_decode($request->getBody(), true);
    
  $email = $data_body['email'] ? $data_body['email'] : '';
  $password = $data_body['password'] ? $data_body['password'] : '';

  if ($email == '' || $password == '') {
    $fail = array(
      'message' => 'Los parametros estan incompletos, revise la informacion suministrada antes de enviar nuevamente.',
      'reason'=> 'missing parameters'
    );

    $fail = json_encode($fail);

    $response->getBody()->write($fail);
    return $response;
  }

  try {
    $user = $auth->createUserWithEmailAndPassword($email, $password);
    $user = json_encode($user);
    
    $user_id = json_decode($user, true)['uid'];
  } catch (\Exception $e) {
    $fail = array (
      'message' => 'Ha ocurrido un problema, verifique la informacion suministrada. Si el error persiste comuniquese con el administrador del servicio',
      'email' => 'xtrate@protonmail.com',
      'reason' => 'invalid'
    );

    $fail = json_encode($fail);

    $response->getBody()->write($fail);
    return $response;
  }

  try {
    $auth->sendEmailVerification($user_id);
    $arg = array(
      'message' => 'Registro exitoso',
      'id' => $user_id,
      'alert' => 'Verifique su correo antes de usar el servicio'
    );

    $arg = json_encode($arg);
    $response->getBody()->write($arg);
    return $response;
  } catch (\Exception $e) {
    $fail = array (
      'message' => 'Ha ocurrido un problema, verifique la informacion suministrada. Si el error persiste comuniquese con el administrador del servicio',
      'email' => 'xtrate@protonmail.com',
      'reason' => 'invalid'
    );

    $fail = json_encode($fail);

    $response->getBody()->write($fail);
    return $response;
  }

});