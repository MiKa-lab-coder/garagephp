<?php
namespace App\Controllers;
Use App\Models\Car;

class CarController extends BaseController
{
    public function index():void
    {
        $this->requireAuth();
        $this->render('car/index', [
            'title' => 'tableau de bord de cars',
            'cars' => (new Car())->all()
        ]);

    }

}