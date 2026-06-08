<?php

session_start();
require_once "../conexao.php"; // Puxa a conexão com o banco

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM imoveis WHERE id = :id");
$stmt->bindValue(":id", $id);
$stmt->execute();

$imovel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$imovel) {

    echo "<script>alert('Imóvel não encontrado!'); window.location.href='index.php';</script>";
    exit;
}

// Transforma o JSON de fotos num Array do PHP
$fotos_array = json_decode($imovel['fotos'], true);

if (!is_array($fotos_array)) {
    $fotos_array = [];
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detalhes do Imóvel - Yago Imóveis</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        /* Suporte ao Dark Mode acompanhando o index */
        body.dark-mode { background-color: #121212 !important; color: #ffffff !important; }
        body.dark-mode .card { background-color: #1e1e1e; color: white; border-color: #333; }
        body.dark-mode .text-muted { color: #adb5bd !important; }
        
        .carousel-item img {
            height: 500px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>

</head>

<body class="bg-light">

<!-- Navbar Simples -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-buildings"></i> Yago Imóveis</a>
        <a href="index.php" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left"></i> Voltar aos imóveis</a>
    </div>
</nav>

<div class="container my-5">
    <div class="row">
        <!-- Coluna da Esquerda: Carrossel de Fotos -->
        <div class="col-md-8 mb-4">
            <div id="carrosselImovel" class="carousel slide shadow-sm" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php if (count($fotos_array) > 0): ?>
                        <?php foreach ($fotos_array as $index => $foto): ?>
                            <!-- A primeira foto ganha a classe 'active' para o carrossel funcionar -->
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="../<?php echo htmlspecialchars($foto); ?>" class="d-block w-100" alt="Foto Imóvel">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Caso o imóvel não tenha fotos (fallback) -->
                        <div class="carousel-item active">
                            <img src="https://sindicfcmg.org.br/wp-content/uploads/2024/08/sem-imagem11-1-768x576.jpg" class="d-block w-100" alt="Foto Genérica">
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Controles do Carrossel (Só mostra se tiver mais de 1 foto) -->
                <?php if (count($fotos_array) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carrosselImovel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carrosselImovel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Coluna da Direita: Informações do Imóvel -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary text-uppercase"><?php echo htmlspecialchars($imovel['tipo']); ?></span>
                        <?php 
                            $corStatus = 'bg-success';
                            if($imovel['status'] == 'vendido') $corStatus = 'bg-danger';
                            if($imovel['status'] == 'alugado') $corStatus = 'bg-warning text-dark';
                        ?>
                        <span class="badge <?php echo $corStatus; ?> text-uppercase"><?php echo htmlspecialchars($imovel['status']); ?></span>
                    </div>

                    <h2 class="fw-bold text-success mt-3 mb-4">R$ <?php echo number_format($imovel['valor'], 2, ',', '.'); ?></h2>
                    
                    <p class="text-muted mb-2">
                        <i class="bi bi-geo-alt-fill text-danger"></i> 
                        <strong>Endereço:</strong><br>
                        <?php echo htmlspecialchars($imovel['endereco']); ?>
                    </p>
                    
                    <p class="text-muted mb-4">
                        <i class="bi bi-arrows-fullscreen text-primary"></i> 
                        <strong>Área:</strong> <?php echo htmlspecialchars($imovel['metragem']); ?> m²
                    </p>

                    <div class="mt-auto">
                        <hr>
                        <p class="small text-muted text-center">Ficou interessado neste imóvel?</p>
                        <!-- Volta para a página principal ancorado no formulário de contato -->
                        <a href="index.php#contactform" class="btn btn-success w-100 py-2">
                            <i class="bi bi-whatsapp"></i> Falar com um Corretor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para puxar a preferência do Dark Mode do LocalStorage (já que está em outra página) -->
<script>

    if (localStorage.getItem('theme') === 'dark') {

        document.body.classList.add('dark-mode');
    }

</script>

</body>

</html>