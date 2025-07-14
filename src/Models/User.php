<?php

namespace App\Models;

use InvalidArgumentException;
use PDO;

class User extends BaseModel
{

    protected string $table = "users";

    private ?int $user_id = null;
    private ?string $username;
    private ?string $email;
    private ?string $password;
    private ?string $role;

    //getters
    public function getId(): ?int
    {
        return $this->user_id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    //setter avec validations
    public function setUsername(int $username): self
    {
        return $this;
    }

    public function setEmail(string $email): self
    {
        return $this;
    }

    public function setPassword(string $password): self
    {
        return $this;
    }

    public function setRole(string $role): self
    {
        return $this;
    }

    /**
     * save de  l'utilisateur en bdd
     * principe du CRUD Create Read Update Delete
     */
    public function save(): bool
    {
        //si pas d'user identifié on insert
        if ($this->user_id === null) {
            //on cache les infos dans une requete preparer
            $sql = "INSERT INTO{$this->table} (username, email, password, role) VALUES (:username, :email, :password, :role)";
            $stmt = $this->db->prepare($sql);
            $params = [
                ":username" => $this->username,
                ":email" => $this->email,
                ":password" => $this->password,//attention a ce que le mdp soit deja hash
                ":role" => $this->role ?? 'user'//on assigne par defaut le role user
            ];
            //si user identifié on update
        } else {
            //on inclus pas le password pour plus de securité
            $sql = "UPDATE {$this->table}(username, email, role) VALUES (:username, :email, :role WHERE user_id = :user_id)";
            $stmt = $this->db->prepare($sql);
            $params = [
                ":username" => $this->username,
                ":email" => $this->email,
                ":role" => $this->role ?? 'user',
                ":user_id" => $this->user_id // Attention la condition WHERE est importante ici
            ];
        }
        $result = $stmt->execute($params);

        //mise a jour de l'objet apres un insert
        if ($this->user_id === null && $result) {
            //on recup l'id et on l'assigne avec l'objet
            $this->user_id = (int)$this->db->lastInsertId();
        }
        return $result;
    }

    public function findByMail(string $email): ?static{
    //trouve un utilisateur par son mail
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");

        $stmt->execute(["email" => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->hydrate($data): null;
    }

    public function authenticate(string $email,string $password): ?static{
        $user = $this->findByMail($email);
        //on verifie que user existe et que mdp match avec mdp hash stocké
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }

    /**
     * methode qui remplie les props de l'objet en bdd
     */
    private function hydrate(array $data): static{
        $this->user_id = (int)$data["id"];
        $this->username = (string)$data["username"];
        $this->email = (string)$data["email"];
        $this->password = (string)$data["password"];
        $this->role = (string)$data["role"];
        return $this;
    }

}
