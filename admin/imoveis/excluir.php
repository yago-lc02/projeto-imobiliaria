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

    $stmt = $conn->prepare("SELECT fotos FROM imoveis WHERE id = :id");
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    
    $imovel = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($imovel) {

        // Exclui do banco
        $stmtDel = $conn->prepare("DELETE FROM imoveis WHERE id = :id");
        $stmtDel->bindValue(":id", $id);
        $stmtDel->execute();

        // Faz o loop para excluir TODAS as fotos deste imóvel do disco do servidor
        $fotos_array = json_decode($imovel['fotos'], true);

        if (is_array($fotos_array)) {

            foreach ($fotos_array as $foto) {

                if (!empty($foto) && file_exists("../../" . $foto)) {

                    unlink("../../" . $foto);
                }
            }
        }

        echo json_encode(["status"=>"success","message"=>"Imóvel e fotos excluídos com sucesso!"]);
    } else {

        echo json_encode(["status"=>"error","message"=>"Imóvel não encontrado."]);
    }
} catch (PDOException $e) {

    echo json_encode(["status"=>"error","message"=>"Erro ao excluir: " . $e->getMessage()]);
}

?>