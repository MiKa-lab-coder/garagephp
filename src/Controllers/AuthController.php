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
                //si l'authentification échoue,on affiche le formulaire avec un message d'erreur
                'title' => 'connexion',
                'error' => 'Email or password invalid',
                'old' => ['email' => $data['email']],
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
        }
    }

    /**
     * affichage du formulaire d'inscription
     */
    public function showRegister(): void
    {
        $this->render('auth/register', [
            'title' => 'inscription',
            'csrf_token' => $this->tokenManager->generateCsrfToken()
        ]);
    }

    /**
     * Traitement des données de soumission formulaire inscription
     */
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->response->redirect('/register');

            $data = $this->getPostData();
            if (!$this->tokenManager->validateCsrfToken($data['csrf_token' ?? null])) {
                $this->response->error('CSRF token invalid', 403);
            }

            //validation des données du form
            $errors = $this->validator->validate($data, [
                'username' => 'required|min:3|max:50',
                'email' => 'required|email',
                'password' => 'required|min:9',
                'confirm_password' => 'required|min:9|matches[password]'
            ]);

            if (!empty($errors)) {
                $this->render('auth/register', [
                    'title' => 'inscription',
                    'error' => $errors,
                    'old' => $data,
                    'csrf_token' => $this->tokenManager->generateCsrfToken()
                ]);
                return;
            }
            //verification de l'email
            if ($this->userModel->findByMail($data['email'])) {
                $this->render('auth/register', [
                    'title' => 'inscription',
                    'error' => ['mail' => ['Email is already in use']],
                    'old' => $data,
                    'csrf_token' => $this->tokenManager->generateCsrfToken()
                ]);
                return;
            }
            //si tout est correct on creer un new utilisateur
            try {
                //on instancie un nouvel utilisateur
                $newUser = new User();
                //on utilise les setter pour assigner les valeurs(inclut la validation et hash du mdp)
                $newUser->setUsername($data['username'])
                    ->setEmail($data['email'])
                    ->setPassword($data['password'])
                    ->setRole($data['user']);// role par default

                //on save en bdd
                if ($newUser->save()) {
                    //si la creation reussi on connecte automatiquement l'utilisateur
                    $_SESSION['user_id'] = $newUser->getId();
                    $_SESSION['user_role'] = $newUser->getRole();
                    $_SESSION['user_username'] = $newUser->getUsername();
                    $this->response->redirect('/cars');
                } else {
                    //si la save echoue
                    throw new \Exception('la creation du compte à echouée');
                }
            } catch (\Exception $e) {
                $this->render('/register', [
                    'title' => 'inscription',
                    'error' => $e->getMessage(),
                    'old' => $data,
                    'csrf_token' => $this->tokenManager->generateCsrfToken()
                ]);
            }
        }
    }

    /**
     * methode de deconnection avec destuction de a session
     */
    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->response->redirect('/');
        }

        //détruit les donnéés de la session
        session_destroy();
        //redirige vers le login
        $this->response->redirect('/login');
    }
}

