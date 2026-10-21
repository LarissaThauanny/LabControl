# SPEC FINAL — LabControl

**Projeto:** LabControl — Sistema de Empréstimo de Equipamentos do Laboratório  
**Versão:** 3.0 (reconstrução do protótipo PHP/XAMPP, outubro de 2026)  
**Curso:** Desenvolvimento de Sistemas, Instituto Federal de Rondônia — Campus Porto Velho Calama  
**Repositório:** https://github.com/LarissaThauanny/LabControl  
**Situação:** código versionado no GitHub e hospedagem configurada em **https://labcontrol.xo.je/**. A responsável relatou conclusão de testes dos fluxos principais com MySQL na hospedagem; a checagem externa independente do domínio não foi possível nesta revisão.

## 1. Objetivo e problema

O laboratório de informática precisa de um registro centralizado de equipamentos, solicitações de empréstimos e devoluções. O LabControl permite consultar itens cadastrados e sua disponibilidade, receber pedidos dos alunos e registrar decisões administrativas com histórico consultável. Não é um sistema oficial do IFRO nem contém inventário real por padrão.

## 2. Público e papéis

- **Visitante:** abre a página `demo.php` e explora itens **fictícios** sem criar conta ou instalar nada. Pode entrar ou se cadastrar.
- **Aluno:** cadastra-se, faz login, consulta equipamentos, solicita empréstimos com prazo, vê respostas e histórico, cancela apenas solicitações pendentes e troca sua senha.
- **Administrador:** cadastra e edita equipamentos, marca manutenção, aprova/recusa pedidos, registra devoluções, consulta usuários, histórico e relatórios CSV. Não pode transformar uma conta pública em administrador pelo formulário.

## 3. Casos de uso

1. Cadastrar e autenticar aluno com senha protegida por hash.
2. Consultar catálogo de equipamentos, incluindo itens disponíveis e indisponíveis.
3. Solicitar empréstimo de equipamento disponível, indicando data pretendida para devolução.
4. Administrador aprovar pedido, definindo prazo, ou recusá-lo com observação opcional.
5. Registrar a devolução e atualizar a disponibilidade do equipamento.
6. Aluno consultar seus pedidos; administrador consultar os pedidos de todas as contas e exportar relatório.
7. Administrador cadastrar, editar e marcar equipamentos em manutenção sem apagar histórico.
8. Avaliador abrir demonstração pública fictícia com pesquisa e filtros sem banco de dados.

## 4. Requisitos funcionais e critérios de aceite

| ID | Requisito | Teste de aceite esperado |
| --- | --- | --- |
| RF01 | Cadastro de aluno e login | Senha é hash; credenciais corretas autenticam; incorretas são recusadas |
| RF02 | Listar e pesquisar equipamentos | O catálogo exibe nome, categoria, patrimônio e condição |
| RF03 | Solicitar empréstimo | Aluno envia pedido; entra em `pendente` e aparece na área admin |
| RF04 | Aprovar ou recusar solicitação | Só admin pode decidir; aprovação exige item disponível |
| RF05 | Devolver equipamento | Aprovação coloca item como `emprestado`; devolução coloca `disponivel` |
| RF06 | Exibir histórico | Aluno só vê seus registros; administrador consulta todos |
| RF07 | Gerenciar equipamentos | Admin pode cadastrar/editar; exclusão com histórico é impedida pelo banco |
| RF08 | Relatórios | Apenas admin pode filtrar e exportar CSV |
| RF09 | Segurança dos formulários | POST exige token CSRF, controle de sessão e validação no servidor |
| RF10 | Demonstração pública | `demo.php` abre e filtra dados fictícios sem MySQL |
| RF11 | Primeiro administrador | Criado com token privado; não existe conta ou senha padrão |
| RF12 | Interface responsiva | Layout adaptável a celular e computador |

## 5. Regras de negócio

- RN01: todo cadastro público nasce com `tipo='aluno'`. Apenas o procedimento inicial com token configura o primeiro administrador.
- RN02: pedido novo tem status `pendente`; o aluno pode cancelar apenas pedidos pendentes próprios.
- RN03: ao aprovar, o sistema verifica e bloqueia o item em transação. O equipamento vira `emprestado`; solicitações pendentes concorrentes para o mesmo equipamento são recusadas automaticamente. Uma tentativa de aprovar item ocupado falha.
- RN04: uma solicitação só pode ser aprovada ou recusada se estiver `pendente`.
- RN05: só o administrador registra devolução; empréstimo `aprovado` vira `devolvido` e libera o item.
- RN06: a data de devolução prevista deve estar entre hoje e os próximos 365 dias; a data não implica cálculo automático de multa.
- RN07: itens `manutencao` ou `emprestado` não podem receber novas solicitações.
- RN08: nenhuma senha, token ou dado real de usuários faz parte do SQL inicial. Os três equipamentos iniciais são explicitamente fictícios.

