<?php

session_start();

if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login.php");
    exit;
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title>Novo Cliente</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container-fluid d-flex align-items-center">
    <a href="listar.php" class="btn btn-outline-light btn-sm me-3" ><i class="bi bi-arrow-left"></i> Voltar</a>
    <span class="text-white">Cadastrar Cliente</span>
  </div>
</nav>

<div class="container" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="mb-4">Dados do Cliente</h3>
            <div id="alerta" class="alert d-none" role="alert"></div>

            <form id="form-cadastrar">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="nome" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CPF *</label>
                        <input type="text" class="form-control" id="cpf" placeholder="000.000.000-00" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">E-mail *</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefone *</label>
                        <input type="text" class="form-control" id="telefone" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Interesse em *</label>
                        <select class="form-select" id="tipo_interesse" required>
                            <option value="">Selecione...</option>
                            <option value="venda">Comprar (Venda)</option>
                            <option value="aluguel">Alugar</option>
                            <option value="ambos">Ambos</option>
                        </select>
                    </div>
                </div>
                <hr class="my-4">
                <button type="submit" class="btn btn-success w-100" id="btn-salvar">Salvar Cliente</button>
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
        btn.textContent = "Salvando...";

        // Objeto JSON simples
        const dados = {

            nome: document.getElementById('nome').value,
            cpf: document.getElementById('cpf').value,
            email: document.getElementById('email').value,
            telefone: document.getElementById('telefone').value,
            tipo_interesse: document.getElementById('tipo_interesse').value
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
            btn.textContent = "Salvar Cliente";
        });
    });
</script>

</body>

</html>