<?php

session_start();
require_once "../../conexao.php";

if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

// Busca os dados do corretor para preencher o formulário
$stmt = $conn->prepare("SELECT * FROM corretores WHERE id = :id");
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$stmt->execute();

$corretor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$corretor) {

    echo "<script>alert('Corretor não encontrado!'); window.location.href='listar.php';</script>";
    exit;
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title>Editar Corretor</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container-fluid d-flex align-items-center">
    <a href="listar.php" class="btn btn-outline-light btn-sm me-3" ><i class="bi bi-arrow-left"></i> Voltar</a>
    <span class="text-white">Editar Corretor</span>
  </div>
</nav>

<div class="container" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="mb-4">Atualizar Informações</h3>
            
            <div id="alerta" class="alert d-none" role="alert"></div>

            <form id="form-editar">
                <input type="hidden" id="id_corretor" value="<?php echo $corretor['id']; ?>">

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="nome" value="<?php echo htmlspecialchars($corretor['nome']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CRECI *</label>
                        <input type="text" class="form-control" id="creci" value="<?php echo htmlspecialchars($corretor['creci']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefone *</label>
                        <input type="text" class="form-control" id="telefone" value="<?php echo htmlspecialchars($corretor['telefone']); ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Especialidade *</label>
                        <select class="form-select" id="especialidade" required>
                            <option value="Vendas de Alto Padrão" <?php echo $corretor['especialidade'] === 'Vendas de Alto Padrão' ? 'selected' : ''; ?>>Vendas de Alto Padrão</option>
                            <option value="Locação Comercial" <?php echo $corretor['especialidade'] === 'Locação Comercial' ? 'selected' : ''; ?>>Locação Comercial</option>
                            <option value="Lançamentos" <?php echo $corretor['especialidade'] === 'Lançamentos' ? 'selected' : ''; ?>>Lançamentos</option>
                            <option value="Geral" <?php echo $corretor['especialidade'] === 'Geral' ? 'selected' : ''; ?>>Geral</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12">
                        <label class="form-label">Foto de Perfil (Deixe em branco para manter a atual)</label>
                        <?php if(!empty($corretor['foto'])): ?>
                            <div class="mb-2">
                                <img src="../../<?php echo htmlspecialchars($corretor['foto']); ?>" alt="Foto Atual" class="img-thumbnail" style="height: 80px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="foto" accept="image/png, image/jpeg">
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

        const formData = new FormData();
        formData.append('id', document.getElementById('id_corretor').value);
        formData.append('nome', document.getElementById('nome').value);
        formData.append('creci', document.getElementById('creci').value);
        formData.append('telefone', document.getElementById('telefone').value);
        formData.append('especialidade', document.getElementById('especialidade').value);
        
        const fotoInput = document.getElementById('foto');

        // Só anexa a foto no envio se o usuário realmente tiver selecionado um novo arquivo
        if (fotoInput.files.length > 0) {

            formData.append('foto', fotoInput.files[0]);
        }

        fetch('atualizar.php', {

            method: 'POST', // POST por causa do upload de imagem
            body: formData
        })

        .then(res => res.json())

        .then(data => {

            alerta.classList.remove('d-none', 'alert-success', 'alert-danger');
            
            if (data.status === 'success') {

                alerta.classList.add('alert-success');
                alerta.textContent = data.message;
                // Redireciona de volta para a lista após 1.5 segundos
                setTimeout(() => window.location.href = 'listar.php', 1500);
            } else {

                alerta.classList.add('alert-danger');
                alerta.textContent = data.message;
                btn.disabled = false;
                btn.textContent = "Salvar Alterações";
            }
        })
        .catch(() => {

            alerta.classList.remove('d-none', 'alert-success');
            alerta.classList.add('alert-danger');
            alerta.textContent = "Erro de conexão com o servidor.";
            btn.disabled = false;
            btn.textContent = "Salvar Alterações";
        });
    });
</script>

</body>

</html>