<?php

session_start();
require_once "../conexao.php";

if (!isset($_SESSION['admin_id'])) {

    header("Location: login.php");
    exit;
}

// Buscando totais para os cards do painel
$total_imoveis = $conn->query("SELECT COUNT(*) FROM imoveis")->fetchColumn();
$total_corretores = $conn->query("SELECT COUNT(*) FROM corretores")->fetchColumn();
$total_clientes = $conn->query("SELECT COUNT(*) FROM clientes")->fetchColumn();

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Painel Admin - Yago Imóveis</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { min-height: calc(100vh - 56px); background-color: #343a40; }
        .sidebar a { color: #cfd8dc; text-decoration: none; padding: 10px 20px; display: block; border-bottom: 1px solid #454d55; }
        .sidebar a:hover { background-color: #495057; color: white; }
    </style>

</head>

<body>

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"><i class="bi bi-buildings"></i> Yago Imóveis - Admin</a>
    <div class="d-flex align-items-center">
        <a href="logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </div>
  </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0 sidebar d-flex flex-column">
            <a href="index.php" class="bg-primary text-white"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="imoveis/listar.php"><i class="bi bi-house-door"></i> Imóveis</a>
            <a href="corretores/listar.php"><i class="bi bi-person-badge"></i> Corretores</a>
            <a href="clientes/listar.php"><i class="bi bi-people"></i> Clientes</a>
            <a href="visitas/listar.php"><i class="bi bi-calendar-check"></i> Visitas</a>
            <a href="../public/index.php" target="_blank" class="mt-auto text-warning"><i class="bi bi-box-arrow-up-right"></i> Ver Site Público</a>
        </div>

        <div class="col-md-10 p-4">
            <h2 class="mb-4">Visão Geral do Sistema</h2>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-house-door"></i> Imóveis Cadastrados</h5>
                            <p class="display-4 fw-bold"><?php echo $total_imoveis; ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <a href="imoveis/listar.php" class="text-white text-decoration-none">Gerenciar imóveis ➔</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-white bg-success h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-person-badge"></i> Corretores Ativos</h5>
                            <p class="display-4 fw-bold"><?php echo $total_corretores; ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <a href="corretores/listar.php" class="text-white text-decoration-none">Gerenciar corretores ➔</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white bg-warning h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-dark"><i class="bi bi-people"></i> Clientes Interessados</h5>
                            <p class="display-4 fw-bold text-dark"><?php echo $total_clientes; ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <a href="clientes/listar.php" class="text-dark text-decoration-none">Gerenciar clientes ➔</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>