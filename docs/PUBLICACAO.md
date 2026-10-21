# Como publicar LabControl — PHP + MySQL

O GitHub armazena o código, mas não executa PHP e MySQL. Para atender à entrega, publique o sistema completo em uma hospedagem PHP/MySQL (por exemplo, InfinityFree).

## Instalação na hospedagem
1. Crie a conta de hospedagem e um domínio/subdomínio com PHP e MySQL disponíveis.
2. No painel MySQL, crie o banco e anote privadamente host, nome do banco, usuário, porta e senha. **Não publique senhas.**
3. Abra phpMyAdmin, selecione o banco atribuído e importe `sql/hosting.sql`. Não importe `sql/install.sql` porque este cria um banco local.
4. Envie os arquivos do LabControl para `htdocs`; `index.php` e `demo.php` ficam na raiz pública.
5. Crie `htdocs/.env` a partir de `.env.example`, com `APP_BASE_PATH=/`, informações do banco e um `ADMIN_SETUP_TOKEN` aleatório de 24+ caracteres. Gere o token localmente com `php -r "echo bin2hex(random_bytes(24)), PHP_EOL;"`.
6. Confirme que `/.env`, `/sql/hosting.sql` e `/config/database.php` não exibem conteúdo privado (403 ou 404).
7. Abra `/setup.php` no domínio e crie o primeiro administrador usando o token. Remova o token do `.env` e exclua `setup.php` do servidor assim que concluir.
8. Abra a raiz do site em aba anônima; ela deve exibir uma demonstração fictícia. Cadastre um aluno de teste, faça login, solicite equipamento, aprove como admin, devolva e consulte o relatório CSV.
9. Teste o sistema no celular e em HTTPS. Só forneça a URL ao professor depois de comprovar acesso ao banco e fluxo funcional.

**Importante:** commits no GitHub não atualizam o InfinityFree automaticamente; envie os arquivos novos manualmente para `htdocs`, sem sobrescrever o `.env` privado.

**URL pública:** ainda não configurada e verificada.
