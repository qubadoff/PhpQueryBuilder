<?php

namespace App;

use PDO;

class User
{
    public function users(): void
    {
        $pdo = new PDO("mysql:host=localhost;dbname=test", "test", "test");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $queryBuilder = DB::table('users', $pdo)
            ->where('gender', '=', 'm')
            ->select('id', 'name', 'age')
            ->orderBy('age')
            ->paginate(10, 2); // 10 items per page, page 2

        $querySQL = $queryBuilder->toSql();
        $result = $queryBuilder->get();

        echo "SQL Query: $querySQL\n";
        var_dump($result);
    }
}