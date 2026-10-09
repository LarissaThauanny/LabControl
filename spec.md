\# LabControl — Sistema de Empréstimo de Equipamentos



\## 1. Objetivo



Desenvolver um sistema web para controlar o empréstimo e a devolução de equipamentos do laboratório de informática.



\## 2. Usuários



\* \*\*Aluno:\*\* pode criar uma conta, entrar no sistema, consultar equipamentos disponíveis, solicitar empréstimos e acompanhar suas solicitações.

\* \*\*Administrador:\*\* pode cadastrar e editar equipamentos, aprovar ou recusar solicitações e registrar devoluções.



\## 3. Casos de uso



1\. Cadastro e login de usuários.

2\. Consulta de equipamentos disponíveis.

3\. Solicitação de empréstimo.

4\. Aprovação ou recusa pelo administrador.

5\. Registro de devolução.

6\. Consulta ao histórico de empréstimos.



\## 4. Requisitos funcionais



\* RF01: permitir cadastro e login.

\* RF02: mostrar os equipamentos disponíveis.

\* RF03: permitir solicitações de empréstimo.

\* RF04: permitir ao administrador gerenciar equipamentos.

\* RF05: permitir aprovação, recusa e registro de devoluções.

\* RF06: armazenar e consultar o histórico.



\## 5. Requisitos não funcionais



\* RNF01: interface simples e responsiva.

\* RNF02: senhas armazenadas com hash seguro.

\* RNF03: validar os dados no servidor.

\* RNF04: proteger rotas administrativas.

\* RNF05: usar variáveis de ambiente para informações secretas.



\## 6. Modelo de dados



\* \*\*Usuarios:\*\* id, nome, email, senha\_hash, tipo, criado\_em.

\* \*\*Equipamentos:\*\* id, nome, descricao, patrimonio, status, criado\_em.

\* \*\*Emprestimos:\*\* id, usuario\_id, equipamento\_id, status, data\_solicitacao, data\_aprovacao, data\_devolucao.



\## 7. Critérios de aceitação



\* Um usuário consegue se cadastrar e entrar.

\* O sistema apresenta equipamentos disponíveis.

\* Um aluno consegue solicitar um empréstimo.

\* Um administrador consegue aprovar ou recusar a solicitação.

\* O sistema registra a devolução e atualiza a disponibilidade.

\* Os dados são salvos no banco e podem ser consultados depois.



\## 8. Tecnologias



\* Next.js e Node.js.

\* PostgreSQL usando Neon.

\* Git e GitHub para controle de versão.

\* Vercel para publicação.



\## 9. Observações



A aplicação deve ter autenticação segura, validação no servidor e controle de acesso por tipo de usuário. Senhas nunca devem ser armazenadas em texto puro.



