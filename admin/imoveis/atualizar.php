<?php

session_start();
require_once "../../conexao.php";

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['admin_id'])) {

    http_response_code(401);
    echo json_encode(["status"=>"error","message"=>"Não autorizado."]);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? '');
$tipo = trim($_POST['tipo'] ?? '');
$valor = floatval($_POST['valor'] ?? 0);
$metragem = floatval($_POST['metragem'] ?? 0);
$endereco = trim($_POST['endereco'] ?? '');

if ($id <= 0 || empty($tipo) || $valor <= 0 || $metragem <= 0 || empty($endereco) || empty($status)) {

    echo json_encode(["status"=>"error","message"=>"Preencha todos os campos corretamente."]); 
    exit;
}

try {

    // Busca os dados atuais para saber quais eram as fotos
    $stmt = $conn->prepare("SELECT fotos FROM imoveis WHERE id = :id");
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    
    $imovel_atual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$imovel_atual) {

        echo json_encode(["status"=>"error","message"=>"Imóvel não encontrado."]);
        exit;
    }

    $fotos_json = $imovel_atual['fotos']; // Mantém as fotos antigas por padrão

    // Verifica se foram enviadas NOVAS fotos
    if (isset($_FILES['fotos']) && is_array($_FILES['fotos']['name']) && $_FILES['fotos']['error'][0] === UPLOAD_ERR_OK) {

        $caminhos_fotos_novas = [];
        $pasta_destino = "../../uploads/imagens/";
        $total_fotos = count($_FILES['fotos']['name']);
        $tamanho_maximo = 2 * 1024 * 1024; // Trava do Tamanho (2MB)
        
        for ($i = 0; $i < $total_fotos; $i++) {

            if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {

                // Verifica o peso da imagem atual do loop
                if ($_FILES['fotos']['size'][$i] > $tamanho_maximo) {

                    echo json_encode(["status" => "error", "message" => "Uma das novas imagem excede o limite de 2MB."]);
                    exit;
                }

                $extensao = strtolower(pathinfo($_FILES['fotos']['name'][$i], PATHINFO_EXTENSION));
                $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
                
                // Trava da Extensão (Anti-PDF)
                if (!in_array($extensao, $extensoes_permitidas)) {

                    echo json_encode(["status" => "error", "message" => "Apenas imagens JPG, PNG ou WEBP."]);
                    exit;
                }

                $novo_nome = uniqid('imovel_') . "_" . $i . "." . $extensao;
                if (move_uploaded_file($_FILES['fotos']['tmp_name'][$i], $pasta_destino . $novo_nome)) {

                    $caminhos_fotos_novas[] = "uploads/imagens/" . $novo_nome;
                }
            }
        }

        // Se conseguiu salvar as novas fotos, apaga as antigas do servidor
        if (count($caminhos_fotos_novas) > 0) {

            $fotos_antigas_array = json_decode($imovel_atual['fotos'], true);

            if (is_array($fotos_antigas_array)) {
                foreach ($fotos_antigas_array as $foto_antiga) {

                    if (file_exists("../../" . $foto_antiga)) {

                        unlink("../../" . $foto_antiga); // Apaga do HD
                    }
                }
            }

            // Atualiza a variável com o novo array em formato JSON
            $fotos_json = json_encode($caminhos_fotos_novas, JSON_UNESCAPED_UNICODE);
        }
    }

    // Executa o UPDATE (Agora padronizado com bindValue explícito para o checklist)
    $sql = "UPDATE imoveis SET tipo=:tipo, endereco=:endereco, metragem=:metragem, valor=:valor, status=:status, fotos=:fotos WHERE id=:id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':tipo', $tipo);
    $stmt->bindValue(':endereco', $endereco);
    $stmt->bindValue(':metragem', $metragem);
    $stmt->bindValue(':valor', $valor);
    $stmt->bindValue(':status', $status);
    $stmt->bindValue(':fotos', $fotos_json);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    echo json_encode(["status"=>"success","message"=>"Imóvel atualizado com sucesso!"]);

} catch (PDOException $e) {

    echo json_encode(["status"=>"error","message"=>"Erro ao atualizar: " . $e->getMessage()]);
}

?>