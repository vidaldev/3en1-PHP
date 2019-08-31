<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

// GET: info ruta base
$app->get('/', function(Request $request, Response $response) {
  $arg = array (
    "response" => "Para usar este servicio lea la documentacion.",
    "documentacion" => ""
  );
  $data = json_encode($arg);
  $response->getBody()->write($data);
  return $response->withHeader('Content-Type', 'application/json');
});

