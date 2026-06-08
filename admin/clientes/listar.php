<?php

session_start();
require_once "../../conexao.php";

if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login.php");
    exit;
}

$stmt = $conn->query("SELECT * FROM clientes");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title>Clientes - Painel Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container-fluid d-flex align-items-center">
    <a href="../index.php" class="btn btn-outline-light btn-sm me-3" ><i class="bi bi-arrow-left"></i> Voltar ao Dashboard</a>
    <span class="text-white">Gerenciamento de Clientes</span>
  </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people"></i> Clientes Interessados</h2>
        <a href="cadastrar.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Novo Cliente</a>
    </div>

    <div id="alerta-sistema" class="alert d-none" role="alert"></div>

    <div class="card shadow-sm">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Interesse</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($clientes) > 0): ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr id="linha-<?php echo $cliente['id']; ?>" class="align-middle">
                                <td class="fw-bold"><?php echo htmlspecialchars($cliente['nome']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['cpf']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['telefone']); ?></td>
                                <td>
                                    <?php 
                                        $cor = 'bg-secondary';
                                        if($cliente['tipo_interesse'] == 'venda') $cor = 'bg-primary';
                                        if($cliente['tipo_interesse'] == 'aluguel') $cor = 'bg-info text-dark';
                                        if($cliente['tipo_interesse'] == 'ambos') $cor = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?php echo $cor; ?> text-uppercase"><?php echo htmlspecialchars($cliente['tipo_interesse']); ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="editar.php?id=<?php echo $cliente['id']; ?>" class="btn btn-warning btn-sm" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deletarCliente(<?php echo $cliente['id']; ?>, '<?php echo htmlspecialchars(addslashes($cliente['nome'])); ?>')" title="Excluir"><i class="bi bi-trash3"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Nenhum cliente cadastrado ainda.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>

    function deletarCliente(id, nome) {

        if (!confirm(`Deseja realmente excluir o cliente ${nome}?`)) return;

        const alerta = document.getElementById('alerta-sistema');

        // Envio de JSON simples!
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
                linha.style.transition = "opacity 0.5s ease";
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