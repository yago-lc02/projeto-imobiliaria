<?php

session_start();
require_once "../conexao.php"; // Puxa a conexão com o banco

// Lógica do Filtro (Venda ou Aluguel)
$filtro = $_GET['tipo'] ?? '';
$sql = "SELECT * FROM imoveis";

if ($filtro === 'venda' || $filtro === 'aluguel') {
    $sql .= " WHERE tipo = :tipo";
}

$sql .= " ORDER BY id DESC LIMIT 6"; // Mostra os 6 mais recentes na Home

$stmt = $conn->prepare($sql);

if ($filtro === 'venda' || $filtro === 'aluguel') {
    $stmt->bindValue(':tipo', $filtro);
}

$stmt->execute();
$imoveis = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>

<html lang="pt-br">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Yago Imóveis</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

        <style>
            body { font-family: 'Inter', sans-serif; }
            body.dark-mode {
                background-color: #121212 !important;
                color: #ffffff !important;
            }
            body.dark-mode .card {
                background-color: #1e1e1e;
                color: white;
                border-color: #333;
            }
            body.dark-mode .text-muted {
                color: #adb5bd !important;
            }
            body.dark-mode .bg-light {
                background-color: #1a1a1a !important;
                color: white !important;
            }
            .hero {
                background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
                background-size: cover;
                background-position: center;
                min-height: 500px;
                color: white;
            }
            .imovel-img {
                height: 250px;
                object-fit: cover;
            }
            .badge-status {
                position: absolute;
                top: 15px;
                right: 15px;
                font-size: 0.9rem;
            }
        </style> 
    </head>

    <body>
        <header class="container-fluid bg-dark text-white py-3">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container">
                    <a class="navbar-brand fs-3" href="index.php">YAGO <span class="text-primary fw-bold">IMÓVEIS</span></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                        <ul class="navbar-nav align-items-center">
                            <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="#imoveis">Imóveis</a></li>
                            <li class="nav-item"><a class="nav-link" href="#sobre">Sobre</a></li>
                            <li class="nav-item"><a class="btn btn-primary ms-3" href="#contato">Contato</a></li>
                            <li>
                                <button id="theme-toggle" class="btn btn-outline-primary ms-3">
                                    <i id="theme-icon" class="bi bi-moon-fill"></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <section class="container-fluid text-center hero d-flex align-items-center justify-content-center">
            <div class="container">
                <h1 class="display-3 fw-bold text-white mb-4"> Encontre o <span class="text-primary">lar dos seus sonhos.</span></h1>
                <p class="fs-5 mb-5">Casas e apartamentos de alto padrão selecionados para você.</p>
                
                <!-- Filtros -->
                <div class="d-flex justify-content-center gap-3">
                    <a href="index.php" class="btn btn-outline-light btn-lg <?php echo $filtro === '' ? 'active' : ''; ?>">Todos</a>
                    <a href="index.php?tipo=venda" class="btn btn-outline-light btn-lg <?php echo $filtro === 'venda' ? 'active' : ''; ?>">Comprar</a>
                    <a href="index.php?tipo=aluguel" class="btn btn-outline-light btn-lg <?php echo $filtro === 'aluguel' ? 'active' : ''; ?>">Alugar</a>
                </div>
            </div>
        </section>

        <!-- Vitrine Dinâmica de Imóveis -->
        <section class="container my-5" id="imoveis">
            <h2 class="text-center mb-5">Imóveis em Destaque</h2>
            <div class="row g-4">
                
                <?php if (count($imoveis) > 0): ?>
                    <?php foreach ($imoveis as $imovel): ?>
                        <div class="col-md-4 col-12">
                            <div class="card h-100 position-relative shadow-sm">
                                <!-- Lógica das cores de status -->
                                <?php 
                                    $corBadge = 'bg-success'; // disponível
                                    if ($imovel['status'] === 'vendido') $corBadge = 'bg-danger';
                                    if ($imovel['status'] === 'alugado') $corBadge = 'bg-warning text-dark';
                                ?>
                                <span class="badge <?php echo $corBadge; ?> badge-status">
                                    <?php echo htmlspecialchars(ucfirst($imovel['status'])); ?>
                                </span>

                                <?php 
                                    $fotos_array = json_decode($imovel['fotos'], true);
                                    $foto_capa = (is_array($fotos_array) && count($fotos_array) > 0) ? "../" . $fotos_array[0] : "https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=800&q=80"; 
                                ?>
                                <img src="<?php echo htmlspecialchars($foto_capa); ?>" class="card-img-top imovel-img" alt="Foto do Imóvel">
                                
                                <div class="card-body">
                                    <span class="text-primary fw-bold text-uppercase small">
                                        <?php echo htmlspecialchars($imovel['tipo']); ?>
                                    </span>
                                    <h5 class="card-title mt-2 fw-bold">
                                        R$ <?php echo htmlspecialchars(number_format($imovel['valor'], 2, ',', '.')); ?>
                                    </h5>
                                    <p class="card-text text-muted mb-2">
                                        <i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($imovel['endereco']); ?>
                                    </p>
                                    <p class="card-text">
                                        <i class="bi bi-arrows-fullscreen"></i> <?php echo htmlspecialchars($imovel['metragem']); ?> m²
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent border-top-0 pb-3 text-center">
                                    <a href="detalhes.php?id=<?php echo $imovel['id']; ?>" class="btn btn-outline-primary w-100">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted fs-5">Nenhum imóvel encontrado no momento.</p>
                    </div>
                <?php endif; ?>

            </div>
        </section>

        <!-- Sessão Sobre -->
        <section class="bg-light py-5" id="sobre">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8 pe-md-5">
                        <h2 class="fw-bold mb-4">Sua parceira de confiança no mercado imobiliário</h2>
                        <p class="fs-5">Com mais de 10 anos de mercado, nossa imobiliária desenvolve soluções que facilitam a realização do seu sonho.</p>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check-circle-fill text-primary me-2"></i> Atendimento personalizado</li>
                            <li><i class="bi bi-check-circle-fill text-primary me-2"></i> Assessoria jurídica completa</li>
                            <li><i class="bi bi-check-circle-fill text-primary me-2"></i> As melhores taxas do mercado</li>
                        </ul>
                    </div>
                    <div class="col-md-4 mt-4 mt-md-0">
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=500&q=60" alt="Sobre a Imobiliária" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </section>

        <!-- Formulário de Contato -->
        <section class="container my-5" id="contato">
            <div class="card p-4 mx-auto" style="max-width: 600px;">
                <h2 class="text-center mb-4">Agende uma Visita</h2>
                <form id="contactform">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="name" class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" id="name" required placeholder="Ex: João Silva">
                        </div>
                        <div class="col-md-6">
                            <label for="cpf" class="form-label">CPF *</label>
                            <input type="text" class="form-control" id="cpf" required placeholder="000.000.000-00">
                        </div>
                        <div class="col-md-6">
                            <label for="telefone" class="form-label">Telefone *</label>
                            <input type="text" class="form-control" id="telefone" required placeholder="(00) 00000-0000">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">E-mail *</label>
                            <input type="email" class="form-control" id="email" required placeholder="seu@email.com">
                        </div>
                        <div class="col-md-6">
                            <label for="interesse" class="form-label">Interesse em *</label>
                            <select class="form-select" id="interesse" required>
                                <option value="">Selecione...</option>
                                <option value="venda">Comprar (Venda)</option>
                                <option value="aluguel">Alugar</option>
                                <option value="ambos">Ambos</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="message" class="form-label">Mensagem / Observações</label>
                            <textarea class="form-control" id="message" rows="3" placeholder="Qual imóvel você gostou?"></textarea>
                        </div>
                        <div class="col-md-12 mt-2">
                            <button class="w-100 btn btn-primary" type="submit">Enviar Solicitação</button>
                        </div>
                    </div>
                </form>
                
                <div id="message-success" class="alert alert-success mt-3 text-center d-none">
                    Solicitação enviada com sucesso! Em breve um corretor entrará em contato.
                </div>
                <button id="button-reload" class="w-100 mt-2 btn btn-warning d-none" onclick="location.reload()">
                    Enviar nova solicitação
                </button>
            </div>
        </section>
        
        <footer class="bg-dark text-white py-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <h5 class="fw-bold border-bottom pb-2">Yago Imóveis</h5>
                        <p class="small text-secondary">Ajudando você a encontrar o imóvel perfeito desde 2014.</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <h5 class="fw-bold border-bottom pb-2">Contato</h5>
                        <p class="small text-secondary mb-1">yago@imoveis.com</p>
                        <p class="small text-secondary">(11) 9999-9999</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <h5 class="fw-bold border-bottom pb-2">Administração</h5>
                        <!-- Botão para o Painel Admin -->
                        <a href="../admin/login.php" class="btn btn-outline-light btn-sm mt-2">
                            <i class="bi bi-lock-fill"></i> Acesso Restrito Admin
                        </a>
                    </div>
                </div>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
        <script>
            
            // Lógica do Dark Mode
            const btn = document.getElementById('theme-toggle');
            const icon = document.getElementById('theme-icon');
            
            // Ao carregar a página, verifica se o usuário já tinha escolhido o Dark Mode antes
            if (localStorage.getItem('theme') === 'dark') {

                document.body.classList.add('dark-mode');
                icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
            }

            btn.addEventListener('click', () => {

                document.body.classList.toggle('dark-mode');
                
                if (document.body.classList.contains('dark-mode')) {

                    icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
                    localStorage.setItem('theme', 'dark'); // Grava no HD do navegador
                } else {

                    icon.classList.replace('bi-sun-fill', 'bi-moon-fill');
                    localStorage.setItem('theme', 'light'); // Grava no HD do navegador
                }
            });

            // Lógica do Formulário
            const contactForm = document.getElementById('contactform');
            const successMessage = document.getElementById('message-success');
            const buttonReload = document.getElementById('button-reload');

            contactForm.addEventListener('submit', function(event) {
                event.preventDefault();
                
                const dados = {
                    name: document.getElementById('name').value,
                    cpf: document.getElementById('cpf').value,
                    telefone: document.getElementById('telefone').value,
                    email: document.getElementById('email').value,
                    interesse: document.getElementById('interesse').value,
                    message: document.getElementById('message').value
                };
                
                console.log('✅ Solicitação recebida do site público! Dados prontos para o Admin cadastrar:', dados);

                contactForm.classList.add('d-none');
                successMessage.classList.remove('d-none');
                buttonReload.classList.remove('d-none');
            });
        </script>

    </body>

</html>