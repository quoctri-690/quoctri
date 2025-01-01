<?php
class Database
{
    public static $connection = NULL;
    public function __construct()
    {
        // 1. Tạo connection
        if (self::$connection == NULL) {
            self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            self::$connection->set_charset('utf8mb4');
        }
    }

    public function select($sql)
    {
        // 3. Thực hiện câu query
        $sql->execute();
        // 4. Xử lý kết quả trả về
        return $sql->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
