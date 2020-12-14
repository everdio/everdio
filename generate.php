<?php
foreach ($this->pdo["databases"] as $namespace => $database) {
    $this->pdo = ["database" => $database];
    $this->model = ["namespace" => $namespace];
    $this->dispatch("../table/generate");
    ob_flush();       
}
