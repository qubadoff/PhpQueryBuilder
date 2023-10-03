<?php

namespace App;

use PDO;
class QueryBuilder
{
    private PDO $pdo;
    private string $table;
    private string $select = '*';
    private string $where = '';
    private array $params = [];
    private string $orderBy = '';
    private int $perPage = 10;
    private int $currentPage = 1;

    public function __construct(PDO $pdo, string $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function select(string ...$columns): static
    {
        $this->select = implode(', ', $columns);
        return $this;
    }

    public function where(string $column, string $operator, mixed $value): static
    {
        $param = ":$column";
        $this->params[$param] = $value;
        $this->where = $this->where ? "$this->where AND $column $operator $param" : " WHERE $column $operator $param";
        return $this;
    }

    public function orWhere(string $column, string $operator, mixed $value): static
    {
        $param = ":$column";
        $this->params[$param] = $value;
        $this->where = $this->where ? "$this->where OR $column $operator $param" : " WHERE $column $operator $param";
        return $this;
    }

    public function orderBy(string ...$columns): static
    {
        $this->orderBy = " ORDER BY " . implode(', ', $columns);
        return $this;
    }

    public function paginate(int $perPage, int $page): static
    {
        $this->perPage = $perPage;
        $this->currentPage = $page;
        return $this;
    }

    public function toSql(): string
    {
        $offset = ($this->currentPage - 1) * $this->perPage;
        $sql = "SELECT {$this->select} FROM {$this->table}{$this->where}{$this->orderBy} LIMIT {$this->perPage} OFFSET $offset";
        return $sql;
    }

    public function get(): array
    {
        $sql = $this->toSql();
        return $this->executeQuery($sql, $this->params);
    }

    private function executeQuery(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
