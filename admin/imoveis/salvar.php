<?php

session_start();
require_once "../../conexao.php";

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['admin_id'])) {

    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Não autorizado."]);
    exit;
}

$tipo = trim($_POST['tipo'] ?? '');
$valor = floatval($_POST['valor'] ?? 0);
$metragem = floatval($_POST['metragem'] ?? 0);
$endereco = trim($_POST['endereco'] ?? '');

if (empty($tipo) || $valor <= 0 || $metragem <= 0 || empty($endereco)) {

    echo json_encode(["status" => "error", "message" => "Preencha os campos corretamente."]);
    exit;
}

// === LÓGICA DE UPLOAD MÚLTIPLO ===
$caminhos_fotos = []; // Array que vai guardar o endereço de cada foto salva
$pasta_destino = "../../uploads/imagens/";
$tamanho_maximo = 2 * 1024 * 1024; // Trava de 2MB

// Verifica se enviaram fotos e se o PHP as reconheceu como Array
if (isset($_FILES['fotos']) && is_array($_FILES['fotos']['name'])) {

    $total_fotos = count($_FILES['fotos']['name']);
    
    // Faz o loop por cada foto enviada
    for ($i = 0; $i < $total_fotos; $i++) {

        if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
            
            // Trava de Peso (Testa o tamanho do arquivo atual do loop)
            if ($_FILES['fotos']['size'][$i] > $tamanho_maximo) {

                echo json_encode(["status" => "error", "message" => "A foto deve ter no máximo 2MB."]);
                exit;
            }

            $nome_arquivo = $_FILES['fotos']['name'][$i];
            $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
            $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
            
            // Trava de Extensão/PDF (Barra o processo e avisa o erro)
            if (!in_array($extensao, $extensoes_permitidas)) {

                echo json_encode(["status" => "error", "message" => "Apenas imagens JPG ou PNG são permitidas."]);
                exit;
            }

            // Cria nome único para a foto
            $novo_nome = uniqid('imovel_') . "_" . $i . "." . $extensao;
            $caminho_absoluto = $pasta_destino . $novo_nome;
            
            // Se a foto foi movida com sucesso, guarda o caminho no array
            if (move_uploaded_file($_FILES['fotos']['tmp_name'][$i], $caminho_absoluto)) {
                $caminhos_fotos[] = "uploads/imagens/" . $novo_nome;
            }
        }
    }
}

// Transforma o Array PHP numa String JSON (Ex: ["uploads/a.jpg", "uploads/b.jpg"])
$fotos_json = json_encode($caminhos_fotos, JSON_UNESCAPED_UNICODE);

try {

    $sql = "INSERT INTO imoveis (tipo, endereco, metragem, valor, fotos, status) VALUES (:tipo, :endereco, :metragem, :valor, :fotos, 'disponivel')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":tipo", $tipo);
    $stmt->bindValue(":endereco", $endereco);
    $stmt->bindValue(":metragem", $metragem);
    $stmt->bindValue(":valor", $valor);
    $stmt->bindValue(":fotos", $fotos_json);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Imóvel cadastrado com sucesso!"]);

} catch (PDOException $e) {

    echo json_encode(["status" => "error", "message" => "Erro ao salvar no banco: " . $e->getMessage()]);
}

?>