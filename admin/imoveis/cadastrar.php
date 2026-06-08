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

    <title>Novo Imóvel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container-fluid d-flex align-items-center">
    <a href="listar.php" class="btn btn-outline-light btn-sm me-3" ><i class="bi bi-arrow-left"></i> Voltar</a>
    <span class="text-white">Cadastrar Imóvel</span>
  </div>
</nav>

<div class="container" style="max-width: 900px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="mb-4">Dados do Imóvel</h3>
            <div id="alerta" class="alert d-none" role="alert"></div>

            <form id="form-cadastrar">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tipo de Negócio *</label>
                        <select class="form-select" id="tipo" required>
                            <option value="">Selecione...</option>
                            <option value="venda">Venda</option>
                            <option value="aluguel">Aluguel</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Valor (R$) *</label>
                        <input type="number" step="0.01" class="form-control" id="valor" placeholder="Ex: 500000.00" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Metragem (m²) *</label>
                        <input type="number" step="0.01" class="form-control" id="metragem" placeholder="Ex: 120.5" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Endereço Completo *</label>
                        <input type="text" class="form-control" id="endereco" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Fotos do Imóvel (Pode selecionar várias) *</label>
                        <input type="file" class="form-control" id="fotos" accept="image/png, image/jpeg" multiple required>
                        <div class="form-text">Selecione todas as fotos segurando a tecla CTRL.</div>
                    </div>
                </div>
                <hr class="my-4">
                <button type="submit" class="btn btn-success w-100" id="btn-salvar">Salvar Imóvel</button>
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
        btn.textContent = "Guardando Imóvel e Fotos (Pode demorar um pouco)...";

        const formData = new FormData();
        formData.append('tipo', document.getElementById('tipo').value);
        formData.append('valor', document.getElementById('valor').value);
        formData.append('metragem', document.getElementById('metragem').value);
        formData.append('endereco', document.getElementById('endereco').value);
        
        // Loop mágico para enviar MÚLTIPLAS fotos via AJAX
        const fotosInput = document.getElementById('fotos');

        for (let i = 0; i < fotosInput.files.length; i++) {

            // O "[]" no nome, avisa o PHP que é um Array de ficheiros
            formData.append('fotos[]', fotosInput.files[i]);
        }

        fetch('salvar.php', {

            method: 'POST',
            body: formData
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
            btn.textContent = "Salvar Imóvel";
        });
    });
</script>

</body>

</html>