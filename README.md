# LabControl — Gestão de empréstimos do laboratório

Sistema educacional PHP 8.2+ e MySQL para controle de equipamentos do laboratório, solicitações por alunos, aprovações e devoluções pelo administrador.

**Demonstração pública:** abra a raiz do domínio (visitantes são direcionados para `demo.php`), com equipamentos fictícios. **Código-fonte:** https://github.com/LarissaThauanny/LabControl.

## Recursos

- Cadastro e login com hash de senha; perfis `aluno` e `admin` e proteção CSRF.
- Aluno: catálogo, pedido de empréstimo, cancelamento de pedido pendente e histórico.
- Administrador: equipamentos, manutenção, solicitações, aprovação/recusa, devoluções, usuários e relatórios CSV.
- Empréstimos com transações e travas no MySQL para evitar dupla aprovação.
- Interface adaptável e demonstração fictícia sem banco nem login.
- Nenhuma credencial padrão; primeiro admin somente via token privado de instalação.

## Instalação rápida (Windows/XAMPP)

1. Instale XAMPP com PHP 8.2+, Git e MySQL/MariaDB compatível.
2. No PowerShell, execute: `cd C:\xampp\htdocs` e `git clone https://github.com/LarissaThauanny/LabControl.git LabControl`.
3. `cd LabControl`, `Copy-Item .env.example .env` e preencha DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS, APP_BASE_PATH e ADMIN_SETUP_TOKEN.
4. Gere o token com `php -r "echo bin2hex(random_bytes(24)), PHP_EOL;"` e coloque apenas no `.env`.
5. Inicie Apache e MySQL; importe **`sql/install.sql`** no phpMyAdmin (`http://localhost/phpmyadmin`).
6. Abra **`http://localhost/LabControl/setup.php`**, crie o primeiro administrador com token privado, remova o token e depois entre em `login.php`.
7. Crie um aluno fictício em `cadastro.php`, faça um pedido, aprove como admin e registre devolução.
8. Abra `http://localhost/LabControl/demo.php` para ver a demonstração pública sem banco.

**Leia o passo a passo completo em [spec.md](spec.md), seção 9.** Não importe o novo SQL em uma base antiga com dados sem backup: o protótipo anterior utiliza esquema diferente.

## Para avaliação

- [SPEC final e instalação](spec.md)
- [Script MySQL local](sql/install.sql)
- [Script MySQL de hospedagem](sql/hosting.sql)
- [Arquivo de ambiente exemplo](.env.example)
- [Registro de decisões e revisões](CHANGELOG.md)
- [Checklist de segurança e testes](docs/TESTES.md)
- [Publicação PHP + MySQL](docs/PUBLICACAO.md)
- [Checklist de entrega](docs/ENTREGA_PROFESSOR.md)

**Status real:** checagem de sintaxe e teste de filtros de demonstração concluídos. Integração MySQL, publicação pública e instalação independente ainda exigem testes da responsável. GitHub não executa PHP automaticamente. Os dados de demonstração são fictícios.

## Hospedagem configurada

- **Endereço:** https://labcontrol.xo.je/
- **Situação:** a responsável informou que realizou testes funcionais com MySQL na hospedagem, inclusive após correção do HTTP 403 em solicitações. Não houve validação independente da URL por esta revisão; confira em aba anônima antes da entrega.
