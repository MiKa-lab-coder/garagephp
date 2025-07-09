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

}