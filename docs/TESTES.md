# Plano de testes e checklist de segurança — LabControl

## Já verificado na preparação
- [x] Sintaxe de 17 arquivos PHP conferida por `php -l`.
- [x] JavaScript de filtros da demonstração testado via `node --test tests/demo-filter.test.js`.
- [x] Demonstração, login e cadastro responderam HTTP 200 em teste local simples (sem dados do MySQL).
- [x] Revisão estática de consultas preparadas PDO, `password_hash`/`password_verify`, restrições por perfil e CSRF.
- [x] Removidas credenciais fixas do protótipo PHP fornecido; o `.env` real é ignorado pelo Git.

## Relato de testes em hospedagem (ainda não reproduzidos independentemente)

A responsável informou que importou as tabelas, configurou banco MySQL e administrador em `https://labcontrol.xo.je/`, testou o sistema e confirmou funcionamento depois da correção do erro 403 no envio de solicitações. Não há evidências independentes de cada caso de teste nesta revisão. A checklist abaixo permanece como roteiro de verificação final, não como falha confirmada.

## Checklist de confirmação em produção
- [ ] Importar `sql/install.sql` em banco novo e conferir três tabelas e chaves estrangeiras.
- [ ] Configurar primeiro administrador por `setup.php` e token privado; depois remover token e instalador no site público.
- [ ] Cadastrar aluno fictício e confirmar senha em formato hash no banco.
- [ ] Fazer login com senha correta, rejeitar senha incorreta e realizar logout.
- [ ] Aluno solicitar equipamento disponível; pedido gravado como pendente.
- [ ] Administrador aprovar ou recusar; aprovação muda equipamento para emprestado e impede dupla aprovação.
- [ ] Registrar devolução e verificar disponibilidade restaurada.
- [ ] Aluno cancelar pedido pendente e consultar apenas o próprio histórico.
- [ ] Admin gerar relatório e baixar CSV.
- [ ] Aluno recebe HTTP 403 nos endpoints administrativos; POST sem CSRF deve retornar 403.
- [ ] Em hospedagem, `/.env` e scripts SQL não devem revelar conteúdo.
- [ ] Conferir ambos os links em aba anônima (GitHub e site online).
- [ ] Confrontar cada item com o Anexo B da apostila de segurança; registrar o que foi testado e as limitações.

**Não apresentar o filtro da demonstração como prova do fluxo MySQL:** a demonstração usa dados fictícios fixos. Testes de integração, acessibilidade completa e disponibilidade da hospedagem ainda precisam de validação.
