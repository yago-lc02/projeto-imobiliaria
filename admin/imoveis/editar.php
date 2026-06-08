<?php

session_start();
require_once "../../conexao.php";

if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM imoveis WHERE id = :id");
$stmt->bindValue(":id", $id);
$stmt->execute();

$imovel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$imovel) { echo "<script>alert('Imóvel não encontrado!'); window.location.href='listar.php';</script>"; exit; }

$fotos_atuais = json_decode($imovel['fotos'], true);

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title>Editar Imóvel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container-fluid d-flex align-items-center">
    <a href="listar.php" class="btn btn-outline-light btn-sm me-3" ><i class="bi bi-arrow-left"></i> Voltar</a>
    <span class="text-white">Editar Imóvel</span>
  </div>
</nav>

<div class="container" style="max-width: 900px;">
    <div class="card shadow-sm mb-5">
        <div class="card-body p-4">
            <h3 class="mb-4">Atualizar Informações</h3>
            <div id="alerta" class="alert d-none" role="alert"></div>

            <form id="form-editar">
                <input type="hidden" id="id_imovel" value="<?php echo $imovel['id']; ?>">

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select bg-warning bg-opacity-25" id="status" required>
                            <option value="disponivel" <?php echo $imovel['status'] === 'disponivel' ? 'selected' : ''; ?>>Disponível</option>
                            <option value="vendido" <?php echo $imovel['status'] === 'vendido' ? 'selected' : ''; ?>>Vendido</option>
                            <option value="alugado" <?php echo $imovel['status'] === 'alugado' ? 'selected' : ''; ?>>Alugado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo *</label>
                        <select class="form-select" id="tipo" required>
                            <option value="venda" <?php echo $imovel['tipo'] === 'venda' ? 'selected' : ''; ?>>Venda</option>
                            <option value="aluguel" <?php echo $imovel['tipo'] === 'aluguel' ? 'selected' : ''; ?>>Aluguel</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Valor (R$) *</label>
                        <input type="number" step="0.01" class="form-control" id="valor" value="<?php echo $imovel['valor']; ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Metragem (m²) *</label>
                        <input type="number" step="0.01" class="form-control" id="metragem" value="<?php echo $imovel['metragem']; ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Endereço Completo *</label>
                        <input type="text" class="form-control" id="endereco" value="<?php echo htmlspecialchars($imovel['endereco']); ?>" required>
                    </div>
                    
                    <div class="col-md-12 mt-4">
                        <label class="form-label">Fotos do Imóvel (Deixe em branco para manter as atuais)</label>
                        
                        <?php if (is_array($fotos_atuais) && count($fotos_atuais) > 0): ?>
                            <div class="d-flex gap-2 mb-3 overflow-auto pb-2">
                                <?php foreach ($fotos_atuais as $foto): ?>
                                    <img src="../../<?php echo $foto; ?>" class="img-thumbnail" style="height: 80px; width: 100px; object-fit: cover;">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <input type="file" class="form-control" id="fotos" accept="image/png, image/jpeg" multiple>
                        <div class="form-text text-danger">Atenção: Ao selecionar novas fotos, todas as fotos antigas serão substituídas.</div>
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
        formData.append('id', document.getElementById('id_imovel').value);
        formData.append('status', document.getElementById('status').value);
        formData.append('tipo', document.getElementById('tipo').value);
        formData.append('valor', document.getElementById('valor').value);
        formData.append('metragem', document.getElementById('metragem').value);
        formData.append('endereco', document.getElementById('endereco').value);
        
        const fotosInput = document.getElementById('fotos');

        if (fotosInput.files.length > 0) {

            for (let i = 0; i < fotosInput.files.length; i++) {

                formData.append('fotos[]', fotosInput.files[i]);
            }
        }

        fetch('atualizar.php', {

            method: 'POST', // POST para permitir envio de ficheiros
            body: formData
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