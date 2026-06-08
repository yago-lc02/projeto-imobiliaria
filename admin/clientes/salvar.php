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
$nome = trim($body['nome'] ?? '');
$cpf = trim($body['cpf'] ?? '');
$email = trim($body['email'] ?? '');
$telefone = trim($body['telefone'] ?? '');
$tipo_interesse = trim($body['tipo_interesse'] ?? '');

if(empty($nome) || empty($cpf) || empty($email) || empty($telefone) || empty($tipo_interesse)){

    echo json_encode(["status"=>"error","message"=>"Preencha todos os campos."]); exit;
}

try {

    $stmt = $conn->prepare("INSERT INTO clientes (nome, cpf, email, telefone, tipo_interesse) VALUES (:n, :c, :e, :t, :ti)");
    $stmt->bindValue(':n', $nome);
    $stmt->bindValue(':c', $cpf);
    $stmt->bindValue(':e', $email);
    $stmt->bindValue(':t', $telefone);
    $stmt->bindValue(':ti', $tipo_interesse);
    $stmt->execute();

    echo json_encode(["status"=>"success","message"=>"Cliente cadastrado com sucesso!"]);
} catch (PDOException $e) {

    if ($e->getCode() === '23000') echo json_encode(["status"=>"error","message"=>"Este CPF já está cadastrado."]);
    else echo json_encode(["status"=>"error","message"=>"Erro ao salvar: ".$e->getMessage()]);
}

?>