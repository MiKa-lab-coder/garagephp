<?php

namespace App\Controllers;

use App\models\User;
use App\Security\Validator;
use App\Security\TokenManager;
use App\Utils\Logger;

/**
 * Cette class gere les actions liées a l'authentification et a l'inscription des utilisateurs
 */
class AuthController extends BaseController
{
    // attributs
    private user $userModel;
    private TokenManager $tokenManager;
    private Logger $logger;

    //constructeur est appellé a chaque création d'un objet AuthController,
    // on en profite pour instancier les modeles dont on a besoin
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->tokenManager = new TokenManager();
        $this->logger = new Logger();
    }

    /**
     * méthode qui affiche la page avec le form de connexion
     */
    public function showLogin(): void
    {
        // redirection vers le login
        $this->render('auth/login', [
            'title' => 'Connexion',
            'csrf_token' => $this->tokenManager->generateCsrfToken()
        ]);
    }

    public function login(): void
    {
        // on s'aasure que la requete est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->response->redirect('/login');
        }
        $data = $this->getPostData();

        // validation du jeton csrf
        if (!$this->tokenManager->validateCsrfToken($data['csrf_token' ?? null])) {
            $this->response->error('CSRF token invalid', 403);
        }

        // le modele User s'occupe de la logique d'authentification
        $user = $this->userModel->authenticate($data['email'], $data['password']);
        // verifications de l'utilisateur
        if ($user) {
            // si authentification réussie, on stock  les infos en session
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_role'] = $user->getRole();
            $_SESSION['user_username'] = $user->getUsername();

            //redirection vers le tableau de bord
            $this->response->redirect('/cars');
        } else {
            $this->render('auth/login', [
                //si l'authentification échoue on reaffiche le formulaire avec un message d'erreur
                'title' => 'connexion',
                'error' => 'Email or password invalid',
                'old' => ['email' => $data['email']],
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
        }
    }
}

