<?php
namespace App\Controllers;

use App\Models\Car;

/**
 * gere la logique de la page d'accueil
 */
class HomeController extends BaseController
{
    public function index(): void
    {
        $carModel = new Car();

        $this->render('home/index', [
            'title' => 'Accueil - Garage php App',
            // appel la methode all dans Car
            'cars'=> $carModel->all()
        ]);
    }
}