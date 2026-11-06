<?php
if (!isset($pdo)) {
    require_once __DIR__ . '/../bd.php';
}

$page_title = $page_title ?? 'Catálogo Fantástico - Universos & Cronologias Cinematográficas';
$usuario_logado = obter_usuario();
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <meta name="description" content="Catálogo Fantástico: Mapeie onde assistir os filmes das suas franquias favoritas em ordem cronológica ou de lançamento. Estilo Quarteto Fantástico.">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2245%22 fill=%22%230284c7%22 stroke=%22%2338bdf8%22 stroke-width=%225%22/><text x=%2250%22 y=%2265%22 font-family=%22sans-serif%22 font-size=%2250%22 font-weight=%22900%22 fill=%22%23ffffff%22 text-anchor=%22middle%22>4</text></svg>">
    
    <!-- Google Fonts: Orbitron, Rajdhani & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN com tema Quarteto Fantástico -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        f4: {
                            blue: '#0ea5e9',
                            sky: '#38bdf8',
                            bluedark: '#0369a1',
                            orange: '#f97316',
                            fire: '#ea580c',
                            amber: '#fb923c',
                            dark: '#070c18',
                            card: '#0d172e',
                            cardhover: '#132142'
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        fantastic: ['"Orbitron"', '"Rajdhani"', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Customizado -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-[#070c18] text-slate-100 min-h-screen flex flex-col antialiased selection:bg-orange-500 selection:text-white">
