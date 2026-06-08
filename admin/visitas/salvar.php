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
$dh = trim($body['data_horario'] ?? '');
$imovel_id = intval($body['imovel_id'] ?? 0);
$cliente_id = intval($body['cliente_id'] ?? 0);
$corretor_id = intval($body['corretor_id'] ?? 0);
$obs = trim($body['observacoes'] ?? '');

if(empty($dh) || $imovel_id <= 0 || $cliente_id <= 0 || $corretor_id <= 0){
    
    echo json_encode(["status"=>"error","message"=>"Preencha todos os campos obrigatórios."]); exit;
}

// Define o fuso horário correto para o Brasil
date_default_timezone_set('America/Sao_Paulo');

// Converte a data enviada e a data de AGORA em segundos para comparar
$data_visita_segundos = strtotime($dh);
$data_atual_segundos = time();

if ($data_visita_segundos < $data_atual_segundos) {
    
    echo json_encode(["status" => "error", "message" => "A visita não pode ser agendada para uma data no passado."]);
    exit;
}

try {

    $stmt = $conn->prepare("INSERT INTO visitas (data_horario, imovel_id, cliente_id, corretor_id, observacoes) VALUES (:dh, :imovel, :cliente, :corretor, :obs)");
    $stmt->bindValue(':dh', $dh);
    $stmt->bindValue(':imovel', $imovel_id);
    $stmt->bindValue(':cliente', $cliente_id);
    $stmt->bindValue(':corretor', $corretor_id);
    $stmt->bindValue(':obs', $obs);
    $stmt->execute();

    echo json_encode(["status"=>"success","message"=>"Visita agendada com sucesso!"]);
} catch (PDOException $e) {

    echo json_encode(["status"=>"error","message"=>"Erro ao salvar: ".$e->getMessage()]);
}

?>