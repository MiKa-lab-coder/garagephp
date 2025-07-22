<?php
namespace App\Controllers;

use App\Security\Validator;
use App\Utilis\Response;

/**
 * Controller de base
 * Toutes les autres class de controller hériterons de celle-ci
 */
abstract class BaseController
{

    protected Response $response;
    protected Validator $validator;

    public function __construct()
    {
        $this->response = new Response();
        $this->validator = new Validator;
    }


    /**
     * Affiche une vue en l'injectant dans le layout principale
     * @param string $view nom du fichier de vue
     * @param array $data le donnees à rendre accessible dans la vue
     */
    protected function render(string $view, array $data = []): void
    {
    //on construit le chemin complet vers le fichier de vue
        $viewPath = __DIR__ . '/Views/' . $view . '.php';
        // on verifie qu le fichier existe bien
        if (!file_exists($viewPath)) {
            $this->response->error("View not found : $viewPath", 500);
            return;
        }
        //extract transforme les clefs d'un tableau en variables
        // ex: $data = ['title'=>'Accueil'] devient $title='Accueil'
        extract($data);
        // on utilise la mise en tampon de sortie pour capturer le html de la vue
        ob_start();
        include $viewPath;
        //on vide le cache la variable $content contient la vue
        $content = ob_get_clean();
        // finalement on inclut le layout pricipal qui peu utiliser la variable $content
        include __DIR__ . "/Views/layout.php";
    }

    //recupere et nettoie les données envoyées via une requete POST
    protected function getPostData(): array
    {
        //
        return $this->validator->sanitize($_POST);
    }

    /**
     * Verifie si l'utilisateur est connecté sinon le redirige vers login
     */
    protected function requireAuth(): void
    {
        if(!isset($_SESSION['user_id'])) {
            $this->response->redirect('/login');
        }
    }
}