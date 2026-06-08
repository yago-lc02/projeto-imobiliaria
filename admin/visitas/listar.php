<?php

session_start();
require_once "../../conexao.php";

if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login.php");
    exit;
}

// Consulta relacional usando JOIN para buscar os nomes em vez dos IDs
$sql = "SELECT v.*, i.endereco AS imovel_end, c.nome AS cliente_nome, co.nome AS corretor_nome 
        FROM visitas v
        JOIN imoveis i ON v.imovel_id = i.id
        JOIN clientes c ON v.cliente_id = c.id
        JOIN corretores co ON v.corretor_id = co.id
        ORDER BY v.data_horario DESC";

$stmt = $conn->query($sql);
$visitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title>Visitas - Painel Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container-fluid d-flex align-items-center">
    <a href="../index.php" class="btn btn-outline-light btn-sm me-3" ><i class="bi bi-arrow-left"></i> Voltar ao Dashboard</a>
    <span class="text-white">Gerenciamento de Visitas</span>
  </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar-check"></i> Agenda de Visitas</h2>
        <a href="cadastrar.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Agendar Visita</a>
    </div>

    <div id="alerta-sistema" class="alert d-none" role="alert"></div>

    <div class="card shadow-sm">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Data/Hora</th>
                        <th>Imóvel</th>
                        <th>Cliente</th>
                        <th>Corretor</th>
                        <th>Resultado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($visitas) > 0): ?>
                        <?php foreach ($visitas as $visita): ?>
                            <tr id="linha-<?php echo $visita['id']; ?>" class="align-middle">
                                <td class="fw-bold text-primary">
                                    <?php echo date('d/m/Y \à\s H:i', strtotime($visita['data_horario'])); ?>
                                </td>
                                <td title="<?php echo htmlspecialchars($visita['imovel_end']); ?>">
                                    <?php echo htmlspecialchars(substr($visita['imovel_end'], 0, 25)) . '...'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($visita['cliente_nome']); ?></td>
                                <td><?php echo htmlspecialchars($visita['corretor_nome']); ?></td>
                                <td>
                                    <?php 
                                        $res = $visita['resultado'];
                                        if (empty($res)) {
                                            echo '<span class="badge bg-secondary">Pendente</span>';
                                        } else {
                                            echo '<span class="badge bg-info text-dark">' . htmlspecialchars($res) . '</span>';
                                        }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <a href="editar.php?id=<?php echo $visita['id']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deletarVisita(<?php echo $visita['id']; ?>)"><i class="bi bi-trash3"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Nenhuma visita agendada.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>

    function deletarVisita(id) {

        if (!confirm(`Deseja realmente cancelar e excluir esta visita?`)) return;

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