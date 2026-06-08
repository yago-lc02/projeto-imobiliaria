<?php

session_start();
require_once "../../conexao.php";

if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = :id");
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$stmt->execute();

$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {

    echo "<script>alert('Não encontrado!'); window.location.href='listar.php';</script>";
    exit;
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title>Editar Cliente</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container-fluid d-flex align-items-center">
    <a href="listar.php" class="btn btn-outline-light btn-sm me-3" ><i class="bi bi-arrow-left"></i> Voltar</a>
    <span class="text-white">Editar Cliente</span>
  </div>
</nav>

<div class="container" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="mb-4">Atualizar Informações</h3>
            <div id="alerta" class="alert d-none" role="alert"></div>

            <form id="form-editar">
                <input type="hidden" id="id_cliente" value="<?php echo $cliente['id']; ?>">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CPF *</label>
                        <input type="text" class="form-control" id="cpf" value="<?php echo htmlspecialchars($cliente['cpf']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">E-mail *</label>
                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefone *</label>
                        <input type="text" class="form-control" id="telefone" value="<?php echo htmlspecialchars($cliente['telefone']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Interesse em *</label>
                        <select class="form-select" id="tipo_interesse" required>
                            <option value="venda" <?php echo $cliente['tipo_interesse'] === 'venda' ? 'selected' : ''; ?>>Comprar (Venda)</option>
                            <option value="aluguel" <?php echo $cliente['tipo_interesse'] === 'aluguel' ? 'selected' : ''; ?>>Alugar</option>
                            <option value="ambos" <?php echo $cliente['tipo_interesse'] === 'ambos' ? 'selected' : ''; ?>>Ambos</option>
                        </select>
                    </div>
                </div>
                <hr class="my-4">
                <button type="submit" class="btn btn-warning w-100" id="btn-salvar">Salvar Alterações</button>
            </form>
        </div>
    </div>
</div>

<script>

    document.getElementById("form-editar").addEventListener("submit", function(e) {

        e.preventDefault();
        
        const btn = document.getElementById("btn-salvar");
        const alerta = document.getElementById("alerta");
        btn.disabled = true;
        btn.textContent = "Atualizando...";

        const dados = {

            id: document.getElementById('id_cliente').value,
            nome: document.getElementById('nome').value,
            cpf: document.getElementById('cpf').value,
            email: document.getElementById('email').value,
            telefone: document.getElementById('telefone').value,
            tipo_interesse: document.getElementById('tipo_interesse').value
        };

        fetch('atualizar.php', {

            method: 'PUT', // Enviando JSON sem imagens
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        })

        .then(res => res.json())

        .then(data => {

            alerta.classList.remove('d-none', 'alert-success', 'alert-danger');

            if (data.status === 'success') {

                alerta.classList.add('alert-success');
                alerta.textContent = data.message;
                setTimeout(() => window.location.href = 'listar.php', 1500);
            } else {
                
                alerta.classList.add('alert-danger');
                alerta.textContent = data.message;
                btn.disabled = false;
                btn.textContent = "Salvar Alterações";
            }
        });
    });
</script>

</body>

</html>