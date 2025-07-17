<?php

//pour le dev, on affiche les erreurs sur la page
init_set('display_errors', 1);
error_reporting(E_ALL);

//inclure l'autoloader
require_once __DIR__ . '/vendor/autoload.php';

//import des class
use App\Config\Config;
use App\Utils\Response;

//demarage de la session existante ou reprendre la session en cours
session_start();

//charger les variables d'env
Config::load();

//definir des routes avec biblio FastRoute
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    //on ajoute une route avec addRoute(methode_http, chemin(gargage/login), ender)
    $r->addRoute("GET", "/", "[App\Controllers\HomeController::Class,'index']");
    $r->addRoute("GET", "/login", "[App\Controllers\AuthController::Class,'showLogin']");
    $r->addRoute("POST", "/login", "[App\Controllers\AuthController::Class,'login']");
    $r->addRoute("POST", "/logout", "[App\Controllers\AuthController::Class,'logout']");
    $r->addRoute("GET", "/cars", "[App\Controllers\CarController::Class,'index']");
});

//traitement des requetes
//recuperer la methode http (GET, POST, PUT, PATCH) et l'URI (/login, /cars, /car/1)
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

//Dispatcher FastRoute
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
$response = new Response();

//analyser le resultat du dispatching
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $response->setStatusCode("404 : page not found", 404);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response->setStatusCode("405 method not allowed", 405);
        break;
    case FastRoute\Dispatcher::FOUND:
        [$controllerClass, $method] = $routeInfo[1];
        $vars = $routeInfo[2];
        try {
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $vars);
        } catch (\Exception $e) {
            if (Config::get("APP_DEBUG") === 'true') {
                $response->error("error 500 : " . $e->getMessage() . "dans" . $e->getfile() . ":" . $e->getline(), 500);
            } else {
                (new \App\Utils\Logger())->log("ERROR", "erreur serveur : " . $e->getMessage());
                $response->error("une erreur interne est survenue : ", 500);
            }
        }
        break;
}