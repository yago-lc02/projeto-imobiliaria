<?php

session_start();
require_once "../../conexao.php"; // Dois saltos para trás: sai de corretores, sai de admin, acha a conexao

// Trava de segurança
if (!isset($_SESSION['admin_id'])) {
    
    header("Location: ../login.php");
    exit;
}

// Busca todos os corretores no banco
$stmt = $conn->query("SELECT * FROM corretores ORDER BY id DESC");
$corretores = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Corretores - Painel Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container-fluid d-flex align-items-center">
    <a href="../index.php" class="btn btn-outline-light btn-sm me-3" ><i class="bi bi-arrow-left"></i> Voltar ao Dashboard</a>
    <span class="text-white">Gerenciamento de Corretores</span>
  </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person-badge"></i> Corretores Cadastrados</h2>
        <a href="cadastrar.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Novo Corretor</a>
    </div>

    <div id="alerta-sistema" class="alert d-none" role="alert"></div>

    <div class="card shadow-sm">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>CRECI</th>
                        <th>Telefone</th>
                        <th>Especialidade</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($corretores) > 0): ?>
                        <?php foreach ($corretores as $corretor): ?>
                            <tr id="linha-<?php echo $corretor['id']; ?>" class="align-middle">
                                <td>
                                    <?php if (!empty($corretor['foto'])): ?>
                                        <img src="../../<?php echo htmlspecialchars($corretor['foto']); ?>" alt="Foto" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded-circle d-flex justify-content-center align-items-center text-white" style="width: 45px; height: 45px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?php echo htmlspecialchars($corretor['nome']); ?></td>
                                <td><?php echo htmlspecialchars($corretor['creci']); ?></td>
                                <td><?php echo htmlspecialchars($corretor['telefone']); ?></td>
                                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($corretor['especialidade']); ?></span></td>
                                <td class="text-center">
                                    <a href="editar.php?id=<?php echo $corretor['id']; ?>" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deletarCorretor(<?php echo $corretor['id']; ?>, '<?php echo htmlspecialchars(addslashes($corretor['nome'])); ?>')" title="Excluir">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Nenhum corretor cadastrado ainda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>

    function deletarCorretor(id, nome) {

        if (!confirm(`Tem certeza que deseja excluir o corretor ${nome}?`)) {

            return;
        }

        const alerta = document.getElementById('alerta-sistema');

        // Faz o fetch para o arquivo excluir.php enviando o ID
        fetch('excluir.php', {

            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })

        .then(response => response.json())
        
        .then(data => {

            alerta.classList.remove('d-none', 'alert-success', 'alert-danger');
            
            if (data.status === 'success') {

                alerta.classList.add('alert-success');
                alerta.textContent = data.message;
                
                // Remove a linha da tabela com uma transição suave
                const linha = document.getElementById(`linha-${id}`);
                linha.style.transition = "opacity 0.5s ease";
                linha.style.opacity = 0;
                setTimeout(() => linha.remove(), 500);
            } else {

                alerta.classList.add('alert-danger');
                alerta.textContent = data.message;
            }
        })
        .catch(() => {
            
            alerta.classList.remove('d-none', 'alert-success');
            alerta.classList.add('alert-danger');
            alerta.textContent = 'Erro de conexão com o servidor.';
        });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>