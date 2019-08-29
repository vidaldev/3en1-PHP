<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use Kreait\Firebase\Exception;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

use Google\Cloud\Firestore\FirestoreClient;

//Post: Crear alquilar 
$app->post('/alquilar', function(Request $request, Response $response){
  $data_body = json_decode($request->getBody(), true);
  $var_color = $data_body['color'] ? $data_body['color'] : '';
  $var_marca = $data_body['marca'] ? $data_body['marca'] : '';
  $var_year = $data_body['year'] ? $data_body['year'] : '';
  $var_responsable = $data_body['responsable'] ? $data_body['responsable'] : '';
  $var_modelo = $data_body['modelo'] ? $data_body['modelo'] : '';
  $var_uid = $request->getAttribute('uid');
  $today = date("d/m/Y");

  if ($var_color == '' || $var_marca == '' || $var_year == '' || $var_responsable == '' || $var_modelo == '') {
    $fail = array(
      'message' => 'Los parametros estan incompletos, revise la informacion suministrada antes de enviar nuevamente.',
      'reason'=> 'missing parameters'
    );

    $fail = json_encode($fail);
    $response->getBody()->write($fail);
    return $response->withStatus(401);
  }

  $arg_new = array(
    'uid' => $var_uid,
    'modelo' => $var_modelo,
    'color' => $var_color,
    'marca' => $var_marca,
    'year' => $var_year,
    'responsable' => $var_responsable,
    'dia'=> $today,
    'status' => 'pendiente'
  );


  try {
    $db = new FirestoreClient();

    $rentalsRef = $db->collection('rentals')->add($arg_new);

    $success = array(
      'message' => 'El alquiler del vehiculo se ha insertado correctamente.',
      'reason'=> 'sucess'
    );

    $success = json_encode($success);
    $response->getBody()->write($success);
    return $response;
  } catch (\Exception $e){
    $fail = array(
      'message' => 'Ha ocurrido un problema, verifique la informacion suministrada. Si el error persiste comuniquese con el administrador del servicio',
      'email' => 'xtrate@protonmail.com',
      'reason' => 'invalid'
    );

    $fail = json_encode($fail);
    $response->getBody()->write($fail);
    return $response;
  }
})->add(new validator());

//Post: Corregir Datos de alquiler 
$app->post('/corregirDatos', function(Request $request, Response $response){
  $data_body = json_decode($request->getBody(), true);
  $var_id = $data_body['id'] ? $data_body['id'] : '';

  if ($var_id == '') 
  {
    $fail = array(
      'message' => 'Los parametros estan incompletos, revise la informacion suministrada antes de enviar nuevamente.',
      'reason'=> 'missing parameters'
    );

    $fail = json_encode($fail);
    $response->getBody()->write($fail);
    return $response;
  }

  $var_color = $data_body['color'] ? $data_body['color'] : '';
  $var_marca = $data_body['marca'] ? $data_body['marca'] : '';
  $var_year = $data_body['year'] ? $data_body['year'] : '';
  $var_responsable = $data_body['responsable'] ? $data_body['responsable'] : '';
  $var_modelo = $data_body['modelo'] ? $data_body['modelo'] : '';

  $modify_arg = [];

  if ($var_color != '') {$modify_arg['color'] = $var_color;}
  if ($var_marca != '') {$modify_arg['marca'] = $var_marca;}
  if ($var_year != '') {$modify_arg['year'] = $var_year;}
  if ($var_responsable != '') {$modify_arg['responsable'] = $var_responsable;}
  if ($var_modelo != '') {$modify_arg['modelo'] = $var_modelo;}

  try {
    $db = new FirestoreClient();

    $rentalsRef = $db->collection('rentals')->document($var_id)->set($modify_arg,['merge'=>true]);

    $success = array(
      'message' => 'La data ha sido actualizada correctamente.',
      'reason'=> 'successful update'
    );

    $success = json_encode($success);
    $response->getBody()->write($success);
    return $response;
  } catch (\Exception $e){
    $fail = array(
      'message' => 'Ha ocurrido un problema, verifique la informacion suministrada. Si el error persiste comuniquese con el administrador del servicio',
      'email' => 'xtrate@protonmail.com',
      'reason' => 'invalid'
    );

    $fail = json_encode($fail);
    $response->getBody()->write($fail);
    return $response;
  }
})->add(new validator());

