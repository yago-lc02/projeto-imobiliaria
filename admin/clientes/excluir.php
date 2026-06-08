<?php

session_start();
require_once "../../conexao.php";

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['admin_id'])) {

    http_response_code(401);
    echo json_encode(["status"=>"error","message"=>"Não autorizado."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id'] ?? 0);

if ($id <= 0) {

    echo json_encode(["status"=>"error","message"=>"ID inválido."]);
    exit;
}

try {

    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = :id");
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    
    echo json_encode(["status"=>"success","message"=>"Cliente excluído!"]);
} catch (PDOException $e) {

    echo json_encode(["status"=>"error","message"=>"Erro ao excluir."]);
}

?>