## 6. Requisitos não funcionais

- RNF01: aplicação web em PHP 8.2+ com extensões PDO MySQL e um SGBD MySQL 8 ou MariaDB compatível.
- RNF02: HTML5/CSS/JavaScript, layout responsivo, sem Composer, npm ou bibliotecas externas obrigatórias.
- RNF03: queries parametrizadas para dados do usuário, senhas com `password_hash()`/`password_verify()`, sessão HTTP Only/SameSite e regeneração após login.
- RNF04: token CSRF obrigatório em formulários de alteração; segregação de perfis em rotas e endpoints.
- RNF05: `.env` ignorado pelo Git e protegido por `.htaccess` (requer Apache compatível); mensagens de erro do banco não revelam credenciais.
- RNF06: projeto escolar, uso de dados fictícios nos testes; arquivos `.env`, backups e dados pessoais não devem ir para o GitHub.
- RNF07: a hospedagem precisa executar PHP e acessar MySQL; GitHub Pages e Vercel estático não executam diretamente esta versão PHP.

## 7. Arquitetura e decisão de stack

O `spec.md` **inicial** publicado em 09/10/2026 planejava **Next.js, Node.js, PostgreSQL/Neon e Vercel**. Durante o desenvolvimento foi produzido um protótipo funcional em **PHP/MySQL para XAMPP**; esta versão o reconstrói, com autenticação por papéis, solicitações e aprovação, para obter uma instalação reproduzível em XAMPP e hospedagem PHP/MySQL. A mudança de tecnologia é uma **decisão de revisão documentada**, não se afirma que a stack inicialmente prevista foi implementada. O histórico original da especificação pode ser consultado no GitHub.

Organização:

- `config/database.php`: lê variáveis de ambiente e cria conexão PDO.
- `includes/bootstrap.php`: sessão, acesso, HTML seguro, CSRF e utilitários.
- `cadastro.php`, `login.php`, `logout.php`, `senha.php`, `setup.php`: identidade e controle de acesso.
- `index.php`, `equipamentos.php`, `solicitacoes.php`, `usuarios.php`, `relatorios.php`: interfaces autenticadas.
- `actions/equipamento.php`, `actions/solicitacao.php`: alterações no banco em POST autenticado.
- `demo.php`: interface pública read-only com dados fictícios; dispensa conexão ao MySQL.
- `sql/install.sql`: instalação local; `sql/hosting.sql`: instalação em banco já criado na hospedagem.

## 8. Modelo de dados e relacionamentos

O banco se chama **`labcontrol`** na instalação local. São **três tabelas**; o SQL cria todas e insere três exemplos fictícios de equipamentos.

| Tabela | Campos principais | PK | Relacionamentos |
| --- | --- | --- | --- |
| `usuarios` | `id`, `nome`, `email` único, `senha_hash`, `tipo` (`aluno`,`admin`), `criado_em` | `id` | 1:N com `emprestimos` |
| `equipamentos` | `id`, `nome`, `descricao`, `codigo` único, `categoria`, `status` (`disponivel`,`emprestado`,`manutencao`), `criado_em` | `id` | 1:N com `emprestimos` |
| `emprestimos` | `id`, `usuario_id`, `equipamento_id`, `status`, `justificativa`, `observacao_admin`, `data_solicitacao`, `data_aprovacao`, `data_devolucao_prevista`, `data_devolucao` | `id` | FK `usuario_id` → `usuarios.id`; FK `equipamento_id` → `equipamentos.id` |

As FKs têm `ON DELETE RESTRICT` para evitar a exclusão acidental de histórico. Não há contas padrão, credenciais de administrador ou dados de estudantes predefinidos. Não é necessário rodar `seed` separado: a instalação já insere o catálogo fictício inicial.

## 9. Instalação e execução

### 9.1 Ambiente

