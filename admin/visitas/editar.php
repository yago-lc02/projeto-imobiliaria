<?php

session_start();
require_once "../../conexao.php";

if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM visitas WHERE id = :id");
$stmt->bindValue(":id", $id);
$stmt->execute();

$visita = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$visita) {

    echo "<script>alert('Visita não encontrada!'); window.location.href='listar.php';</script>";
    exit;
}

// Para a edição, é carregado TODOS os imóveis (mesmo os vendidos, pois a visita já pode ter ocorrido)
$imoveis = $conn->query("SELECT id, endereco FROM imoveis ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$clientes = $conn->query("SELECT id, nome FROM clientes ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
$corretores = $conn->query("SELECT id, nome FROM corretores ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title>Editar Visita</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container-fluid d-flex align-items-center">
    <a href="listar.php" class="btn btn-outline-light btn-sm me-3" ><i class="bi bi-arrow-left"></i> Voltar</a>
    <span class="text-white">Editar Visita</span>
  </div>
</nav>

<div class="container" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="mb-4">Atualizar Visita / Lançar Resultado</h3>
            <div id="alerta" class="alert d-none" role="alert"></div>

            <form id="form-editar">
                <input type="hidden" id="id_visita" value="<?php echo $visita['id']; ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Data e Hora *</label>
                        <input type="datetime-local" class="form-control" id="data_horario" value="<?php echo date('Y-m-d\TH:i', strtotime($visita['data_horario'])); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Corretor Responsável *</label>
                        <select class="form-select" id="corretor_id" required>
                            <?php foreach($corretores as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo $visita['corretor_id'] == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Imóvel *</label>
                        <select class="form-select" id="imovel_id" required>
                            <?php foreach($imoveis as $i): ?>
                                <option value="<?php echo $i['id']; ?>" <?php echo $visita['imovel_id'] == $i['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($i['endereco']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Cliente *</label>
                        <select class="form-select" id="cliente_id" required>
                            <?php foreach($clientes as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo $visita['cliente_id'] == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" rows="2"><?php echo htmlspecialchars($visita['observacoes'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-12 mt-4 p-3 bg-warning bg-opacity-10 rounded">
                        <label class="form-label fw-bold">Resultado da Visita</label>
                        <input type="text" class="form-control border-warning" id="resultado" value="<?php echo htmlspecialchars($visita['resultado'] ?? ''); ?>" placeholder="Ex: Cliente gostou, faremos proposta / Cliente achou pequeno...">
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

            id: document.getElementById('id_visita').value,
            data_horario: document.getElementById('data_horario').value,
            corretor_id: document.getElementById('corretor_id').value,
            imovel_id: document.getElementById('imovel_id').value,
            cliente_id: document.getElementById('cliente_id').value,
            observacoes: document.getElementById('observacoes').value,
            resultado: document.getElementById('resultado').value
        };

        fetch('atualizar.php', {

            method: 'PUT',
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