<?php
class Comment extends Database
{
    public function add($content, $productId, $userId)
    {
        // 2. Tạo câu query
        $sql = parent::$connection->prepare('INSERT INTO `comments`(`content`, `product_id`, `user_id`) VALUES (?,?,?)');
        // 3 & 4
        $sql->bind_param('sii', $content, $productId, $userId);
        return $sql->execute();
    }

    public function find($productId)
    {
        // 2. Tạo câu query
        $sql = parent::$connection->prepare('SELECT * FROM `comments` WHERE `product_id`=?');
        // 3 & 4
        $sql->bind_param('i', $productId);
        return parent::select($sql);
    }
}
