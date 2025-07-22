<?php
namespace App\Controllers;

use App\Security\Validator;
use App\Utilis\Response;


abstract class BaseController
{

    protected Response $response;
    protected Validator $validator;

    public function __construct()
    {
        $this->response = new Response();
        $this->validator = new Validator;
    }

    protected function render(string $view, array $data = []): void
    {

        $viewPath = __DIR__ . '/Views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            $this->response->error("View not found : $viewPath", 500);
            return;
        }
        extract($data);
        ob_start();
        include $viewPath;
        $content = ob_get_clean();
        include __DIR__ . "/Views/layout.php";
    }

    protected function getPostData(): array
    {
        return $this->validator->sanitize($_POST);
    }

    protected function requireAuth(): void
    {
        if(!isset($_SESSION['user_id'])) {
            $this->response->redirect('/login');
        }
    }
}