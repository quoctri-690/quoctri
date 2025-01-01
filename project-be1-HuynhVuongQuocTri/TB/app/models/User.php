<?php
class User extends Database
{
    public function register($username, $password)
    {
        // 2. Tạo câu query
        $sql = parent::$connection->prepare('INSERT INTO `users`(`username`, `password`) VALUES (?,?)');
        // 3 & 4
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql->bind_param('ss', $username, $password);
        return $sql->execute();
    }

    public function login($username, $password)
    {
        // 2. Tạo câu query
        $sql = parent::$connection->prepare('SELECT * FROM `users` WHERE `username`=?');
        // 3 & 4
        $sql->bind_param('s', $username);
        $user = parent::select($sql)[0];

        if (password_verify($password, $user['password']) === true) {
            return $user;
        }
        return false;
    }
}