| Requisito | Versão / observação |
| --- | --- |
| Sistema operacional recomendado | Windows 10/11 para XAMPP; arquivos de código conferidos em Linux com PHP 8.4 |
| Linguagem | PHP 8.2 ou superior |
| Servidor | Apache 2.4+ no XAMPP; não use `php -S` para hospedagem de `.env` porque ele ignora `.htaccess` |
| SGBD | MySQL 8.0+ ou MariaDB 10.5+ compatível; InnoDB e utf8mb4 |
| Extensões PHP | `pdo_mysql`; nenhuma dependência externa de runtime |
| Dependências | Não há `composer install` ou `npm install`. JavaScript de filtro é local |
| Ferramentas úteis | Git, XAMPP, phpMyAdmin ou MySQL Workbench |

### 9.2 Variáveis de ambiente

Na pasta do projeto, execute `Copy-Item .env.example .env` (PowerShell) e preencha os valores do arquivo `.env`. Ele nunca deve ser enviado ao GitHub.

| Variável | Exemplo local | Finalidade |
| --- | --- | --- |
| `APP_ENV` | `local` | Identifica ambiente para documentação |
| `APP_BASE_PATH` | `/LabControl/` | Prefixo URL no XAMPP; use `/` para raiz do site hospedado |
| `DB_HOST` | `127.0.0.1` | Host do MySQL |
| `DB_PORT` | `3306` | Porta do MySQL |
| `DB_NAME` | `labcontrol` | Nome do banco local criado pelo SQL |
| `DB_USER` | `root` | Conta MySQL local, ajustar segundo ambiente |
| `DB_PASS` | *(em branco)* | Senha da conta MySQL; nunca usar senha de hospedagem em arquivo Git |
| `ADMIN_SETUP_TOKEN` | *(gerar no computador)* | Token secreto para criar o primeiro administrador em `/setup.php`; remover do `.env` depois |

Para gerar um token seguro no computador local, com PHP instalado, execute no PowerShell: `php -r "echo bin2hex(random_bytes(24)), PHP_EOL;"`. Cole o resultado em `ADMIN_SETUP_TOKEN=` apenas no `.env` privado (não compartilhe esse valor).

### 9.3 Passo a passo no Windows/XAMPP

1. Instale Git e XAMPP que ofereça PHP 8.2+ e MariaDB/MySQL; habilite a extensão `pdo_mysql`. Se usa uma instalação separada de MySQL pelo Workbench, escolha **apenas um** servidor MySQL.
2. Abra o PowerShell e execute: `cd C:\xampp\htdocs` e, em seguida, `git clone https://github.com/LarissaThauanny/LabControl.git LabControl`.
3. Entre na pasta: `cd C:\xampp\htdocs\LabControl`. Execute `Copy-Item .env.example .env`.
4. No `.env`, mantenha `APP_BASE_PATH=/LabControl/`, configure o MySQL local e gere/preencha `ADMIN_SETUP_TOKEN` como na seção 9.2.
5. Abra o XAMPP Control Panel, inicie **Apache** e **MySQL**. Se houver conflito na porta do MySQL, ajuste `DB_PORT`; se Apache não usar porta 80, adapte o endereço nos passos abaixo.
6. Abra `http://localhost/phpmyadmin`, entre na aba **SQL**, cole e execute TODO o conteúdo de `sql/install.sql` (ou importe o arquivo pelo phpMyAdmin). O script cria o banco `labcontrol`, três tabelas, FKs e três equipamentos **fictícios**. Em MySQL Workbench, abra o arquivo e execute todas as instruções.
7. Abra **`http://localhost/LabControl/setup.php`**. Preencha o token privado e crie a primeira conta de **administrador** com nome, e-mail de teste e uma senha temporária forte (mínimo 12 caracteres). Não existe login padrão.
8. Remova o valor `ADMIN_SETUP_TOKEN` do `.env` após a criação, e, se publicar na internet, retire `setup.php` da pasta pública. Depois entre em **`http://localhost/LabControl/login.php`** como administrador.
9. Faça cadastro de aluno em **`http://localhost/LabControl/cadastro.php`** usando um e-mail fictício diferente (ex.: `aluno.teste@example.com`) e senha escolhida na hora (mínimo 10 caracteres). Faça login como aluno; escolha um equipamento na aba **Equipamentos**, envie solicitação em **Meus empréstimos**.
10. Entre como administrador em **Solicitações**, aprove o pedido, registre a devolução e veja a atualização da disponibilidade. Consulte **Relatórios** e exporte CSV.
11. Para a demonstração sem login nem MySQL, acesse **`http://localhost/LabControl/demo.php`**. Para a interface principal após login, acesse **`http://localhost/LabControl/index.php`**.

