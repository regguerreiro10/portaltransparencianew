<?php

$config = require __DIR__ . '/admin/app/config/minierp.php';

$query = '';
if (isset($_GET['q']) && is_scalar($_GET['q'])) {
    $query = trim((string) $_GET['q']);
} elseif (isset($_GET['busca']) && is_scalar($_GET['busca'])) {
    $query = trim((string) $_GET['busca']);
}

$results = [
    'modulos' => [],
    'noticias' => [],
    'licitacoes' => [],
];
$error = '';

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function formatDateBr($date)
{
    if (empty($date)) {
        return '';
    }

    $timestamp = strtotime((string) $date);
    return $timestamp ? date('d/m/Y', $timestamp) : '';
}

function normalizeSearchText($value)
{
    $value = strtolower((string) $value);
    $value = strtr($value, [
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'ç' => 'c',
    ]);

    return $value;
}

function textMatchesQuery($query, array $fields)
{
    $query = normalizeSearchText($query);

    foreach ($fields as $field) {
        if (strpos(normalizeSearchText($field), $query) !== false) {
            return true;
        }
    }

    return false;
}

$modules = [
    [
        'title' => 'Gestao de Documentos',
        'description' => 'Modulo para organizacao, tramitacao e consulta de documentos publicos.',
        'url' => 'gestao-documentos.html',
        'keywords' => 'documentos gestao protocolo processos arquivos',
    ],
    [
        'title' => 'Modulo Financeiro',
        'description' => 'Consultas de receitas, despesas, pagamentos, empenhos e informacoes financeiras.',
        'url' => 'modulo-financeiro.html',
        'keywords' => 'financeiro financas receitas despesas orcamento pagamentos empenhos',
    ],
    [
        'title' => 'Modulo de Relatorios',
        'description' => 'Relatorios, estatisticas e paineis de acompanhamento do portal.',
        'url' => 'relatorios-modulo.html',
        'keywords' => 'relatorios estatisticas indicadores graficos modulo',
    ],
    [
        'title' => 'Licitacoes',
        'description' => 'Divulgacao e acompanhamento dos processos de licitacao.',
        'url' => 'licitacoes-consulta.html',
        'keywords' => 'licitacao licitacoes editais pregoes compras publicas',
    ],
    [
        'title' => 'Processo Administrativo',
        'description' => 'Acompanhamento de processos administrativos, tramitacoes e anexos.',
        'url' => 'processo-administrativo.html',
        'keywords' => 'processo administrativo tramitacao protocolo sigilo anexos',
    ],
    [
        'title' => 'Processo Legislativo',
        'description' => 'Acesso a legislacao vigente, normas e documentos legislativos.',
        'url' => 'legislacao-portal.html',
        'keywords' => 'legislacao legislativo leis decretos normas',
    ],
    [
        'title' => 'Contratos',
        'description' => 'Consulta e acompanhamento de contratos administrativos.',
        'url' => 'contratos-portal.html',
        'keywords' => 'contratos administrativos fornecedores obras',
    ],
    [
        'title' => 'Gestao de Pessoas',
        'description' => 'Informacoes sobre servidores, remuneracao, diarias, passagens e pessoal.',
        'url' => 'gestao-pessoas-portal.html',
        'keywords' => 'pessoas servidores remuneracao salarios cargos diarias passagens',
    ],
    [
        'title' => 'LGPD',
        'description' => 'Informacoes sobre protecao de dados pessoais no portal.',
        'url' => 'lgpd-portal.html',
        'keywords' => 'dados pessoais privacidade protecao lgpd',
    ],
    [
        'title' => 'Convenios',
        'description' => 'Consulta e acompanhamento de convenios firmados.',
        'url' => 'convenios-portal.html',
        'keywords' => 'convenios parcerias repasses instrumentos',
    ],
    [
        'title' => 'Obras',
        'description' => 'Divulgacao e acompanhamento de obras publicas.',
        'url' => 'obras-portal.html',
        'keywords' => 'obras publicas contratos acompanhamento execucao',
    ],
    [
        'title' => 'Prestacao de Contas',
        'description' => 'Publicacoes e consultas de prestacao de contas.',
        'url' => 'prestacao-contas.html',
        'keywords' => 'contas prestacao transparencia relatorios',
    ],
    [
        'title' => 'Perguntas Frequentes',
        'description' => 'Respostas para as duvidas frequentes sobre o portal.',
        'url' => 'perguntas-frequentes.html',
        'keywords' => 'faq perguntas frequentes duvidas ajuda',
    ],
];

