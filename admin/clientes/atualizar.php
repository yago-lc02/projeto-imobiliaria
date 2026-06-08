<?php

session_start();
require_once "../../conexao.php";

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['admin_id'])) {

    http_response_code(401);
    echo json_encode(["status"=>"error","message"=>"Não autorizado."]);
    exit;
}

$body = json_decode(file_get_contents("php://input"), true);
$id = intval($body['id'] ?? 0);
$nome = trim($body['nome'] ?? '');
$cpf = trim($body['cpf'] ?? '');
$email = trim($body['email'] ?? '');
$telefone = trim($body['telefone'] ?? '');
$tipo_interesse = trim($body['tipo_interesse'] ?? '');

if($id <= 0 || empty($nome) || empty($cpf) || empty($email) || empty($telefone) || empty($tipo_interesse)){

    echo json_encode(["status"=>"error","message"=>"Dados inválidos."]);
    exit;
}

try {

    $stmt = $conn->prepare("UPDATE clientes SET nome=:n, cpf=:c, email=:e, telefone=:t, tipo_interesse=:ti WHERE id=:id");
    $stmt->bindValue(':n', $nome);
    $stmt->bindValue(':c', $cpf);
    $stmt->bindValue(':e', $email);
    $stmt->bindValue(':t', $telefone);
    $stmt->bindValue(':ti', $tipo_interesse);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    echo json_encode(["status"=>"success","message"=>"Cliente atualizado com sucesso!"]);

} catch (PDOException $e) {

    if ($e->getCode() === '23000') echo json_encode(["status"=>"error","message"=>"Este CPF já pertence a outro."]);
    else echo json_encode(["status"=>"error","message"=>"Erro: ".$e->getMessage()]);

}

?>