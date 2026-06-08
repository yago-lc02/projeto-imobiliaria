<?php

session_start();
require_once "../../conexao.php";

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['admin_id'])) {

    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Não autorizado."]);
    exit;
}

// Por usar FormData, os dados vêm no $_POST normal, e a imagem no $_FILES
$nome = trim($_POST['nome'] ?? '');
$creci = trim($_POST['creci'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$especialidade = trim($_POST['especialidade'] ?? '');

if (empty($nome) || empty($creci) || empty($telefone) || empty($especialidade)) {

    echo json_encode(["status" => "error", "message" => "Preencha todos os campos obrigatórios."]);
    exit;
}

// Lógica de Upload de Arquivo
$caminho_foto = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

    $pasta_destino = "../../uploads/imagens/";
    
    // Pega as informações do arquivo
    $nome_arquivo = $_FILES['foto']['name'];
    $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
    
    // Validações
    $extensoes_permitidas = ['jpg', 'jpeg', 'png'];
    $tamanho_maximo = 2 * 1024 * 1024; // 2MB

    if (!in_array($extensao, $extensoes_permitidas)) {

        echo json_encode(["status" => "error", "message" => "Apenas imagens JPG ou PNG são permitidas."]);
        exit;
    }
    
    if ($_FILES['foto']['size'] > $tamanho_maximo) {

        echo json_encode(["status" => "error", "message" => "A foto deve ter no máximo 2MB."]);
        exit;
    }

    // Cria um nome único para não sobrescrever fotos com o mesmo nome
    $novo_nome = uniqid('corretor_') . "." . $extensao;
    $caminho_absoluto = $pasta_destino . $novo_nome;
    
    // Caminho relativo que será salvo no banco de dados
    $caminho_foto = "uploads/imagens/" . $novo_nome;

    // Move o arquivo da pasta temporária para a nossa pasta uploads
    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_absoluto)) {

        echo json_encode(["status" => "error", "message" => "Erro ao salvar a imagem no servidor."]);
        exit;
    }
}

try {

    $sql = "INSERT INTO corretores (nome, creci, telefone, especialidade, foto) VALUES (:nome, :creci, :telefone, :especialidade, :foto)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":nome", $nome);
    $stmt->bindValue(":creci", $creci);
    $stmt->bindValue(":telefone", $telefone);
    $stmt->bindValue(":especialidade", $especialidade);
    $stmt->bindValue(":foto", $caminho_foto, $caminho_foto ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Corretor cadastrado com sucesso!"]);

} catch (PDOException $e) {

    if ($e->getCode() === '23000') {

        echo json_encode(["status" => "error", "message" => "Este CRECI já está cadastrado."]);
    } else {

        echo json_encode(["status" => "error", "message" => "Erro ao salvar: " . $e->getMessage()]);
    }
}

?>