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

    <title>Novo Corretor</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container-fluid d-flex align-items-center">
    <a href="listar.php" class="btn btn-outline-light btn-sm me-3" ><i class="bi bi-arrow-left"></i> Voltar</a>
    <span class="text-white">Cadastrar Corretor</span>
  </div>
</nav>

<div class="container" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="mb-4">Informações do Corretor</h3>
            
            <div id="alerta" class="alert d-none" role="alert"></div>

            <form id="form-cadastrar">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="nome" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CRECI *</label>
                        <input type="text" class="form-control" id="creci" placeholder="Ex: 12345-F" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefone *</label>
                        <input type="text" class="form-control" id="telefone" placeholder="(00) 00000-0000" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Especialidade *</label>
                        <select class="form-select" id="especialidade" required>
                            <option value="">Selecione...</option>
                            <option value="Vendas de Alto Padrão">Vendas de Alto Padrão</option>
                            <option value="Locação Comercial">Locação Comercial</option>
                            <option value="Lançamentos">Lançamentos</option>
                            <option value="Geral">Geral</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Foto de Perfil (JPG, PNG)</label>
                        <input type="file" class="form-control" id="foto" accept="image/png, image/jpeg">
                    </div>
                </div>
                <hr class="my-4">
                <button type="submit" class="btn btn-success w-100" id="btn-salvar">Salvar Corretor</button>
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

        // FormData porque tem arquivo de imagem envolvido!
        const formData = new FormData();
        formData.append('nome', document.getElementById('nome').value);
        formData.append('creci', document.getElementById('creci').value);
        formData.append('telefone', document.getElementById('telefone').value);
        formData.append('especialidade', document.getElementById('especialidade').value);
        
        const fotoInput = document.getElementById('foto');

        if (fotoInput.files.length > 0) {

            formData.append('foto', fotoInput.files[0]);
        }

        fetch('salvar.php', {

            method: 'POST',
            body: formData // Não é convertido para JSON, enviado o FormData cru
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
            btn.textContent = "Salvar Corretor";
        })
        .catch(() => {
            
            alerta.classList.remove('d-none', 'alert-success');
            alerta.classList.add('alert-danger');
            alerta.textContent = "Erro de conexão com o servidor.";
            btn.disabled = false;
            btn.textContent = "Salvar Corretor";
        });
    });
</script>

</body>

</html>