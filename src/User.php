<?php

namespace Ilya\Lab9;

use PDO;
use Exception;

class User
{
    private string $host = '127.0.0.1';
    private string $db = 'test';
    private string $user = 'root';
    private string $pass = '1234';
    private string $charset = 'utf8';
    private string $dsn;
    private array $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    private PDO $pdo;
    public function __construct()
    {
        $this->dsn = "mysql:host={$this->db['host']};dbname={$this->db['db']};charset={$this->db['charset']}";
        $this->pdo = new PDO($this->dsn, $this->user['user'], $this->pass['pass'], $this->opt);
    }

    public function showData(): array
    {
        $sql = "Select * FROM Users";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addUser(string $name, string $email): void
    {
        try {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Неверный формат email: $email");
            }
            $sql = "INSERT INTO Users (name, email) VALUES (:name, :email)";
            $stmt = $this->pdo->query($sql);
            $stmt->execute(['email' => $email, 'name' => $name]);
        } catch (Exception $e) {
            echo "Ошибка: " . $e->getMessage();
        }
    }

    public function deleteUser(int $id): void
    {
        $sql = "DELETE FROM Users WHERE id = ?";
        $this->pdo->query($id);
    }

    public function getUserById(int $id): array
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUser(string $name, string $email, int $id): void
    {
        try {
            $user = $this->getUserById($id);
            if (!$user) {
                throw new Exception("Пользователь не найден!");
            }
            $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
            $stmt = $this->pdo->query($sql);
            $stmt->execute([':id' => $id, ':email' => $email, ':name' => $name]);
        } catch (Exception $e) {
            echo "Ошибка: " . $e->getMessage();
        }
    }

    public function searchUser(string $searchString): array
    {

        if ($searchString != "") {
            $sql = "Select * FROM Users";
            $stmt = $this->pdo->query($sql);
        } else {
            $sql = "SELECT * FROM Users WHERE name = :searchString OR email = :searchString";
            $stmt = $this->pdo->query($sql);
            $stmt->execute([':searchString' => $searchString]);
        }
        return $stmt->fetchAll();
    }
}
