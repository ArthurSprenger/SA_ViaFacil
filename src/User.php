<?php
class User {
    private $conn;

    public function __construct($db){
        $this-> conn = $db;
    }

    public function register($username, $email, $password){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO usuarios (nome, email, senha) VALUES (:username , :email, :password)';
        $stmt = $this -> conn->prepare($sql);
        $stmt ->bindParam(':username', $username);
        $stmt ->bindParam(':email', $email);
        $stmt ->bindParam(':password', $hash);
        return $stmt -> execute();
    }

    public function login($email,$password){
        $sql = 'SELECT * FROM usuarios WHERE email = :email';
        $stmt = $this -> conn->prepare($sql);
        $stmt ->bindParam(':email', $email);
        $stmt ->execute();
        $user = $stmt -> fetch(PDO::FETCH_ASSOC);

        if(!$user){ return false; }

        $hash = $user['senha'] ?? '';
        $ok = false;
        if(strpos($hash, '$2y$')===0 || strpos($hash,'$argon2')===0){
            $ok = password_verify($password, $hash);
        } else {
            // Legacy MD5 support during migration
            $ok = (strtolower(md5($password)) === strtolower($hash));
            if($ok){
                // Migrate to password_hash
                $new = password_hash($password, PASSWORD_DEFAULT);
                $up = $this->conn->prepare('UPDATE usuarios SET senha = :h WHERE id = :id');
                $up->bindParam(':h',$new);
                $up->bindParam(':id',$user['id']);
                $up->execute();
                $user['senha'] = $new;
            }
        }
        return $ok ? $user : false;
    }

    public function getUserById($userId){
        $sql = 'SELECT * FROM usuarios WHERE id = :id';
        $stmt = $this -> conn->prepare($sql);
        $stmt ->bindParam(':id', $userId);
        $stmt ->execute();
        return $stmt -> fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfilePic($userId,$profilePic){
        $sql = 'UPDATE usuarios SET foto_perfil = :profile_pic WHERE id = :id';
        $stmt = $this -> conn->prepare($sql);
        $stmt ->bindParam(':profile_pic', $profilePic);
        $stmt ->bindParam(':id', $userId);
        return $stmt -> execute();
    }
}
