<?php
class Product extends Database
{
    public function all()
    {
        //2. Tạo câu query
        //$sql = parent::$connection->prepare('SELECT * from `products`');
        $sql = parent::$connection->prepare('SELECT `products`.*, GROUP_CONCAT(`categories`.`name`) AS category_name
                                            FROM `products`
                                            LEFT JOIN `category_product`
                                            ON `products`.`id` = `category_product`.`product_id`
                                            LEFT JOIN `categories`
                                            ON `categories`.`id` = `category_product`.`category_id`
                                            WHERE `products`.status = 1
                                            GROUP BY `products`.`id`');
        // 3 & 4
        return parent::select($sql);
    }

    public function allBin()
    {
        // 2. Tạo câu query
        // $sql = parent::$connection->prepare('SELECT * from `products`');
        $sql = parent::$connection->prepare('SELECT *
                                            FROM `products`
                                            WHERE `products`.status = 0');
        // 3 & 4
        return parent::select($sql);
    }

    public function find($id)
    {
        // 2. Tạo câu query
        $sql = parent::$connection->prepare("SELECT `products`.*, GROUP_CONCAT(`category_product`.`category_id`) AS 'category_ids'
                                            FROM `products`
                                            LEFT JOIN `category_product`
                                            ON `products`.`id` = `category_product`.`product_id`
                                            WHERE `id`=?");
        $sql->bind_param('i', $id);
        // 3 & 4
        return parent::select($sql)[0];
    }

    public function findIds($productIds)
    {
        // 2. Tạo câu query
        // Tạo chuỗi kiểu ?,?,?
        $insertPlace = str_repeat("?,", count($productIds) - 1) . "?";
        // Tạo chuỗi iiiiiiii
        $insertType = str_repeat('i', count($productIds) * 2);


        $sql = parent::$connection->prepare("SELECT * FROM `products` WHERE `id` IN ($insertPlace) ORDER BY FIELD(id, $insertPlace) DESC");
        $sql->bind_param($insertType, ...$productIds, ...$productIds);
        // 3 & 4
        return parent::select($sql);
    }

    public function findByCategory($id, $limit = '')
    {
        $limit = ($limit != '') ? "LIMIT $limit" : '';
        // 2. Tạo câu query
        $sql = parent::$connection->prepare("SELECT *
                                            FROM `category_product`
                                            INNER JOIN `products`
                                            ON `category_product`.`product_id` = `products`.`id`
                                            WHERE `category_id`=?
                                            $limit");
        $sql->bind_param('i', $id);
        // 3 & 4
        return parent::select($sql);
    }

    public function findByKeyWord($keyword, $page, $perPage)
    {
        $start = ($page - 1) * $perPage;
        // 2. Tạo câu query
        $sql = parent::$connection->prepare("SELECT * FROM `products` WHERE `name` LIKE ? LIMIT ?,?");
        $keyword = "%{$keyword}%";
        $sql->bind_param('sii', $keyword, $start, $perPage);
        // 3 & 4
        return parent::select($sql);
    }

    public function countByKeyWord($keyword)
    {
        // 2. Tạo câu query
        $sql = parent::$connection->prepare("SELECT COUNT(`id`) AS 'total' FROM `products` WHERE `name` LIKE ?");
        $keyword = "%{$keyword}%";
        $sql->bind_param('s', $keyword);
        // 3 & 4
        return parent::select($sql)[0]['total'];
    }

    public function add($name, $preview, $description, $description2, $image, $categoryIds)
    {
        // 2. Tạo câu query
        $sql = parent::$connection->prepare("INSERT INTO `products`(`name`, `preview`, `description`,`description2`, `image`) VALUES (?, ?, ?, ?,  ?)");
        $sql->bind_param('sssss', $name, $preview, $description, $description2, $image);
        // 3 & 4
        $sql->execute();

        // Thêm categories vào products
        // 2. Tạo câu query
        $productId = parent::$connection->insert_id;

        // Tạo chuỗi kiểu (?, id), (?, id), (?, id)
        $insertPlace = str_repeat("(?, $productId),", count($categoryIds) - 1) . "(?, $productId)";
        // Tạo chuỗi iiiiiiii
        $insertType = str_repeat('i', count($categoryIds));

        $sql = parent::$connection->prepare("INSERT INTO `category_product`(`category_id`, `product_id`) VALUES $insertPlace");

        $sql->bind_param($insertType, ...$categoryIds);
        return $sql->execute();
    }

    public function update($name, $preview, $description, $description2, $image, $productId, $categoryIds)
    {
        // 2. Tạo câu query
        $sql = parent::$connection->prepare("UPDATE `products` SET `name`=?,`preview`=?,`description`=?,`description2`=?,`image`=? WHERE `id`=?");
        $sql->bind_param('sssssi', $name, $preview, $description, $description2, $image, $productId);
        // 3 & 4
        $sql->execute();

        // Xóa các categories cũ
        $sql = parent::$connection->prepare("DELETE FROM `category_product` WHERE `product_id`=?");
        $sql->bind_param('i', $productId);
        // 3 & 4
        $sql->execute();

        // Thêm categories vào products
        // 2. Tạo câu query
        // Tạo chuỗi kiểu (?, id), (?, id), (?, id)
        $insertPlace = str_repeat("(?, $productId),", count($categoryIds) - 1) . "(?, $productId)";
        // Tạo chuỗi iiiiiiii
        $insertType = str_repeat('i', count($categoryIds));

        $sql = parent::$connection->prepare("INSERT INTO `category_product`(`category_id`, `product_id`) VALUES $insertPlace");

        $sql->bind_param($insertType, ...$categoryIds);
        return $sql->execute();
    }

    public function delete($productIds)
    {
        // 2. Tạo câu query
        // Tạo chuỗi kiểu ?,?,?
        $insertPlace = str_repeat("?,", count($productIds) - 1) . "?";
        // Tạo chuỗi iiiiiiii
        $insertType = str_repeat('i', count($productIds));


        $sql = parent::$connection->prepare("DELETE FROM `products` WHERE `id` IN ($insertPlace)");
        $sql->bind_param($insertType, ...$productIds);
        // 3 & 4
        return $sql->execute();
    }

    public function deleteBin($productId)
    {
        // 2. Tạo câu query
        $sql = parent::$connection->prepare("UPDATE `products` SET `status`=0 WHERE `id`=?");
        $sql->bind_param('i', $productId);
        // 3 & 4
        return $sql->execute();
    }
    public function restore($productIds)
    {
        // 2. Tạo câu query
        // Tạo chuỗi kiểu ?,?,?
        $insertPlace = str_repeat("?,", count($productIds) - 1) . "?";
        // Tạo chuỗi iiiiiiii
        $insertType = str_repeat('i', count($productIds));

        $sql = parent::$connection->prepare("UPDATE `products` SET `status`=1 WHERE `id` IN ($insertPlace)");
        $sql->bind_param($insertType, ...$productIds);
        // 3 & 4
        return $sql->execute();
    }

    public function like($productId)
    {
        // 2. Tạo câu query
        $sql = parent::$connection->prepare("UPDATE `products` SET `likes` = `likes` + 1 WHERE `id`=?");
        $sql->bind_param('i', $productId);
        // 3 & 4
        return $sql->execute();
    }
}
