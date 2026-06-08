<?php

session_start();
require_once "../../conexao.php";

if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login.php");
    exit;
}

$stmt = $conn->query("SELECT * FROM imoveis ORDER BY id DESC");
$imoveis = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title>Imóveis - Painel Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container-fluid d-flex align-items-center">
    <a href="../index.php" class="btn btn-outline-light btn-sm me-3" ><i class="bi bi-arrow-left"></i> Voltar ao Dashboard</a>
    <span class="text-white">Gerenciamento de Imóveis</span>
  </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-house-door"></i> Imóveis Cadastrados</h2>
        <a href="cadastrar.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Novo Imóvel</a>
    </div>

    <div id="alerta-sistema" class="alert d-none" role="alert"></div>

    <div class="card shadow-sm">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Foto Principal</th>
                        <th>Tipo</th>
                        <th>Endereço</th>
                        <th>Metragem</th>
                        <th>Valor (R$)</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($imoveis) > 0): ?>
                        <?php foreach ($imoveis as $imovel): 
                            // O pulo do gato: Transforma a string JSON do banco num Array PHP
                            $fotos = json_decode($imovel['fotos'], true);
                            $foto_principal = (is_array($fotos) && count($fotos) > 0) ? "../../" . $fotos[0] : "";
                        ?>
                            <tr id="linha-<?php echo $imovel['id']; ?>" class="align-middle">
                                <td>
                                    <?php if (!empty($foto_principal)): ?>
                                        <img src="<?php echo $foto_principal; ?>" class="rounded" style="width: 60px; height: 45px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded d-flex justify-content-center align-items-center text-white" style="width: 60px; height: 45px;"><i class="bi bi-image"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-primary text-uppercase"><?php echo htmlspecialchars($imovel['tipo']); ?></span></td>
                                <td title="<?php echo htmlspecialchars($imovel['endereco']); ?>">
                                    <?php echo htmlspecialchars(substr($imovel['endereco'], 0, 30)) . '...'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($imovel['metragem']); ?> m²</td>
                                <td class="fw-bold">R$ <?php echo number_format($imovel['valor'], 2, ',', '.'); ?></td>
                                <td>
                                    <?php 
                                        $corStatus = 'bg-success';
                                        if($imovel['status'] == 'vendido') $corStatus = 'bg-danger';
                                        if($imovel['status'] == 'alugado') $corStatus = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?php echo $corStatus; ?> text-uppercase"><?php echo htmlspecialchars($imovel['status']); ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="editar.php?id=<?php echo $imovel['id']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deletarImovel(<?php echo $imovel['id']; ?>)"><i class="bi bi-trash3"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Nenhum imóvel cadastrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>

    function deletarImovel(id) {

        if (!confirm(`Atenção: Todas as fotos deste imóvel também serão apagadas. Deseja continuar?`)) return;

        const alerta = document.getElementById('alerta-sistema');

        fetch('excluir.php', {

            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })

        .then(res => res.json())

        .then(data => {

            alerta.classList.remove('d-none', 'alert-success', 'alert-danger');

            if (data.status === 'success') {

                alerta.classList.add('alert-success');
                alerta.textContent = data.message;
                const linha = document.getElementById(`linha-${id}`);
                linha.style.opacity = 0;
                setTimeout(() => linha.remove(), 500);
            } else {
                
                alerta.classList.add('alert-danger');
                alerta.textContent = data.message;
            }
        });
    }
</script>

</body>

</html>