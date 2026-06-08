<?php

session_start();
require_once "../../conexao.php";

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['admin_id'])) {

    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Não autorizado."]);
    exit;
}

// Lê o JSON recebido
$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id'] ?? 0);

if ($id <= 0) {

    echo json_encode(["status" => "error", "message" => "ID inválido."]);
    exit;
}

try {

    // Primeiro, busca o caminho da foto para poder apagá-la da pasta
    $stmt = $conn->prepare("SELECT foto FROM corretores WHERE id = :id");
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    
    $corretor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($corretor) {

        // Apaga do banco de dados
        $stmtDel = $conn->prepare("DELETE FROM corretores WHERE id = :id");
        $stmtDel->bindValue(":id", $id);
        $stmtDel->execute();

        // Se tinha foto, apaga o arquivo físico da pasta uploads
        if (!empty($corretor['foto']) && file_exists("../../" . $corretor['foto'])) {

            unlink("../../" . $corretor['foto']);
        }

        echo json_encode(["status" => "success", "message" => "Corretor excluído com sucesso!"]);
    } else {

        echo json_encode(["status" => "error", "message" => "Corretor não encontrado."]);
    }
} catch (PDOException $e) {

    echo json_encode(["status" => "error", "message" => "Erro ao excluir: " . $e->getMessage()]);
}

?>