//Post: Mostrar todos los alquileres
$app->post('/alquileres', function(Request $request, Response $response){
  $data_body = json_decode($request->getBody(), true);

  $var_filtro = $data_body['filtro'] ? $data_body['filtro'] : '';

  if ($var_filtro == '') 
  {
    $fail = array(
      'message' => 'Los parametros estan incompletos, revise la informacion suministrada antes de enviar nuevamente.',
      'reason'=> 'missing parameters'
    );

    $fail = json_encode($fail);
    $response->getBody()->write($fail);
    return $response;
  }

  if ($var_filtro != 'todo' && $var_filtro != 'pendiente' && $var_filtro != 'entregado')
  {
    $fail = array(
      'message' => 'Ha ocurrido un problema, verifique la informacion suministrada. Si el error persiste comuniquese con el administrador del servicio',
      'email' => 'xtrate@protonmail.com',
      'reason' => 'invalid'
    );

    $fail = json_encode($fail);
    $response->getBody()->write($fail);
    return $response;
  }

  try {
    $db = new FirestoreClient();
    $docs_arg = [];

    if ($var_filtro == 'todo'){
      $rentalsRef = $db->collection('rentals')->documents();
    }

    if ($var_filtro == 'pendiente' || $var_filtro == 'entregado') {
      $rentalsRef = $db->collection('rentals')->where('status', '=', $var_filtro)->documents();
    }

    $i = 0;
    foreach ($rentalsRef as $document) {
      if ($document->exists()) {
        $data = $document->data();
        $docs_arg[$i] = $data;
        $i++;
      }
    }
    
    $docs_arg = json_encode($docs_arg);
  
    $response->getBody()->write($docs_arg);
    return $response;
  } catch (\Exception $e){
    $fail = array(
      'message' => 'Ha ocurrido un problema, verifique la informacion suministrada. Si el error persiste comuniquese con el administrador del servicio',
      'email' => 'xtrate@protonmail.com',
      'reason' => 'invalid'
    );

    $fail = json_encode($fail);
    $response->getBody()->write($fail);
    return $response;
  }
})->add(new validator());

//Post: Mostrar los alquileres por usuarios
$app->post('/alquileres/user', function(Request $request, Response $response){
  $uid = $request->getAttribute('uid');
  
  $data_body = json_decode($request->getBody(), true);

  $var_filtro = $data_body['filtro'] ? $data_body['filtro'] : '';

  if ($var_filtro == '') 
  {
    $fail = array(
      'message' => 'Los parametros estan incompletos, revise la informacion suministrada antes de enviar nuevamente.',
      'reason'=> 'missing parameters'
    );

    $fail = json_encode($fail);
    $response->getBody()->write($fail);
    return $response;
  }

  if ($var_filtro != 'todo' && $var_filtro != 'pendiente' && $var_filtro != 'entregado')
  {
    $fail = array(
      'message' => 'Ha ocurrido un problema, verifique la informacion suministrada. Si el error persiste comuniquese con el administrador del servicio',
      'email' => 'xtrate@protonmail.com',
      'reason' => 'invalid'
    );

    $fail = json_encode($fail);
    $response->getBody()->write($fail);
    return $response;
  }

  try {
    $db = new FirestoreClient();
    $docs_arg = [];

    if ($var_filtro == 'todo'){
      $rentalsRef = $db->collection('rentals')->where('uid', '=', $uid)->documents();
    }

    if ($var_filtro == 'pendiente' || $var_filtro == 'entregado') {
      $rentalsRef = $db->collection('rentals')->where('uid', '=', $uid)->where('status', '=', $var_filtro)->documents();
    }

    foreach ($rentalsRef as $document) {
      if ($document->exists()) {
        $data = $document->data();
        $docs_arg[$document->id()] = $data;
      }
    }
    
    $docs_arg = json_encode($docs_arg);
  
    $response->getBody()->write($docs_arg);
    return $response;
  } catch (\Exception $e){
    $fail = array(
      'message' => 'Ha ocurrido un problema, verifique la informacion suministrada. Si el error persiste comuniquese con el administrador del servicio',
      'email' => 'xtrate@protonmail.com',
      'reason' => 'invalid'
    );

    $fail = json_encode($fail);
    $response->getBody()->write($fail);
    return $response;
  }
})->add(new validator());

//Post: Cerrar alquiler
$app->post('/cerrarAlquiler', function(Request $request, Response $response){
  $data_body = json_decode($request->getBody(), true);

  $var_filtro = $data_body['filtro'] ? $data_body['filtro'] : '';
  $var_id = $data_body['id'] ? $data_body['id'] : '';
  
  if ($var_filtro == '' || $var_id == '') 
  {
    $fail = array(
      'message' => 'Los parametros estan incompletos, revise la informacion suministrada antes de enviar nuevamente.',
      'reason'=> 'missing parameters'
    );

    $fail = json_encode($fail);
    $response->getBody()->write($fail);
    return $response;
  }
  
  if ($var_filtro != 'entregado')
  {
    $fail = array(
      'message' => 'Ha ocurrido un problema, verifique la informacion suministrada. Si el error persiste comuniquese con el administrador del servicio',
      'email' => 'xtrate@protonmail.com',
      'reason' => 'invalid'
    );

    $fail = json_encode($fail);
    $response->getBody()->write($fail);
    return $response;
  }
  
  try {
    $db = new FirestoreClient();

    $rentalsRef = $db->collection('rentals')->document($var_id)->set(['status'=>$var_filtro],['merge'=>true]);

    $success = array(
      'message' => 'La data ha sido actualizada correctamente.',
      'reason'=> 'successful update'
    );

    $success = json_encode($success);
    $response->getBody()->write($success);
    return $response;
  } catch (\Exception $e){
    $fail = array(
      'message' => 'Ha ocurrido un problema, verifique la informacion suministrada. Si el error persiste comuniquese con el administrador del servicio',
      'email' => 'xtrate@protonmail.com',
      'reason' => 'invalid'
    );

    $fail = json_encode($fail);
    $response->getBody()->write($fail);
    return $response;
  }
  
})->add(new validator());