<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require 'vendor/autoload.php' ;



$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$app = AppFactory::create();

# General
require 'api/general.php';

# Usuarios
require 'api/users.php';

# Rentals
require 'api/rentals.php';

$app->run();