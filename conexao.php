<?php

$host = "sql200.infinityfree.com";
$banco = "if0_42189875_imobiliaria";
$usuario = "if0_42189875";
$senha = "Bancomysql";

try {

    $conn = new PDO("mysql:host=$host;dbname=$banco", $usuario, $senha);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {

    // Se for requisição AJAX devolve JSON. Se não for, exibe erro simples.
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || $_SERVER['REQUEST_METHOD'] === 'POST') {

        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Erro de conexão com o banco."], JSON_UNESCAPED_UNICODE);
        exit;
    } else {

        die("Erro critico: Não foi possivel conectar ao banco de dados.");
    }
}
