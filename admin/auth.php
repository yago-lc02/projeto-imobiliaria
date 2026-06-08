<?php

header("Content-Type: application/json; charset=UTF-8");
session_start();

require_once "../conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Lê o JSON enviado pelo JavaScript
    $body = json_decode(file_get_contents("php://input"), true);
    
    $email = trim($body['email'] ?? '');
    $senha = trim($body['senha'] ?? '');

    if (empty($email) || empty($senha)) {

        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Preencha todos os campos."]);
        exit;
    }

    // Busca o administrador pelo e-mail
    $stmt = $conn->prepare("SELECT * FROM administradores WHERE email = :email");
    $stmt->bindValue(":email", $email);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // password_verify compara a senha digitada com o Hash gigante que está no banco
    if ($admin && password_verify($senha, $admin['senha'])) {
        
        // LOGIN APROVADO: Cria a sessão do usuário!
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nome'] = $admin['nome'];

        echo json_encode(["status" => "success", "message" => "Login aprovado."]);
    } else {

        // LOGIN NEGADO
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "E-mail ou senha incorretos."]);
    }
} else {

    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Método não permitido."]);
}

?>