<?php
class Category extends Database
{
    public function all()
    {
        // 2. Tạo câu query
        $sql = parent::$connection->prepare('SELECT * from `categories`');
        // 3 & 4
        return parent::select($sql);
    }
}
