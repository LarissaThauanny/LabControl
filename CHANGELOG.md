# CHANGELOG — LabControl

Este arquivo registra o que foi revisado no desenvolvimento com apoio de IA e as decisões que precisam de confirmação da responsável. As datas dos commits verdadeiros devem ser consultadas diretamente no GitHub. **Não** devem ser inventados commits nem datas de desenvolvimento.

## Histórico inicial — 09/10/2026

- O repositório original de `LarissaThauanny/LabControl` foi iniciado com a SPEC de sistema de empréstimos, que previa alunos, administradores e fluxo de aprovação. A stack proposta era Next.js, Node.js, PostgreSQL/Neon e Vercel.
- A versão inicial continua disponível no histórico do GitHub. O conteúdo foi revisado para refletir o sistema que foi realmente desenvolvido.

## Protótipo PHP/XAMPP recebido — outubro de 2026

- ZIP de desenvolvimento com páginas PHP para gerenciar equipamentos, empréstimos registrados diretamente pelo administrador, login e relatórios.
- O protótipo possuía cópias duplicadas da pasta, login incompatível com colunas do SQL, credencial de banco em configuração e criação de administrador com senha fixa.
- Foi feita uma revisão crítica dessas limitações em vez de aceitar o código gerado ou fornecer senhas padrão na entrega.

## Versão final do código — LabControl 3.0 (preparação da entrega)

### Reestruturação e decisão técnica

- Escolhida a continuidade da versão PHP 8.2+/MySQL, por ser compatível com XAMPP e hospedagem PHP/MySQL, em vez de afirmar implementação da stack Next.js planejada inicialmente.
- Reconstruídas as tabelas `usuarios`, `equipamentos`, `emprestimos` e os fluxos dependentes. O esquema é indicado para instalação **nova**; não foi prometida migração automática dos dados antigos.
- Preservadas a ideia de inventário, empréstimos, devoluções e relatórios, com interface visual reformulada aproveitada do protótipo.

### Requisitos da SPEC original adicionados

- Cadastro público de aluno com perfil restrito, login e alteração de senha.
- Solicitação de equipamento pelo aluno e visualização somente do próprio histórico.
- Aprovação/recusa pelo administrador, prazo de devolução, cancelamento de pedido pendente e registro de devolução.
- Travas/transações para evitar duas aprovações do mesmo equipamento; atualização automática da disponibilidade.
- Cadastro/edição/estado de manutenção de equipamentos, listagem administrativa de usuários e relatório CSV.
- Demonstração pública sem login nem MySQL, com itens explicitamente fictícios para evitar exposição de dados pessoais.

### Revisão de segurança, publicação e documentação

- Removida configuração com senha fixa e instalador de administrador inseguro; criada opção `setup.php` com token privado e bloqueio após instalação.
- `.env.example` e `.gitignore`; bloqueios Apache `.htaccess`, PDO parametrizado, CSRF, autenticação por sessão e autorização por perfil.
- Criados SQL local e para hospedagem, SPEC de instalação, README, checklist de segurança/testes e guia para publicar PHP/MySQL.
- Repositório organizado para envio ao GitHub **sem substituir o commit da SPEC original**.

### Verificações realizadas

- Checagem de sintaxe PHP (`php -l`) para 17 arquivos, sem erros.
- Teste automatizado de filtro da demonstração (`node --test tests/demo-filter.test.js`) passou.
- Não houve execução completa de MySQL no ambiente de preparação. O fluxo aluno → solicitação → aprovação → devolução → relatório precisa de teste real em XAMPP e hospedagem antes de declarar a aplicação integralmente pronta.

## Publicação e correção em hospedagem PHP/MySQL — outubro de 2026

- O site foi configurado pela responsável no endereço **https://labcontrol.xo.je/** com banco MySQL em hospedagem InfinityFree; ela informou que as funcionalidades testadas estavam funcionando.
- Durante o teste de envio de solicitação, foi observado erro **HTTP 403**. A causa identificada foi a regra `Require all denied` aplicada equivocadamente à pasta `actions/`, que bloqueava os POSTs legítimos mesmo com autenticação e CSRF implementados.
- A regra foi corrigida para `Options -Indexes` no arquivo `actions/.htaccess` e a mudança foi versionada em commit real no GitHub. A responsável confirmou que voltou a funcionar.
- Os testes de integração com banco são **relatados pela responsável**, não reproduzidos pelo ambiente de revisão externo. A conferência final do checklist de segurança e o acesso em janela anônima continuam necessários.

## Pendências para a responsável verificar antes de entregar

- Confirmar funcionamento de banco com duas contas fictícias e testes de permissão.
- Testar instalação em outro computador **seguindo apenas a SPEC**, corrigindo qualquer passo que trave.
- Publicar em hospedagem que execute PHP/MySQL, conferir HTTPS e o domínio em aba anônima.
- Confirmar que o repositório esteja público e acessível ao professor (se ficar privado, conceder-lhe acesso).
- Confirmar a decisão de stack e a descrição deste changelog, pois a revisão humana final é responsabilidade da autora do projeto.