if ($query !== '') {
    $normalizedQuery = normalizeSearchText($query);

    foreach ($modules as $module) {
        if (
            in_array($normalizedQuery, ['modulo', 'modulos'], true)
            || textMatchesQuery($query, [$module['title'], $module['description'], $module['keywords']])
        ) {
            $results['modulos'][] = $module;
        }
    }

    try {
        $dsn = sprintf(
            '%s:host=%s;dbname=%s;charset=utf8mb4',
            $config['type'] ?? 'mysql',
            $config['host'] ?? 'localhost',
            $config['name'] ?? ''
        );

        $pdo = new PDO($dsn, $config['user'] ?? '', $config['pass'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $like = '%' . $query . '%';

        $noticias = $pdo->prepare(
            "SELECT id, titulo, resumo, data_publicacao
             FROM noticia
             WHERE titulo LIKE :query AND (status = 'published' OR status = 'Publicado')
             ORDER BY data_publicacao DESC, id DESC
             LIMIT 30"
        );
        $noticias->execute([':query' => $like]);
        $results['noticias'] = $noticias->fetchAll();

        $licitacoes = $pdo->prepare(
            "SELECT id, objeto, modalidade, data_licitacao
             FROM licitacao
             WHERE objeto LIKE :query
             ORDER BY data_licitacao DESC, id DESC
             LIMIT 30"
        );
        $licitacoes->execute([':query' => $like]);
        $results['licitacoes'] = $licitacoes->fetchAll();
    } catch (Throwable $e) {
        $error = 'Nao foi possivel carregar os resultados da busca no momento.';
    }
}

$total = count($results['modulos']) + count($results['noticias']) + count($results['licitacoes']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da busca | Portal da Transparencia</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/default.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <style>
        body {
            background: #f3f7fb;
            color: #062b4f;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
        }

        .transparent-header {
            background: #fff;
            border-bottom: 1px solid #d8e5f0;
            box-shadow: 0 10px 24px rgba(20, 54, 86, 0.06);
            position: relative;
            z-index: 10;
        }

        #header-fixed-height {
            display: none;
        }

        .tg-header__area {
            background: #fff;
        }

        .custom-container,
        .search-page-hero .container,
        .search-results-wrap .container {
            max-width: 1120px;
        }

        .tgmenu__wrap {
            min-height: 92px;
            display: flex;
            align-items: center;
        }

        .tgmenu__nav {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
        }

        .logo {
            flex: 0 0 auto;
        }

        .logo a {
            display: inline-flex;
            align-items: center;
        }

        .logo img {
            width: auto;
            max-width: 190px;
            max-height: 66px;
            display: block;
        }

        .tgmenu__navbar-wrap {
            flex: 1 1 auto;
            justify-content: flex-end;
        }

        .navigation {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 6px 18px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .navigation li {
            margin: 0;
            padding: 0;
        }

        .navigation a {
            color: #173d5f;
            display: block;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.2;
            padding: 12px 0;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .navigation a:hover {
            color: #0878c9;
        }

        .search-page-hero {
            background:
                linear-gradient(135deg, rgba(9, 72, 119, 0.94) 0%, rgba(8, 120, 201, 0.92) 100%),
                url("assets/img/slider/slider_bg01.jpg") center/cover no-repeat;
            color: #fff;
            padding: 56px 0 52px;
        }

        .search-page-hero h1 {
            color: #fff;
            font-size: 36px;
            line-height: 1.15;
            margin-bottom: 10px;
        }

        .search-page-hero p {
            color: rgba(255, 255, 255, 0.92);
            margin: 0;
            max-width: 620px;
        }

        .search-results-wrap {
            padding: 36px 0 70px;
        }

        .search-panel {
            background: #fff;
            border: 1px solid #d8e5f0;
            border-radius: 8px;
            padding: 22px;
            box-shadow: 0 16px 35px rgba(20, 54, 86, 0.08);
            margin-bottom: 24px;
        }

        .search-form-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
        }

        .search-form-row input {
            min-height: 48px;
            border: 1px solid #c8d8e8;
            border-radius: 6px;
            padding: 0 14px;
            color: #062b4f;
        }

        .search-form-row button {
            min-height: 48px;
            border: 0;
            border-radius: 6px;
            background: #0878c9;
            color: #fff;
            font-weight: 700;
            padding: 0 22px;
        }

        .result-section {
            margin-top: 26px;
        }

        .result-section h2 {
            font-size: 21px;
            margin-bottom: 14px;
        }

        .result-card {
            display: block;
            background: #fff;
            border: 1px solid #d8e5f0;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 12px;
            color: #062b4f;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .result-card:hover {
            color: #062b4f;
            border-color: #0878c9;
            box-shadow: 0 12px 26px rgba(20, 54, 86, 0.1);
        }

        .result-card__type {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #0878c9;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .result-card h3 {
            font-size: 18px;
            margin: 0 0 8px;
        }

        .result-card p {
            color: #55708a;
            margin: 0;
        }

        .result-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            color: #55708a;
            font-size: 13px;
            margin-top: 10px;
        }

        .empty-results {
            background: #fff;
            border: 1px dashed #b7cde3;
            border-radius: 8px;
            padding: 22px;
            color: #33536f;
        }

        @media (max-width: 575px) {
            .tgmenu__wrap {
                min-height: auto;
                padding: 18px 0;
            }

            .tgmenu__nav {
                align-items: flex-start;
                flex-direction: column;
                gap: 14px;
            }

            .logo img {
                max-width: 160px;
                max-height: 56px;
            }

            .navigation {
                justify-content: flex-start;
                gap: 0 14px;
            }

            .navigation a {
                font-size: 13px;
                padding: 6px 0;
            }

            .search-form-row {
                grid-template-columns: 1fr;
            }

            .search-page-hero h1 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <header class="transparent-header">
        <div id="header-fixed-height"></div>
        <div id="sticky-header" class="tg-header__area">
            <div class="container custom-container">
                <div class="row">
                    <div class="col-12">
                        <div class="tgmenu__wrap">
                            <nav class="tgmenu__nav">
                                <div class="logo">
                                    <a href="index.html"><img src="assets/img/logo/logo.png" alt="Logo"></a>
                                </div>
                                <div class="tgmenu__navbar-wrap tgmenu__main-menu d-none d-lg-flex">
                                    <ul class="navigation">
                                        <li><a href="index.html">Inicio</a></li>
                                        <li><a href="sites-municipais.html">Sites Municipais</a></li>
                                        <li><a href="portal-transparencia.html">Portal Transparencia</a></li>
                                        <li><a href="servicos.html">Servicos</a></li>
                                        <li><a href="galeria.html">Galeria</a></li>
                                        <li><a href="blog.html">Noticias</a></li>
                                        <li><a href="contato.html">Fale Conosco</a></li>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="search-page-hero">
            <div class="container">
                <h1>Resultados da busca</h1>
                <p>Consulta integrada em modulos, noticias e licitacoes cadastradas no portal.</p>
            </div>
        </section>

        <section class="search-results-wrap">
            <div class="container">
                <div class="search-panel">
                    <form action="formulario.php" method="get" class="search-form-row">
                        <input type="text" name="q" value="<?php echo h($query); ?>" placeholder="Digite o termo da consulta" aria-label="Termo de busca">
                        <button type="submit"><i class="fas fa-search"></i> Buscar</button>
                    </form>
                </div>

                <?php if ($query === ''): ?>
                    <div class="empty-results">Digite um termo para realizar a busca no portal.</div>
                <?php elseif ($error !== ''): ?>
                    <div class="empty-results"><?php echo h($error); ?></div>
                <?php elseif ($total === 0): ?>
                    <div class="empty-results">Nenhum resultado encontrado para "<?php echo h($query); ?>".</div>
                <?php else: ?>
                    <div class="result-meta">
                        <span><?php echo (int) $total; ?> resultado(s) encontrado(s)</span>
                        <span>Termo pesquisado: <?php echo h($query); ?></span>
                    </div>

                    <div class="result-section">
                        <h2>Modulos</h2>
                        <?php if (empty($results['modulos'])): ?>
                            <div class="empty-results">Nenhum modulo encontrado.</div>
                        <?php endif; ?>
                        <?php foreach ($results['modulos'] as $item): ?>
                            <a class="result-card" href="<?php echo h($item['url']); ?>">
                                <span class="result-card__type"><i class="fas fa-th-large"></i> Modulo</span>
                                <h3><?php echo h($item['title']); ?></h3>
                                <?php if (!empty($item['description'])): ?>
                                    <p><?php echo h($item['description']); ?></p>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <div class="result-section">
                        <h2>Noticias</h2>
                        <?php if (empty($results['noticias'])): ?>
                            <div class="empty-results">Nenhuma noticia encontrada.</div>
                        <?php endif; ?>
                        <?php foreach ($results['noticias'] as $item): ?>
                            <a class="result-card" href="blog-details.html?id=<?php echo (int) $item['id']; ?>">
                                <span class="result-card__type"><i class="fas fa-newspaper"></i> Noticia</span>
                                <h3><?php echo h($item['titulo']); ?></h3>
                                <?php if (!empty($item['resumo'])): ?>
                                    <p><?php echo h($item['resumo']); ?></p>
                                <?php endif; ?>
                                <span class="result-meta">
                                    <?php if (!empty($item['data_publicacao'])): ?>
                                        <span><i class="far fa-calendar-alt"></i> <?php echo h(formatDateBr($item['data_publicacao'])); ?></span>
                                    <?php endif; ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <div class="result-section">
                        <h2>Licitacoes</h2>
                        <?php if (empty($results['licitacoes'])): ?>
                            <div class="empty-results">Nenhuma licitacao encontrada.</div>
                        <?php endif; ?>
                        <?php foreach ($results['licitacoes'] as $item): ?>
                            <a class="result-card" href="licitacoes-consulta.html?keyword=<?php echo rawurlencode($item['objeto']); ?>">
                                <span class="result-card__type"><i class="fas fa-file-contract"></i> Licitacao</span>
                                <h3><?php echo h($item['objeto']); ?></h3>
                                <span class="result-meta">
                                    <?php if (!empty($item['modalidade'])): ?>
                                        <span><i class="fas fa-tag"></i> <?php echo h($item['modalidade']); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($item['data_licitacao'])): ?>
                                        <span><i class="far fa-calendar-alt"></i> <?php echo h(formatDateBr($item['data_licitacao'])); ?></span>
                                    <?php endif; ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
