<?php

namespace App;

use PDO;

class DB
{
    public static function table(string $table, PDO $pdo): QueryBuilder
    {
        return new QueryBuilder($pdo, $table);
    }
}