**Importante sobre a base antiga:** o `database.sql` do protótipo anterior tinha outras colunas e regras. O script `sql/install.sql` foi feito para **banco novo de teste**, não para atualizar uma base antiga já utilizada. Se houver dados reais legados, faça backup e planeje migração individual antes de usar esta versão. **Não apague a base anterior sem consentimento e backup.**

### 9.4 Instalação em hospedagem PHP + MySQL

1. Crie um banco pelo painel da hospedagem, anote **apenas em local privado** hostname, usuário, nome atribuído e senha.
2. Abra o phpMyAdmin da hospedagem, selecione o banco e importe **`sql/hosting.sql`** (não `sql/install.sql`, pois este tenta criar banco).
3. Envie os arquivos do projeto ao diretório público (`htdocs` no InfinityFree). Deixe `index.php` e `demo.php` na raiz pública. O `.env` deve ser criado apenas no servidor, usando `APP_BASE_PATH=/` se for a raiz do domínio.
4. Configure `ADMIN_SETUP_TOKEN` privado, abra `/setup.php` no domínio para configurar o primeiro admin e, depois, remova o token e o próprio `setup.php` do diretório público. Não envie esse arquivo `.env` ao GitHub.
5. Acesse `/demo.php` em janela anônima e teste cadastro, login, solicitação, aprovação, devolução e CSV. Confirme que `/.env`, `/sql/hosting.sql` e `/config/database.php` **não expõem conteúdo** (403 ou 404). Confira HTTPS.
6. Link de produção configurado: **https://labcontrol.xo.je/**. A responsável confirmou que o sistema funcionou nos testes realizados; acesso e disponibilidade para terceiros devem ser checados em aba anônima antes da entrega. O repositório GitHub sozinho não executa PHP.

Guia completo: `docs/PUBLICACAO.md`.

## 10. Como testar e verificar a segurança

- **Credenciais de teste:** nenhuma senha comum foi distribuída. Cadastre administrador via token privado; crie um aluno fictício pela interface. Nunca teste usando senha real de contas pessoais.
- **Fluxo principal:** admin cadastra equipamento → aluno faz cadastro/login → solicita empréstimo → admin aprova → equipamento fica indisponível → admin registra devolução → equipamento fica disponível → aluno visualiza histórico → admin exporta relatório.
- **Separação de acesso:** aluno deve receber HTTP 403 ao tentar `/relatorios.php`, `/usuarios.php` e ações administrativas.
- **Segurança:** valide `.env` bloqueado, senhas hash no banco, consultas parametrizadas, CSRF 403 em POST sem token e nenhum e-mail real na demonstração.
- **Verificações já realizadas:** sintaxe de 17 arquivos PHP (`php -l`) e teste automático do filtro da demonstração (`node --test tests/demo-filter.test.js`). Validações HTTP da página demonstrativa podem ser repetidas em servidor local.
- **Relato da responsável após a publicação:** banco MySQL hospedado configurado, formulário de solicitações corrigido após erro HTTP 403 e fluxo funcional nos testes realizados por ela. Esta declaração não equivale a auditoria externa. **Ainda requerem confirmação documentada:** permissões, arquivos sensíveis bloqueados, remoção de `setup.php` e do token inicial, teste em aba anônima por terceira pessoa, inspeção adicional de segurança e acessibilidade.
- **Limitações conhecidas:** sem envio de e-mails automáticos, sem notificações em tempo real, não inclui migração automática do esquema antigo, CSV limitado aos primeiros 1.500 registros filtrados; exemplo público é somente demonstrativo.

## 11. Histórico, revisão humana e entrega

A primeira SPEC foi registrada em 9 de outubro, com Next.js/PostgreSQL previstos. O protótipo em PHP/XAMPP fornecido posteriormente possuía login administrativo, inventário e empréstimos diretos, mas não o fluxo de solicitações por aluno previsto originalmente. A presente versão foi reconstruída a partir desse material, preservando a ideia e a documentação histórica no GitHub e adotando PHP/MySQL como stack final. O registro das mudanças, bugs encontrados, justificativas e das revisões manuais do responsável está em `CHANGELOG.md`.

**Critérios de entrega da atividade domiciliar:** repositório público (ou privado com acesso ao professor), preservando o commit original, SPEC e CHANGELOG coerentes; URL pública funcional sem instalação e fluxo com dados em MySQL; verificação de segurança e revisão humana. **A publicação depende da configuração do serviço de hospedagem pela responsável.** O convite ao professor não é obrigatório para repositório público nesta versão da atividade.