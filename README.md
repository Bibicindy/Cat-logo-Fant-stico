# 🎬 Catálogo Fantástico

> Plataforma inteligente para mapear universos cinematográficos, alternar rotas de exibição (Ordem Cronológica vs. Ordem de Lançamento vs. Ordem Personalizada) e sincronizar o progresso de maratonas em tempo real.

---

## 🚀 Funcionalidades Implementadas

1. **Mapear Universo**:
   - Exibição de onde cada filme está disponível para assistir (Disney+, Netflix, Max, Prime Video, Apple TV, etc.) com badges visuais temáticos e filtros rápidos.
2. **Definir Rota**:
   - Alternância fluida e instantânea entre:
     - 🚀 **Ordem Cronológica (Enredo)**
     - 🍿 **Ordem de Lançamento (Cinemas)**
     - ✨ **Minha Ordem Personalizada (Reordenação Manual e Salva)**
3. **Sincronizar Progresso**:
   - Checklist interativo de filmes assistidos via AJAX sem recarregar a página.
   - Recálculo dinâmico da porcentagem (%) concluída da franquia e quantidade de filmes vistos.
   - Sincronização automática para usuários logados no banco de dados e sessão temporária para visitantes.
4. **Troca do Local na Lista & Adição de Filmes**:
   - Botões "Subir" e "Descer" para reordenar filmes na lista com persistência imediata.
   - Modal com validação completa para acrescentar filmes que não estavam presentes na franquia (título, ano, streaming, classificação etária, duração, sinopse, pôster).
5. **Acrescentar Novas Franquias**:
   - Cadastro de novas franquias e sagas cinematográficas com geração de slug, banner de capa, ícone e visibilidade (pública ou privada).
6. **Usuários com Poder & Auditoria**:
   - Usuários cadastrados podem organizar e salvar ordens cronológicas e personalizadas.
   - **Histórico & Logs de Auditoria** (`historico.php`): registro permanente de todas as ações de usuários (criação, edição, reordenação, exclusão, IPs, datas e horários).
7. **Segurança e Privacidade (LGPD & CORE)**:
   - Criptografia irreversível de senhas com `bcrypt` (`password_hash`).
   - Conexão e queries com **PDO e Prepared Statements** contra SQL Injections.
   - Navegação aberta: visitantes podem navegar e testar listas livremente; cadastro com nome, usuário, email ou telefone e senha para persistência definitiva.
   - Modal de termos LGPD e consentimento de dados para recomendações.
   - Política de classificação indicativa (Livre, 10+, 12+, 14+, 16+, 18+) e moderação de conteúdos ilícitos ou anúncios com incitação a vícios/crimes.
8. **Avaliações & Espaço de Melhorias** (`feedbacks.php`):
   - Módulo para o público avaliar a plataforma com 1 a 5 estrelas, categorias (sugestão, elogio, crítica, bug) e comentários.
9. **UI Moderna Mobile-First**:
   - Estilização com **Tailwind CSS**, design cinematográfico escuro e responsivo.
   - Notificações **Toast** animadas para mensagens de feedback claras.
   - Sistema de **Modal customizado** que substitui alertas e caixas nativas do navegador.

---

## 🛠️ Stack Tecnológico

- **Backend:** PHP 8.x estruturado com sessões nativas
- **Banco de Dados:** MySQL (com conexão segura via PDO e fallback automático)
- **Frontend:** HTML5, Tailwind CSS, Font Awesome, JavaScript Vanilla

---

## 💻 Como Rodar o Projeto

### Opção 1: Servidor Embutido do PHP (Execução Instantânea)

Abra o terminal na pasta do projeto e execute:
```bash
php -S localhost:8000
```
Acesse no navegador:
```
http://localhost:8000
```

### Opção 2: Com MySQL (XAMPP, Laragon ou Docker)

1. Inicie o serviço do MySQL na porta padrão (`3306`).
2. Se desejar, importe o arquivo `database/schema.sql` no phpMyAdmin ou console MySQL:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
3. Caso queira configurar usuário ou senha do MySQL diferentes do padrão (`root` / sem senha), edite o arquivo `config/database.php`:
   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_PORT', '3306');
   define('DB_NAME', 'catalogo_fantastico');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```
4. Inicie o servidor PHP ou acesse pelo Apache do XAMPP (`http://localhost/Catálogo Fantástico/`).

---

## 🔑 Credenciais Padrão Pré-Cadastradas

- **Usuário:** `admin`
- **Senha:** `admin123`
*(Você também pode criar novos usuários na página de Cadastro).*

---

## 📂 Estrutura de Diretórios

```
Catálogo Fantástico/
├── actions/                  # Controladores e scripts de processamento backend
│   ├── add_franchise.php     # Cadastro de novas franquias
│   ├── add_movie.php         # Cadastro de filmes em franquias
│   ├── auth_login.php        # Autenticação e login
│   ├── auth_logout.php       # Encerramento de sessão
│   ├── auth_register.php     # Cadastro com validações e bcrypt
│   ├── delete_movie.php      # Remoção de filme com auditoria
│   ├── save_order.php        # Persistência da reordenação
│   ├── submit_feedback.php   # Avaliações do público
│   ├── toggle_progress.php   # AJAX para checklist e cálculo de %
│   └── update_permissions.php# Gerenciador de permissões e convites
├── assets/
│   ├── css/custom.css        # Estilos cinematográficos e animações
│   └── js/main.js            # Lógica dos Toasts, Modais e AJAX
├── config/
│   └── database.php          # Configuração de credenciais MySQL
├── database/
│   ├── schema.sql            # Script SQL MySQL completo
│   └── seed.php              # Inicializador e seed automático
├── includes/
│   ├── footer.php            # Rodapé global e scripts
│   ├── header.php            # Cabeçalho HTML, Tailwind e fontes
│   ├── modal.php             # Modal universal personalizado
│   ├── navbar.php            # Barra de navegação responsiva
│   └── toast.php             # Sistema de notificações Toast
├── bd.php                    # Conexão PDO desacoplada e utilitários
├── cadastro.php              # Tela de registro de novo usuário
├── feedbacks.php             # Avaliações e canal de sugestões
├── franquia.php              # Página da franquia, rotas e filmes
├── historico.php             # Logs de auditoria de alterações
├── index.php                 # Página inicial e catálogo de universos
├── login.php                 # Tela de login
├── nova-franquia.php         # Tela para criar nova franquia
├── perfil.php                # Perfil do usuário e permissões de listas
├── privacidade.php           # Termos de privacidade LGPD e moderação
└── requisitos.md             # Documento de requisitos do projeto
```
