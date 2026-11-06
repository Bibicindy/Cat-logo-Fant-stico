<?php
/**
 * Inicialização e Seed do Banco de Dados - Catálogo Fantástico
 * Estilo Quarteto Fantástico com Franquias e Filmes Completos
 */

function inicializar_banco_se_necessario($pdo, $driver = 'mysql') {
    if ($driver === 'mysql') {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                usuario VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(150) NULL UNIQUE,
                telefone VARCHAR(30) NULL,
                senha VARCHAR(255) NOT NULL,
                papel VARCHAR(20) DEFAULT 'usuario',
                consentimento_dados TINYINT DEFAULT 1,
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS franquias (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(120) NOT NULL,
                slug VARCHAR(120) NOT NULL UNIQUE,
                genero VARCHAR(60) NOT NULL DEFAULT 'Super-Heróis',
                descricao TEXT NULL,
                banner_url VARCHAR(500) NULL,
                icone_url VARCHAR(500) NULL,
                criado_por INT NULL,
                visibilidade VARCHAR(20) DEFAULT 'publica',
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS filmes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                franquia_id INT NOT NULL,
                titulo VARCHAR(200) NOT NULL,
                ano_lancamento INT NOT NULL,
                ordem_cronologica INT NOT NULL,
                ordem_lancamento INT NOT NULL,
                streaming VARCHAR(255) NOT NULL,
                streaming_url VARCHAR(500) NULL,
                duracao VARCHAR(30) NULL,
                classificacao_etaria VARCHAR(20) DEFAULT '12',
                sinopse TEXT NULL,
                poster_url VARCHAR(500) NULL,
                criado_por INT NULL,
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (franquia_id) REFERENCES franquias(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS progresso_usuario (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                filme_id INT NOT NULL,
                assistido TINYINT DEFAULT 1,
                atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_usuario_filme (usuario_id, filme_id),
                FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS ordem_personalizada (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                franquia_id INT NOT NULL,
                filme_id INT NOT NULL,
                posicao INT NOT NULL,
                UNIQUE KEY uk_usuario_franquia_filme (usuario_id, franquia_id, filme_id),
                FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS permissoes_listas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                dono_id INT NOT NULL,
                franquia_id INT NOT NULL,
                tipo_acesso VARCHAR(20) DEFAULT 'publico',
                pode_editar TINYINT DEFAULT 0,
                pode_excluir TINYINT DEFAULT 0,
                pode_administrar TINYINT DEFAULT 0,
                codigo_convite VARCHAR(64) UNIQUE,
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS logs_auditoria (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NULL,
                usuario_nome VARCHAR(100) NOT NULL,
                acao VARCHAR(80) NOT NULL,
                detalhes TEXT NOT NULL,
                ip VARCHAR(45) NOT NULL,
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS avaliacoes_feedbacks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NULL,
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(150) NULL,
                nota TINYINT NOT NULL DEFAULT 5,
                tipo VARCHAR(20) DEFAULT 'sugestao',
                mensagem TEXT NOT NULL,
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    } else {
        // SQLite
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL,
                usuario TEXT NOT NULL UNIQUE,
                email TEXT UNIQUE,
                telefone TEXT,
                senha TEXT NOT NULL,
                papel TEXT DEFAULT 'usuario',
                consentimento_dados INTEGER DEFAULT 1,
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS franquias (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                genero TEXT NOT NULL DEFAULT 'Super-Heróis',
                descricao TEXT,
                banner_url TEXT,
                icone_url TEXT,
                criado_por INTEGER,
                visibilidade TEXT DEFAULT 'publica',
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS filmes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                franquia_id INTEGER NOT NULL,
                titulo TEXT NOT NULL,
                ano_lancamento INTEGER NOT NULL,
                ordem_cronologica INTEGER NOT NULL,
                ordem_lancamento INTEGER NOT NULL,
                streaming TEXT NOT NULL,
                streaming_url TEXT,
                duracao TEXT,
                classificacao_etaria TEXT DEFAULT '12',
                sinopse TEXT,
                poster_url TEXT,
                criado_por INTEGER,
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (franquia_id) REFERENCES franquias(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS progresso_usuario (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER NOT NULL,
                filme_id INTEGER NOT NULL,
                assistido INTEGER DEFAULT 1,
                atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(usuario_id, filme_id),
                FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS ordem_personalizada (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER NOT NULL,
                franquia_id INTEGER NOT NULL,
                filme_id INTEGER NOT NULL,
                posicao INTEGER NOT NULL,
                UNIQUE(usuario_id, franquia_id, filme_id),
                FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS permissoes_listas (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                dono_id INTEGER NOT NULL,
                franquia_id INTEGER NOT NULL,
                tipo_acesso TEXT DEFAULT 'publico',
                pode_editar INTEGER DEFAULT 0,
                pode_excluir INTEGER DEFAULT 0,
                pode_administrar INTEGER DEFAULT 0,
                codigo_convite TEXT UNIQUE,
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS logs_auditoria (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER,
                usuario_nome TEXT NOT NULL,
                acao TEXT NOT NULL,
                detalhes TEXT NOT NULL,
                ip TEXT NOT NULL,
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS avaliacoes_feedbacks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER,
                nome TEXT NOT NULL,
                email TEXT,
                nota INTEGER NOT NULL DEFAULT 5,
                tipo TEXT DEFAULT 'sugestao',
                mensagem TEXT NOT NULL,
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    // Verificar se já temos a franquia do Quarteto Fantástico; se não, limpamos para repovoar completo
    $temF4 = $pdo->query("SELECT id FROM franquias WHERE slug = 'quarteto-fantastico'")->fetch();
    if ($temF4) {
        return; // Já populado com a nova base
    }

    // Limpar para atualização das novas franquias ricas e posters originais
    $pdo->exec("DELETE FROM filmes; DELETE FROM franquias; DELETE FROM usuarios;");

    // Criar Usuário Admin inicial (senha bcrypt)
    $senhaHash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmtUser = $pdo->prepare("INSERT INTO usuarios (nome, usuario, email, telefone, senha, papel, criado_em) VALUES (:nome, :usuario, :email, :telefone, :senha, :papel, :criado_em)");
    $stmtUser->execute([
        ':nome' => 'Reed Richards (Admin Fantástico)',
        ':usuario' => 'admin',
        ':email' => 'admin@catalogofantastico.com',
        ':telefone' => '(11) 98765-4321',
        ':senha' => $senhaHash,
        ':papel' => 'admin',
        ':criado_em' => date('Y-m-d H:i:s')
    ]);
    $adminId = $pdo->lastInsertId();

    // Catálogo Completo de Franquias e Filmes
    $franquias = [
        [
            'nome' => 'Quarteto Fantástico (Fantastic Four)',
            'slug' => 'quarteto-fantastico',
            'genero' => 'Super-Heróis',
            'descricao' => 'A Primeira Família da Marvel. Reed Richards (Sr. Fantástico), Sue Storm (Mulher Invisível), Johnny Storm (Tocha Humana) e Ben Grimm (O Coisa) enfrentam ameaças cósmicas, o Doutor Destino e o temível Devorador de Mundos, Galactus.',
            'banner_url' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=1600&auto=format&fit=crop&q=80',
            'icone_url' => 'https://cdn-icons-png.flaticon.com/512/1077/1077063.png',
            'filmes' => [
                [
                    'titulo' => 'Quarteto Fantástico',
                    'ano_lancamento' => 2005,
                    'ordem_cronologica' => 1,
                    'ordem_lancamento' => 1,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Quarteto+Fantastico',
                    'duracao' => '1h 46m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Durante uma expedição científica no espaço, quatro cientistas são expostos a uma tempestade de radiação cósmica que altera seus DNAs, concedendo-lhes incríveis superpoderes para combater o sinistro Victor Von Doom.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMjA5NTg3NTUzMF5BMl5BanBnXkFtZTcwNDg4MDUzMw@@._V1_.jpg'
                ],
                [
                    'titulo' => 'Quarteto Fantástico e o Surfista Prateado',
                    'ano_lancamento' => 2007,
                    'ordem_cronologica' => 2,
                    'ordem_lancamento' => 2,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Quarteto+Fantastico+e+o+Surfista+Prateado',
                    'duracao' => '1h 32m',
                    'classificacao_etaria' => '10',
                    'sinopse' => 'O Quarteto Fantástico descobre que não são os únicos seres com poderes no cosmo quando encontram o enigmático Surfista Prateado e precisam deter o devorador de planetas Galactus antes da destruição da Terra.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTY3NTg2NjQxNF5BMl5BanBnXkFtZTcwODg4MDUzMw@@._V1_.jpg'
                ],
                [
                    'titulo' => 'Quarteto Fantástico (Reboot)',
                    'ano_lancamento' => 2015,
                    'ordem_cronologica' => 3,
                    'ordem_lancamento' => 3,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Quarteto+Fantastico+2015',
                    'duracao' => '1h 40m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Quatro jovens desajustados são teletransportados para um universo alternativo perigoso, o que altera sua forma física de maneiras chocantes. Eles devem aprender a controlar suas novas habilidades para salvar o planeta.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTk0NTMwODcxMl5BMl5BanBnXkFtZTgwNzYwMzU2NTE@._V1_.jpg'
                ],
                [
                    'titulo' => 'Quarteto Fantástico: Primeiros Passos (MCU)',
                    'ano_lancamento' => 2025,
                    'ordem_cronologica' => 4,
                    'ordem_lancamento' => 4,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Fantastic+Four',
                    'duracao' => '2h 15m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Ambientado em uma vibrante década de 1960 em estilo retro-futurista cósmico, Reed Richards, Sue Storm, Johnny Storm e Ben Grimm se unem para enfrentar Galactus e o Surfista Prateado na Fase 6 do Universo Cinematográfico Marvel.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BODg0ZjY3YmItNzY4Yy00MTZlLWE2OWUtYTBiYmEyYWIzMWM3XkEyXkFqcGc@._V1_.jpg'
                ]
            ]
        ],
        [
            'nome' => 'Universo Cinematográfico Marvel (MCU)',
            'slug' => 'marvel-cinematic-universe',
            'genero' => 'Super-Heróis',
            'descricao' => 'A maior saga interconectada do cinema mundial produzida pela Marvel Studios. Acompanhe a trajetória dos Vingadores, Guardiões da Galáxia, Joias do Infinito e a Saga do Multiverso.',
            'banner_url' => 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=1600&auto=format&fit=crop&q=80',
            'icone_url' => 'https://cdn-icons-png.flaticon.com/512/889/889192.png',
            'filmes' => [
                [
                    'titulo' => 'Capitão América: O Primeiro Vingador',
                    'ano_lancamento' => 2011,
                    'ordem_cronologica' => 1,
                    'ordem_lancamento' => 5,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Capitao+America+O+Primeiro+Vingador',
                    'duracao' => '2h 04m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Durante a Segunda Guerra Mundial, o franzino Steve Rogers se voluntaria para uma experiência secreta que o transforma no super soldado Capitão América para lutar contra o Caveira Vermelha e a HYDRA.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTYzOTc2NzU3N15BMl5BanBnXkFtZTcwMzY5MzAwNA@@._V1_.jpg'
                ],
                [
                    'titulo' => 'Capitã Marvel',
                    'ano_lancamento' => 2019,
                    'ordem_cronologica' => 2,
                    'ordem_lancamento' => 21,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Capita+Marvel',
                    'duracao' => '2h 04m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Nos anos 1990, Carol Danvers se torna uma das heroínas mais poderosas da galáxia quando a Terra é apanhada no meio de uma guerra intergaláctica entre os nobres heróis Kree e os metamorfos Skrulls.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTE0YWFmOTMtYTU2ZS00ZmMxLTgyNzUtNWU4NWQzNzgxY2Q5XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg'
                ],
                [
                    'titulo' => 'Homem de Ferro',
                    'ano_lancamento' => 2008,
                    'ordem_cronologica' => 3,
                    'ordem_lancamento' => 1,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Homem+de+Ferro',
                    'duracao' => '2h 06m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Após ser sequestrado por terroristas, o magnata e inventor Tony Stark constrói uma armadura de alta tecnologia para escapar e decide usá-la para proteger o mundo.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTczNTI2ODUwOF5BMl5BanBnXkFtZTcwMTU0NTIzMw@@._V1_.jpg'
                ],
                [
                    'titulo' => 'Homem de Ferro 2',
                    'ano_lancamento' => 2010,
                    'ordem_cronologica' => 4,
                    'ordem_lancamento' => 3,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Homem+de+Ferro+2',
                    'duracao' => '2h 04m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Com sua identidade secreta revelada, Tony Stark lida com a pressão do governo, problemas de saúde causados pelo reator em seu peito e a vingança mortal de Ivan Vanko.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTM0MDgwNjMyMl5BMl5BanBnXkFtZTcwNTg3NzAzMw@@._V1_.jpg'
                ],
                [
                    'titulo' => 'Thor',
                    'ano_lancamento' => 2011,
                    'ordem_cronologica' => 5,
                    'ordem_lancamento' => 4,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Thor',
                    'duracao' => '1h 55m',
                    'classificacao_etaria' => '10',
                    'sinopse' => 'O arrogante deus do trovão Thor reacende uma antiga guerra e é despojado de seus poderes por Odin, sendo exilado na Terra para aprender o valor da humildade.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BOGE4NzU1YTAtNzA3Mi00ZTA2LTg2YmYtMDJmMThiMjlkYjg2XkEyXkFqcGdeQXVyNTgzMDMzMTg@._V1_.jpg'
                ],
                [
                    'titulo' => 'Os Vingadores',
                    'ano_lancamento' => 2012,
                    'ordem_cronologica' => 6,
                    'ordem_lancamento' => 6,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Os+Vingadores',
                    'duracao' => '2h 23m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Nick Fury convoca os heróis mais poderosos da Terra para deter Loki e seu exército alienígena Chitauri de subjugar a humanidade em Nova York.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BNDYxNjQyMjAtNTdiOS00NGYwLWFmNTAtNThmYjU5ZGI2YTI1XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg'
                ],
                [
                    'titulo' => 'Homem de Ferro 3',
                    'ano_lancamento' => 2013,
                    'ordem_cronologica' => 7,
                    'ordem_lancamento' => 7,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Homem+de+Ferro+3',
                    'duracao' => '2h 10m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Abalado pelos eventos de Nova York, Tony Stark precisa se reinventar após ter seu mundo pessoal destruído pelo impiedoso terrorista Mandarim.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMjE5MzcyNjk1M15BMl5BanBnXkFtZTcwMjQ4MjcxOQ@@._V1_.jpg'
                ],
                [
                    'titulo' => 'Capitão América: O Soldado Invernal',
                    'ano_lancamento' => 2014,
                    'ordem_cronologica' => 8,
                    'ordem_lancamento' => 9,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Capitao+America+O+Soldado+Invernal',
                    'duracao' => '2h 16m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Steve Rogers e a Viúva Negra desvendam uma perigosa conspiração instalada dentro da S.H.I.E.L.D., enquanto enfrentam um misterioso assassino vindo do passado.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMzA2NDkwODAwM15BMl5BanBnXkFtZTgwODgyODMzMTE@._V1_.jpg'
                ],
                [
                    'titulo' => 'Guardiões da Galáxia',
                    'ano_lancamento' => 2014,
                    'ordem_cronologica' => 9,
                    'ordem_lancamento' => 10,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Guardioes+da+Galaxia',
                    'duracao' => '2h 01m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'O aventureiro espacial Peter Quill se torna alvo de caçadores de recompensas após roubar uma orbe misteriosa e forma uma aliança relutante com um bando de desajustados espaciais.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTAwMDA5NDA1MzheQTJeQWpwZ15BbWU4MDkyNjUxMDE@._V1_.jpg'
                ],
                [
                    'titulo' => 'Vingadores: Era de Ultron',
                    'ano_lancamento' => 2015,
                    'ordem_cronologica' => 10,
                    'ordem_lancamento' => 11,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Vingadores+Era+de+Ultron',
                    'duracao' => '2h 21m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Tony Stark tenta alavancar um programa pacificador adormecido, mas cria Ultron, uma inteligência artificial maligna determinada a extinguir a raça humana.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTM4OGJmNWMtOTM4Ni00NTE3LTg3MDItU3AxNDJhMTg2MTBoXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg'
                ],
                [
                    'titulo' => 'Capitão América: Guerra Civil',
                    'ano_lancamento' => 2016,
                    'ordem_cronologica' => 11,
                    'ordem_lancamento' => 13,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Capitao+America+Guerra+Civil',
                    'duracao' => '2h 27m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'O Tratado de Sokovia divide os Vingadores entre Steve Rogers, que defende liberdade sem amarras governamentais, e Tony Stark, que apoia a supervisão estatal.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMjQ0MTgyNjAxMV5BMl5BanBnXkFtZTgwNjUzMDkyODE@._V1_.jpg'
                ],
                [
                    'titulo' => 'Pantera Negra',
                    'ano_lancamento' => 2018,
                    'ordem_cronologica' => 12,
                    'ordem_lancamento' => 18,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Pantera+Negra',
                    'duracao' => '2h 14m',
                    'classificacao_etaria' => '14',
                    'sinopse' => 'T\'Challa retorna à isolada e tecnologicamente avançada nação de Wakanda para assumir o trono como rei, mas seu direito é desafiado por um adversário ligado ao seu passado familiar.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTg1MTY2MjYzNV5BMl5BanBnXkFtZTgwMTc4NTMwNDI@._V1_.jpg'
                ],
                [
                    'titulo' => 'Vingadores: Guerra Infinita',
                    'ano_lancamento' => 2018,
                    'ordem_cronologica' => 13,
                    'ordem_lancamento' => 19,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Vingadores+Guerra+Infinita',
                    'duracao' => '2h 29m',
                    'classificacao_etaria' => '14',
                    'sinopse' => 'Os Vingadores e os Guardiões da Galáxia unem forças para tentar impedir que Thanos reúna as seis Joias do Infinito e elimine metade de toda a vida no universo.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMjMxNjY2MDU1OV5BMl5BanBnXkFtZTgwNzY1MTUwNTM@._V1_.jpg'
                ],
                [
                    'titulo' => 'Vingadores: Ultimato',
                    'ano_lancamento' => 2019,
                    'ordem_cronologica' => 14,
                    'ordem_lancamento' => 22,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Vingadores+Ultimato',
                    'duracao' => '3h 01m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Após a devastação de Thanos, os heróis sobreviventes realizam uma arriscada missão no tempo pelo Reino Quântico para trazer de volta os que foram perdidos.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_.jpg'
                ],
                [
                    'titulo' => 'Deadpool & Wolverine',
                    'ano_lancamento' => 2024,
                    'ordem_cronologica' => 15,
                    'ordem_lancamento' => 34,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Deadpool+Wolverine',
                    'duracao' => '2h 08m',
                    'classificacao_etaria' => '18',
                    'sinopse' => 'Wade Wilson leva uma vida tranquila até ser convocado pela Autoridade de Variância Temporal (AVT) e unir forças a um relutante Wolverine para salvar seu universo da extinção.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BNzRiMjg0MzUtNTQ1Mi00Y2Q5LWEwM2MtMzUwZDU5NmVjN2NkXkEyXkFqcGc@._V1_.jpg'
                ]
            ]
        ],
        [
            'nome' => 'Star Wars (Guerra nas Estrelas)',
            'slug' => 'star-wars',
            'genero' => 'Ficção Científica',
            'descricao' => 'Uma galáxia muito, muito distante. Siga os caminhos da Força, dos Cavaleiros Jedi e dos Lordes Sith pela lendária saga Skywalker e histórias paralelas.',
            'banner_url' => 'https://images.unsplash.com/photo-1579566346927-c68383817a25?w=1600&auto=format&fit=crop&q=80',
            'icone_url' => 'https://cdn-icons-png.flaticon.com/512/1077/1077063.png',
            'filmes' => [
                [
                    'titulo' => 'Star Wars: Ep I - A Ameaça Fantasma',
                    'ano_lancamento' => 1999,
                    'ordem_cronologica' => 1,
                    'ordem_lancamento' => 4,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=A+Ameaca+Fantasma',
                    'duracao' => '2h 16m',
                    'classificacao_etaria' => 'Livre',
                    'sinopse' => 'Dois Cavaleiros Jedi descobrem o jovem escravo Anakin Skywalker em Tatooine, enquanto protegem a Rainha Amidala de Naboo da invasão da Federação do Comércio.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BYTRhNjcwNWQtMGJmMi00NmQyLWE2YzItODVmMTdjNWI0ZDA2XkEyXkFqcGdeQXVyNTAyODkwOQ@@._V1_.jpg'
                ],
                [
                    'titulo' => 'Star Wars: Ep II - Ataque dos Clones',
                    'ano_lancamento' => 2002,
                    'ordem_cronologica' => 2,
                    'ordem_lancamento' => 5,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Ataque+dos+Clones',
                    'duracao' => '2h 22m',
                    'classificacao_etaria' => '10',
                    'sinopse' => 'Dez anos depois, Anakin se apaixona proibitivamente por Padmé Amidala enquanto Obi-Wan Kenobi descobre a criação secreta de um exército de clones.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMDAzM2M0Y2UtZjRmZi00MzVlLTg4MjEtOTE3NzU5ZDVlMTU5XkEyXkFqcGdeQXVyNDUyOTg3Njg@._V1_.jpg'
                ],
                [
                    'titulo' => 'Star Wars: Ep III - A Vingança dos Sith',
                    'ano_lancamento' => 2005,
                    'ordem_cronologica' => 3,
                    'ordem_lancamento' => 6,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=A+Vinganca+dos+Sith',
                    'duracao' => '2h 20m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'As Guerras Clônicas atingem o auge. Seduzido por promessas de poder para salvar sua esposa, Anakin se volta para o Lado Sombrio e se torna Darth Vader.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BNTc4MTc3NTQ5OF5BMl5BanBnXkFtZTcwOTg0NjI4NA@@._V1_.jpg'
                ],
                [
                    'titulo' => 'Han Solo: Uma História Star Wars',
                    'ano_lancamento' => 2018,
                    'ordem_cronologica' => 4,
                    'ordem_lancamento' => 10,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Han+Solo',
                    'duracao' => '2h 15m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Durante uma série de ousadas escapadas no submundo do crime galáctico, Han Solo conhece seu futuro copiloto Chewbacca e o apostador Lando Calrissian.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BOTM2NTI3NTc3Nl5BMl5BanBnXkFtZTgwNzM1OTQyNTM@._V1_.jpg'
                ],
                [
                    'titulo' => 'Rogue One: Uma História Star Wars',
                    'ano_lancamento' => 2016,
                    'ordem_cronologica' => 5,
                    'ordem_lancamento' => 8,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Rogue+One',
                    'duracao' => '2h 13m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Um corajoso esquadrão rebelde liderado por Jyn Erso executa uma perigosa missão suicida para roubar os esquemas da temida Estrela da Morte.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMjEwMzMxODIzOV5BMl5BanBnXkFtZTgwNzg3OTAzMDI@._V1_.jpg'
                ],
                [
                    'titulo' => 'Star Wars: Ep IV - Uma Nova Esperança',
                    'ano_lancamento' => 1977,
                    'ordem_cronologica' => 6,
                    'ordem_lancamento' => 1,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Uma+Nova+Esperanca',
                    'duracao' => '2h 01m',
                    'classificacao_etaria' => 'Livre',
                    'sinopse' => 'Luke Skywalker une forças com Obi-Wan Kenobi, Han Solo e a Princesa Leia para salvar a galáxia da maior arma de destruição em massa do Império.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BOTA5NjhiOTAtZWM0ZC00MWNhLThiMzEtZDFkOTk2OTU1ZDJkXkEyXkFqcGdeQXVyMTA4NDI1NTQx._V1_.jpg'
                ],
                [
                    'titulo' => 'Star Wars: Ep V - O Império Contra-Ataca',
                    'ano_lancamento' => 1980,
                    'ordem_cronologica' => 7,
                    'ordem_lancamento' => 2,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=O+Imperio+Contra-Ataca',
                    'duracao' => '2h 04m',
                    'classificacao_etaria' => 'Livre',
                    'sinopse' => 'Enquanto os Rebeldes fogem das forças imperiais, Luke viaja ao pântano de Dagobah para treinar com o Grão-Mestre Yoda e descobre a terrível verdade sobre seu pai.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BYmU1NDRjNDgtMzhiMi00NjZmLTg5NGItZDNiGDU5NTFiYzQ1XkEyXkFqcGdeQXVyNTc1NTQxODI@._V1_.jpg'
                ],
                [
                    'titulo' => 'Star Wars: Ep VI - O Retorno de Jedi',
                    'ano_lancamento' => 1983,
                    'ordem_cronologica' => 8,
                    'ordem_lancamento' => 3,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=O+Retorno+de+Jedi',
                    'duracao' => '2h 11m',
                    'classificacao_etaria' => 'Livre',
                    'sinopse' => 'Luke Skywalker tenta resgatar seu pai Darth Vader de volta à luz enquanto a frota rebelde lança um ataque definitivo contra a segunda Estrela da Morte e o Imperador.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BOWZlMjFiYzgtMTUzNC00Y2IzLTk1NTMtZmNhMTczNTk0ODk1XkEyXkFqcGdeQXVyNTAyODkwOQ@@._V1_.jpg'
                ],
                [
                    'titulo' => 'Star Wars: Ep VII - O Despertar da Força',
                    'ano_lancamento' => 2015,
                    'ordem_cronologica' => 9,
                    'ordem_lancamento' => 7,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=O+Despertar+da+Forca',
                    'duracao' => '2h 18m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Trinta anos após a derrota do Império, a sucateira Rey e o desertor Finn se unem a Han Solo para combater a temível Primeira Ordem liderada por Kylo Ren.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BOTAzODEzNDAzMl5BMl5BanBnXkFtZTgwMDU1MTgzNzE@._V1_.jpg'
                ],
                [
                    'titulo' => 'Star Wars: Ep VIII - Os Últimos Jedi',
                    'ano_lancamento' => 2017,
                    'ordem_cronologica' => 10,
                    'ordem_lancamento' => 9,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=Os+Ultimos+Jedi',
                    'duracao' => '2h 32m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Rey busca os ensinamentos do lendário e recluso Luke Skywalker em Ahch-To, enquanto a General Leia comanda a Resistência encurralada.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMjQ1MzcxNjg4N15BMl5BanBnXkFtZTgwNzgwMjY4MzI@._V1_.jpg'
                ],
                [
                    'titulo' => 'Star Wars: Ep IX - A Ascensão Skywalker',
                    'ano_lancamento' => 2019,
                    'ordem_cronologica' => 11,
                    'ordem_lancamento' => 11,
                    'streaming' => 'Disney+',
                    'streaming_url' => 'https://www.disneyplus.com/pt-br/search?q=A+Ascensao+Skywalker',
                    'duracao' => '2h 22m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Com o retorno espectral do Imperador Palpatine das trevas, Rey e Kylo Ren travam a batalha conclusiva pelo destino de toda a galáxia.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMDljNTQ5ODItZmQwMy00M2ExLTljOTQtZTVjNGE2NTg0NndmXkEyXkFqcGdeQXVyNDg4NjY5OTQ@._V1_.jpg'
                ]
            ]
        ],
        [
            'nome' => 'Mundo Bruxo (Harry Potter & Animais Fantásticos)',
            'slug' => 'wizarding-world',
            'genero' => 'Fantasia',
            'descricao' => 'Mergulhe no castelo de Hogwarts, nos feitiços de Harry Potter e na era de Newt Scamander na luta mágica contra Grindelwald e Lord Voldemort.',
            'banner_url' => 'https://images.unsplash.com/photo-1547756536-cde3673fa2e5?w=1600&auto=format&fit=crop&q=80',
            'icone_url' => 'https://cdn-icons-png.flaticon.com/512/3233/3233483.png',
            'filmes' => [
                [
                    'titulo' => 'Animais Fantásticos e Onde Habitam',
                    'ano_lancamento' => 2016,
                    'ordem_cronologica' => 1,
                    'ordem_lancamento' => 9,
                    'streaming' => 'Max',
                    'streaming_url' => 'https://www.max.com/search?q=Animais+Fantasticos',
                    'duracao' => '2h 13m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'O magizoologista Newt Scamander chega a Nova York nos anos 1920 com sua mala cheia de criaturas mágicas extraordinárias que acidentalmente escapam.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMjMxOTE2PTEzN15BMl5BanBnXkFtZTgwNzY5OTQ5OTE@._V1_.jpg'
                ],
                [
                    'titulo' => 'Animais Fantásticos: Os Crimes de Grindelwald',
                    'ano_lancamento' => 2018,
                    'ordem_cronologica' => 2,
                    'ordem_lancamento' => 10,
                    'streaming' => 'Max',
                    'streaming_url' => 'https://www.max.com/search?q=Os+Crimes+de+Grindelwald',
                    'duracao' => '2h 14m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'O poderoso mago das trevas Gellert Grindelwald escapa da custódia e reúne seguidores em Paris para instaurar a supremacia dos bruxos puro-sangue.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BYWVlMDI5N2UtZTIyMC00NjZkLWI5YGtudGNmNjM2NmFjNDBmXkEyXkFqcGdeQXVyNzU1NzE3NTg@._V1_.jpg'
                ],
                [
                    'titulo' => 'Harry Potter e a Pedra Filosofal',
                    'ano_lancamento' => 2001,
                    'ordem_cronologica' => 3,
                    'ordem_lancamento' => 1,
                    'streaming' => 'Max',
                    'streaming_url' => 'https://www.max.com/search?q=Harry+Potter+e+a+Pedra+Filosofal',
                    'duracao' => '2h 32m',
                    'classificacao_etaria' => 'Livre',
                    'sinopse' => 'No seu 11º aniversário, o órfão Harry Potter descobre sua herança bruxa e ingressa na Escola de Magia e Bruxaria de Hogwarts.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BNmQ0ODBhMjUtNDRhOC00MGQzLTgzOTUtZGJkMWIwYTFmNjAyXkEyXkFqcGdeQXVyNDUyOTg3Njg@._V1_.jpg'
                ],
                [
                    'titulo' => 'Harry Potter e o Prisioneiro de Azkaban',
                    'ano_lancamento' => 2004,
                    'ordem_cronologica' => 4,
                    'ordem_lancamento' => 3,
                    'streaming' => 'Max',
                    'streaming_url' => 'https://www.max.com/search?q=Harry+Potter+e+o+Prisioneiro+de+Azkaban',
                    'duracao' => '2h 22m',
                    'classificacao_etaria' => 'Livre',
                    'sinopse' => 'Em seu terceiro ano em Hogwarts, Harry enfrenta os terríveis Dementadores enquanto o infame fugitivo Sirius Black ronda as cercanias do castelo.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTY4NTQxNzM1N15BMl5BanBnXkFtZTcwOTQyNDYxMw@@._V1_.jpg'
                ],
                [
                    'titulo' => 'Harry Potter e as Relíquias da Morte - Parte 2',
                    'ano_lancamento' => 2011,
                    'ordem_cronologica' => 5,
                    'ordem_lancamento' => 8,
                    'streaming' => 'Max',
                    'streaming_url' => 'https://www.max.com/search?q=Harry+Potter+e+as+Reliquias+da+Morte+Parte+2',
                    'duracao' => '2h 10m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Harry, Rony e Hermione destroem as Horcruxes remanescentes de Voldemort enquanto o castelo de Hogwarts se torna o campo da batalha mágica definitiva.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMGVmMWNiMDktYjQ0Mi00MWIxLTk0N2UtN2ZlYTdkN2IzNDNlXkEyXkFqcGdeQXVyODE5NzE3OTE@._V1_.jpg'
                ]
            ]
        ],
        [
            'nome' => 'Terra Média (O Senhor dos Anéis & O Hobbit)',
            'slug' => 'middle-earth',
            'genero' => 'Fantasia',
            'descricao' => 'A obra-prima épica de J.R.R. Tolkien. Da inesperada jornada de Bilbo Bolseiro até o destino supremo do Um Anel na Montanha da Perdição.',
            'banner_url' => 'https://images.unsplash.com/photo-1506466010722-395aa2bef877?w=1600&auto=format&fit=crop&q=80',
            'icone_url' => 'https://cdn-icons-png.flaticon.com/512/3655/3655581.png',
            'filmes' => [
                [
                    'titulo' => 'O Hobbit: Uma Jornada Inesperada',
                    'ano_lancamento' => 2012,
                    'ordem_cronologica' => 1,
                    'ordem_lancamento' => 4,
                    'streaming' => 'Max / Prime Video',
                    'streaming_url' => 'https://www.primevideo.com/search/ref=atv_nb_sr?phrase=O+Hobbit',
                    'duracao' => '2h 49m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Bilbo Bolseiro é convencido pelo mago Gandalf a acompanhar uma companhia de 13 anões para recuperar a Montanha Solitária das garras do dragão Smaug.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTcwNTE4MTUxMl5BMl5BanBnXkFtZTcwMDIyODM4OA@@._V1_.jpg'
                ],
                [
                    'titulo' => 'O Senhor dos Anéis: A Sociedade do Anel',
                    'ano_lancamento' => 2001,
                    'ordem_cronologica' => 2,
                    'ordem_lancamento' => 1,
                    'streaming' => 'Max / Prime Video',
                    'streaming_url' => 'https://www.primevideo.com/search/ref=atv_nb_sr?phrase=A+Sociedade+do+Anel',
                    'duracao' => '2h 58m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'O jovem hobbit Frodo Bolseiro herda o Um Anel do Lorde das Trevas Sauron e inicia uma jornada heroica ao lado de elfos, anões, magos e humanos para destruí-lo.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BN2EyZjM3NzUtNWUzMi00MTgxLWI0NTctMzY4M2VlOTdjZWRiXkEyXkFqcGdeQXVyNDUzOTQ5MjY@._V1_.jpg'
                ],
                [
                    'titulo' => 'O Senhor dos Anéis: O Retorno do Rei',
                    'ano_lancamento' => 2003,
                    'ordem_cronologica' => 3,
                    'ordem_lancamento' => 3,
                    'streaming' => 'Max / Prime Video',
                    'streaming_url' => 'https://www.primevideo.com/search/ref=atv_nb_sr?phrase=O+Retorno+do+Rei',
                    'duracao' => '3h 21m',
                    'classificacao_etaria' => '14',
                    'sinopse' => 'Aragorn abraça seu destino real liderando o Mundo dos Homens nos campos de Pelennor, enquanto Frodo e Sam se aproximam das chamas da Montanha da Perdição.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BNzA5ZDNlZWMtM2NhNS00NDJjLTk4NDItYTRmY2EwMWZlMTY3XkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg'
                ]
            ]
        ],
        [
            'nome' => 'Batman & Cavaleiro das Trevas (DC)',
            'slug' => 'batman-dark-knight',
            'genero' => 'Ação',
            'descricao' => 'As lendas do Cavaleiro das Trevas de Gotham City. A aclamada trilogia de Christopher Nolan e a sombria interpretação investigativa de Matt Reeves.',
            'banner_url' => 'https://images.unsplash.com/photo-1509281373149-e957c6296406?w=1600&auto=format&fit=crop&q=80',
            'icone_url' => 'https://cdn-icons-png.flaticon.com/512/1077/1077063.png',
            'filmes' => [
                [
                    'titulo' => 'Batman Begins',
                    'ano_lancamento' => 2005,
                    'ordem_cronologica' => 1,
                    'ordem_lancamento' => 1,
                    'streaming' => 'Max',
                    'streaming_url' => 'https://www.max.com/search?q=Batman+Begins',
                    'duracao' => '2h 20m',
                    'classificacao_etaria' => '12',
                    'sinopse' => 'Após testemunhar a morte dos pais, Bruce Wayne viaja pelo Oriente aprendendo artes marciais com a Liga das Sombras antes de retornar a Gotham como o Batman.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BOTY4YjI2N2MtYmFlMC00ZjcyLTg3YjEtMDQyM2ZjYzQ5YWFkXkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_.jpg'
                ],
                [
                    'titulo' => 'Batman: O Cavaleiro das Trevas',
                    'ano_lancamento' => 2008,
                    'ordem_cronologica' => 2,
                    'ordem_lancamento' => 2,
                    'streaming' => 'Max',
                    'streaming_url' => 'https://www.max.com/search?q=O+Cavaleiro+das+Trevas',
                    'duracao' => '2h 32m',
                    'classificacao_etaria' => '14',
                    'sinopse' => 'Com a ajuda do Tenente Gordon e do promotor Harvey Dent, Batman mantém a ordem em Gotham até a chegada do anárquico e brilhante Coringa.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_.jpg'
                ],
                [
                    'titulo' => 'Batman: O Cavaleiro das Trevas Ressurge',
                    'ano_lancamento' => 2012,
                    'ordem_cronologica' => 3,
                    'ordem_lancamento' => 3,
                    'streaming' => 'Max',
                    'streaming_url' => 'https://www.max.com/search?q=O+Cavaleiro+das+Trevas+Ressurge',
                    'duracao' => '2h 44m',
                    'classificacao_etaria' => '14',
                    'sinopse' => 'Oito anos após assumir a culpa pelos crimes de Harvey Dent, um Bruce Wayne debilitado é forçado a voltar para salvar Gotham do terrorista Bane.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTk4ODQzNDY3Ml5BMl5BanBnXkFtZTcwODA0NTM4Nw@@._V1_.jpg'
                ],
                [
                    'titulo' => 'The Batman',
                    'ano_lancamento' => 2022,
                    'ordem_cronologica' => 4,
                    'ordem_lancamento' => 4,
                    'streaming' => 'Max',
                    'streaming_url' => 'https://www.max.com/search?q=The+Batman',
                    'duracao' => '2h 56m',
                    'classificacao_etaria' => '14',
                    'sinopse' => 'No seu segundo ano como combatente do crime, Batman investiga uma série de assassinatos sádicos cometidos pelo misterioso Charada na elite corrupta de Gotham.',
                    'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMDdmMTBiNTYtMGMzYS00ODAzLWEzOTUtZGRkNmVmNjBkNWYxXkEyXkFqcGdeQXVyMTkxNjUyNQ@@._V1_.jpg'
                ]
            ]
        ]
    ];

    $stmtFranquia = $pdo->prepare("INSERT INTO franquias (nome, slug, genero, descricao, banner_url, icone_url, criado_por, visibilidade, criado_em) VALUES (:nome, :slug, :genero, :descricao, :banner_url, :icone_url, :criado_por, 'publica', :criado_em)");
    $stmtFilme = $pdo->prepare("INSERT INTO filmes (franquia_id, titulo, ano_lancamento, ordem_cronologica, ordem_lancamento, streaming, streaming_url, duracao, classificacao_etaria, sinopse, poster_url, criado_por, criado_em) VALUES (:franquia_id, :titulo, :ano_lancamento, :ordem_cronologica, :ordem_lancamento, :streaming, :streaming_url, :duracao, :classificacao_etaria, :sinopse, :poster_url, :criado_por, :criado_em)");

    foreach ($franquias as $fr) {
        $stmtFranquia->execute([
            ':nome' => $fr['nome'],
            ':slug' => $fr['slug'],
            ':genero' => $fr['genero'],
            ':descricao' => $fr['descricao'],
            ':banner_url' => $fr['banner_url'],
            ':icone_url' => $fr['icone_url'],
            ':criado_por' => $adminId,
            ':criado_em' => date('Y-m-d H:i:s')
        ]);
        $franquiaId = $pdo->lastInsertId();

        foreach ($fr['filmes'] as $f) {
            $stmtFilme->execute([
                ':franquia_id' => $franquiaId,
                ':titulo' => $f['titulo'],
                ':ano_lancamento' => $f['ano_lancamento'],
                ':ordem_cronologica' => $f['ordem_cronologica'],
                ':ordem_lancamento' => $f['ordem_lancamento'],
                ':streaming' => $f['streaming'],
                ':streaming_url' => $f['streaming_url'] ?? null,
                ':duracao' => $f['duracao'],
                ':classificacao_etaria' => $f['classificacao_etaria'],
                ':sinopse' => $f['sinopse'],
                ':poster_url' => $f['poster_url'],
                ':criado_por' => $adminId,
                ':criado_em' => date('Y-m-d H:i:s')
            ]);
        }
    }

    // Registrar auditoria
    registrar_log($pdo, 'CATALOGO_ATUALIZADO', 'Catálogo populado com a saga Quarteto Fantástico e filmes completos com streaming.', $adminId, 'Sistema');
}
