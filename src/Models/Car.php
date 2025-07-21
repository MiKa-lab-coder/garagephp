<?php
namespace App\Models;
use PDO;

// model Cars, represente une voiture en bdd

class Cars extends BaseModel{
    protected string $table = "cars";

    public function all():array{
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");

        //FETCH_ASSOC est deja defini dans notre class Database
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}