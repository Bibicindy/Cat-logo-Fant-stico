# Catálogo Fantástico

## Objetivos
1. Mapear Universo ➔ O usuário escolhe a franquia e o sistema localiza onde cada filme está disponível (Disney+, Netflix, Max), eliminando a perda de tempo no lazer.
2. Definir Rota ➔ A plataforma reorganiza instantaneamente a lista com base na preferência do fã: Ordem Cronológica (enredo) ou Ordem de Lançamento (cinema).
3. Sincronizar Progresso ➔ A cada filme marcado no checklist, o banco de dados atualiza o status do perfil e recalcula a porcentagem (%) concluída da franquia.
4. Opção de troca do local na lista dos filmes:  podendo colocar-los na ordem em que desejar e acrescentar filmes da franquia que não estam presente na lista.
5. Acrescentar franquias de filmes que não estam presente no site, iniciando uma nova lista em ordem.
6. Usuários com poder: usuários poderão organizar a lista de ordem cronológica e o registro ficará salvo de acordo com a última mudança salva.

### Stack Tecnlógico
- Backend: PHP estruturado com sessões nativas
- Banco de dados: MySQL (PDO para segurança)
- Frontend: HTML5, PHP, CSS, Tailwind CSS

#### Regras de negócio (CORE)
Tratar senhas de usuários com hash bcript
O sistema deve ter uma página de históricos e manter sempre os logs de qualquer alteração feita por 
qualquer usuário, para auditorias futuras.
- Cadastro de usuários:
Não é necessário para entrar no site e nem fazer as listas, porém para salvá-las é necessário um nome de usuário, email ou telefone e senha.

- Login e segurança do site:
 Para o acesso as listas, como salvá-las é necessário as credenciais usadas no login, para proteger os dados dos usuários.

- Validação de informações: 
impedir o envio de formulários com campos obrigatórios vazios ou dados incorretos.

- Permissões de acesso do usuário em outras contas:
O usuário pode definir se outros podem visualizar, editar, excluir ou administrar suas listas, ou por meio de convites ou acesso público.

- Privacidade de dados:
Coletas somente de dados necessários, como para o login, e com a permissão do usuário usar dados de pesquisas anteriores para recomendações, além de respeitar a LGPD, 

- Navegação: 
garantir que os menus, botões e links funcionem como devem.

- Tratamento de erros: informar ao usuário quando uma ação não puder ser concluída e oferecer um espaço para o público avaliar o site e o que a plataforma pode melhorar

- Disponibilidade: estabelecer como o site deve funcionar e como lidar com falhas técnicas.

- Regras do conteúdo disponível:
determinar quais conteúdos podem ser publicados e quais são proibidos de acordo com a idade e anúncios que possam incitar algum crime ou vício.

- Responsividade: 
permitir que o site funcione adequadamente em qualquer aparelho.


##### Regras Globais
- Use sempre PDO para conexão e queries no MySQL para evitar SQL Injections
- Mantenha o código limpo e comente apenas logicas complexas.
- Separe os arquivos de forma lógica: um arquivo para conexão com a base (bd.php) e scripts de backend isolados e views em HTML5/PHP, nunca faça o sistema como um monolito, deixe sempre separados todas as regras para facilitar os futuros upgrades.
 - Estilize as telas em Tailwind de forma responsiva priorizando o MobileFrist.
 - Retorne sempre as mensagens de erros de forma claras na interface para o usuário (TOAST)
 - sempre trate as mensagens de caixa de mensagens nativas do navegador em um MODAL
