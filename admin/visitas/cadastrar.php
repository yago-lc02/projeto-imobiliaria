<?php

session_start();
require_once "../../conexao.php";

if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login.php");
    exit;
}

// Prepara as listas para os campos <select>
$imoveis = $conn->query("SELECT id, endereco FROM imoveis WHERE status = 'disponivel' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$clientes = $conn->query("SELECT id, nome FROM clientes ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
$corretores = $conn->query("SELECT id, nome FROM corretores ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title>Agendar Visita</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container-fluid d-flex align-items-center">
    <a href="listar.php" class="btn btn-outline-light btn-sm me-3" ><i class="bi bi-arrow-left"></i> Voltar</a>
    <span class="text-white">Agendar Visita</span>
  </div>
</nav>

<div class="container" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="mb-4">Detalhes da Visita</h3>
            <div id="alerta" class="alert d-none" role="alert"></div>

            <form id="form-cadastrar">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Data e Hora *</label>
                        <input type="datetime-local" class="form-control" id="data_horario" min="<?php echo date('Y-m-d\TH:i'); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Corretor Responsável *</label>
                        <select class="form-select" id="corretor_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach($corretores as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Imóvel a ser visitado *</label>
                        <select class="form-select" id="imovel_id" required>
                            <option value="">Selecione um imóvel disponível...</option>
                            <?php foreach($imoveis as $i): ?>
                                <option value="<?php echo $i['id']; ?>"><?php echo htmlspecialchars($i['endereco']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Cliente Interessado *</label>
                        <select class="form-select" id="cliente_id" required>
                            <option value="">Selecione o cliente...</option>
                            <?php foreach($clientes as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Observações Prévias</label>
                        <textarea class="form-control" id="observacoes" rows="2" placeholder="Ex: Cliente só tem disponibilidade pela manhã..."></textarea>
                    </div>
                </div>
                <hr class="my-4">
                <button type="submit" class="btn btn-success w-100" id="btn-salvar">Agendar Visita</button>
            </form>
        </div>
    </div>
</div>

<script>

    document.getElementById("form-cadastrar").addEventListener("submit", function(e) {

        e.preventDefault();

        const btn = document.getElementById("btn-salvar");
        const alerta = document.getElementById("alerta");
        btn.disabled = true;
        btn.textContent = "Agendando...";

        const dados = {

            data_horario: document.getElementById('data_horario').value,
            corretor_id: document.getElementById('corretor_id').value,
            imovel_id: document.getElementById('imovel_id').value,
            cliente_id: document.getElementById('cliente_id').value,
            observacoes: document.getElementById('observacoes').value
        };

        fetch('salvar.php', {

            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        })

        .then(res => res.json())

        .then(data => {

            alerta.classList.remove('d-none', 'alert-success', 'alert-danger');

            if (data.status === 'success') {

                alerta.classList.add('alert-success');
                alerta.textContent = data.message;
                document.getElementById("form-cadastrar").reset();
            } else {

                alerta.classList.add('alert-danger');
                alerta.textContent = data.message;
            }
            
            btn.disabled = false;
            btn.textContent = "Agendar Visita";
        });
    });
</script>

</body>

</html>