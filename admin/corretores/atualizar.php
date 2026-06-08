<?php

session_start();
require_once "../../conexao.php";

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['admin_id'])) {

    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Não autorizado."]);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$nome = trim($_POST['nome'] ?? '');
$creci = trim($_POST['creci'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$especialidade = trim($_POST['especialidade'] ?? '');

if ($id <= 0 || empty($nome) || empty($creci) || empty($telefone) || empty($especialidade)) {

    echo json_encode(["status" => "error", "message" => "Preencha todos os campos obrigatórios."]);
    exit;
}

try {

    // Busca os dados atuais do corretor para saber qual era a foto antiga
    $stmt = $conn->prepare("SELECT foto FROM corretores WHERE id = :id");
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    
    $corretor_atual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$corretor_atual) {

        echo json_encode(["status" => "error", "message" => "Corretor não encontrado."]);
        exit;
    }

    $caminho_foto = $corretor_atual['foto']; // Por padrão, mantém a foto que já estava no banco

    // Verifica se o administrador enviou uma foto NOVA
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

        // Trava do Tamanho (2MB)
        $tamanho_maximo = 2 * 1024 * 1024;
        if ($_FILES['foto']['size'] > $tamanho_maximo) {

            echo json_encode(["status" => "error", "message" => "A nova imagem excede o limite de 2MB."]);
            exit;
        }

        $pasta_destino = "../../uploads/imagens/";
        $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        
        // Adicionando o webp por padrão
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        // Trava da Extensão (Anti-PDF)
        if (!in_array($extensao, $extensoes_permitidas)) {
            
            echo json_encode(["status" => "error", "message" => "Apenas imagens JPG, PNG ou WEBP."]);
            exit;
        }

        // Gera novo nome e move a imagem
        $novo_nome = uniqid('corretor_') . "." . $extensao;
        $caminho_absoluto = $pasta_destino . $novo_nome;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_absoluto)) {

            // Se guardou a foto nova com sucesso, a antiga do HD é apagada (se existisse)
            if (!empty($corretor_atual['foto']) && file_exists("../../" . $corretor_atual['foto'])) {

                unlink("../../" . $corretor_atual['foto']);
            }

            // Atualiza a variável que vai para o banco com o novo caminho
            $caminho_foto = "uploads/imagens/" . $novo_nome;
        } else {

            echo json_encode(["status" => "error", "message" => "Erro ao salvar a nova imagem."]);
            exit;
        }
    }

    // Faz o UPDATE no banco de dados
    $sql = "UPDATE corretores SET nome = :nome, creci = :creci, telefone = :telefone, especialidade = :especialidade, foto = :foto WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":nome", $nome);
    $stmt->bindValue(":creci", $creci);
    $stmt->bindValue(":telefone", $telefone);
    $stmt->bindValue(":especialidade", $especialidade);
    $stmt->bindValue(":foto", $caminho_foto, $caminho_foto ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(":id", $id);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Corretor atualizado com sucesso!"]);

} catch (PDOException $e) {

    if ($e->getCode() === '23000') {

        echo json_encode(["status" => "error", "message" => "Este CRECI já pertence a outro corretor."]);
    } else {

        echo json_encode(["status" => "error", "message" => "Erro: " . $e->getMessage()]);
    }
}

?>