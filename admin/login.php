<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Yago Imóveis</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 100%; max-width: 400px; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); background: white; }
    </style>

</head>

<body>

<div class="login-card">

    <h3 class="text-center mb-4"><i class="bi bi-buildings text-primary"></i> Yago Imóveis - Admin</h3>
    
    <div id="alerta-erro" class="alert alert-danger d-none" role="alert"></div>

    <form id="form-login">
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" placeholder="admin@imobiliaria.com" required>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-primary w-100" id="btn-entrar">Entrar no Painel</button>
    </form>
</div>

<script>
    document.getElementById("form-login").addEventListener("submit", function(event) {

        event.preventDefault(); // Impede a página de recarregar
        
        const btnEntrar = document.getElementById("btn-entrar");
        const alertaErro = document.getElementById("alerta-erro");
        
        // Muda o botão para mostrar que está carregando
        btnEntrar.disabled = true;
        btnEntrar.textContent = "Verificando...";
        alertaErro.classList.add("d-none"); // Esconde erros antigos

        const dados = {
            email: document.getElementById("email").value.trim(),
            senha: document.getElementById("senha").value.trim()
        };

        fetch("auth.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(dados)
        })

        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {

                // Deu certo! Manda o admin para o painel principal
                window.location.href = "index.php";
            } else {

                // Erro: Mostra o alerta vermelho do Bootstrap
                alertaErro.textContent = data.message;
                alertaErro.classList.remove("d-none");
                btnEntrar.disabled = false;
                btnEntrar.textContent = "Entrar no Painel";
            }
        })
        .catch(() => {
            alertaErro.textContent = "Erro de conexão com o servidor.";
            alertaErro.classList.remove("d-none");
            btnEntrar.disabled = false;
            btnEntrar.textContent = "Entrar no Painel";
        });
    });
</script>

</body>

</html>