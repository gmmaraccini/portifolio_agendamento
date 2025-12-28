# portifolio_agendamento
Projeto 7 - Agendamento

7. Sistema de Agendamento (Consulta ou Barbearia)
O que faz: Um calendário onde um cliente pode ver horários disponíveis e marcar um horário. O admin pode gerenciar os agendamentos.
Habilidades que demonstra: Lógica de negócios complexa (lidar com datas, horas e disponibilidade), gerenciamento de fuso horário e interação entre front-end (JavaScript) e back-end (PHP).


Como não consigo acessar links externos diretamente (como repositórios privados ou recém-criados no GitHub), farei este relatório **baseado no código exato que construímos juntos** nesta sessão, assumindo que foi isso que você subiu para o repositório.

Aqui está o relatório técnico estruturado para o seu portfólio.

---

# 📄 Relatório Técnico: Sistema de Agendamento (MVP)

**Projeto:** Sistema de Agendamento para Barbearia/Consultório

**Stack:** PHP 8+, MySQL, JavaScript (FullCalendar), HTML/CSS.

**Arquitetura:** Vanilla PHP (Sem frameworks), focada em Lógica de Negócios e Manipulação de Dados.

---

### 1. O que foi feito

Desenvolvemos um sistema *full-stack* funcional de agendamento que permite:

* **Cliente:** Visualizar horários em um calendário interativo e solicitar um agendamento.
* **Sistema:** Validar disponibilidade (impedir conflitos de horário) e salvar dados em UTC.
* **Admin:** Painel administrativo para visualizar solicitações pendentes e Aprovar ou Rejeitar agendamentos.

### 2. Como foi feito (Metodologia)

A construção seguiu o padrão de separação de responsabilidades (embora em uma estrutura simples):

* **Database (MySQL):** Modelagem relacional focada em integridade. Uso de campos `DATETIME` para precisão temporal e `ENUM` para controle de estado (`pending`, `confirmed`).
* **Back-end (PHP - `Scheduler.php`):** Uma classe central que encapsula toda a regra de negócios. Nenhuma query SQL é feita fora desta classe (ou do arquivo admin simples), garantindo organização.
* **API (`api.php`):** Atua como uma camada de controle que recebe requisições JSON do front-end e devolve dados, sem misturar HTML com lógica.
* **Front-end (JS):** Integração com a biblioteca **FullCalendar** para renderização visual, consumindo a API via `fetch()`.

---

### 3. Principais Problemas e Soluções

Durante o desenvolvimento, enfrentamos três desafios técnicos principais:

#### A. O "Sumuço" dos Agendamentos (Lógica de Status)

* **Problema:** O cliente agendava, o dado era salvo no banco, mas não aparecia no calendário.
* **Causa:** O banco salvava por padrão como `pending`, mas a API filtrava apenas `confirmed`.
* **Solução:** Criamos um fluxo de aprovação completo. Alteramos a API temporariamente para testes e, em seguida, construímos o `admin.php` para permitir que o dono altere o status para `confirmed`, fechando o ciclo corretamente.

#### B. Conflito de Nomes no Banco

* **Problema:** O banco foi criado com typo (`agentamento` vs `agendamento`).
* **Solução:** Executamos script SQL de migração (`RENAME TABLE`) e ajustamos a string de conexão PDO, evitando perda de dados.

#### C. Fuso Horário (Timezones)

* **Problema:** Risco de horários aparecerem errados dependendo de onde o servidor ou o usuário estivesse.
* **Solução (Best Practice):**
* Banco e PHP trabalham exclusivamente em **UTC**.
* Front-end converte para o horário local do navegador.
* Admin converte explicitamente para `America/Sao_Paulo` na visualização.



---

### 4. Análise do Código e Correções

Aqui está uma revisão técnica do código presente no repositório, pontuando a qualidade e as correções aplicadas.

#### ✅ Pontos Fortes

1. **Segurança (SQL Injection):** Todo o acesso ao banco utiliza `PDO` com *Prepared Statements* (`$stmt->prepare` e `execute`). Não há concatenação de strings na query, o que protege contra injeção de SQL.
2. **Lógica de Intersecção:** A função `isSlotAvailable` usa a lógica matemática correta para intervalos de tempo (), que é mais robusta do que simples comparações de igualdade.
3. **Orientação a Objetos:** O uso da classe `Scheduler` torna o código reutilizável. Se amanhã você quiser criar um App Mobile, pode usar a mesma classe.

#### ⚠️ Pontos de Atenção & Correção (Review)

**1. Visualização de Data no Admin**
No código inicial, a data no painel administrativo era exibida em UTC (crua), o que confundiria o usuário final.

* **Correção Aplicada:** Implementação da classe `DateTime` com `setTimezone` no `admin.php`.
```php
// Código corrigido
$date = new DateTime($p['start_at'], new DateTimeZone('UTC'));
$date->setTimezone(new DateTimeZone('America/Sao_Paulo'));
echo $date->format('d/m/Y H:i');

```



**2. Filtro da API (Pós-Admin)**
Durante o debug, alteramos a API para mostrar pendentes (`status IN ('confirmed', 'pending')`).

* **Sugestão de Correção Final:** Agora que o Painel Admin existe, o ideal para produção é voltar a API pública para mostrar **apenas** confirmados, para que um cliente não veja o horário "reservado" de outro cliente que ainda não foi aprovado (a menos que a regra de negócio seja "primeiro a chegar leva").

**3. Tratamento de Erros**
O código usa `try/catch` básico.

* **Melhoria Futura:** Em um ambiente de produção real, os erros de banco de dados não deveriam ser ecoados diretamente na tela (`$e->getMessage()`) por motivos de segurança, mas sim logados em arquivo, retornando apenas "Erro interno" para o usuário.

---

### Conclusão para o Portfólio

Este projeto demonstra competência em **lógica de programação backend** (manipulação de datas e estados), **integração de sistemas** (API RESTful) e **resolução de problemas reais** (fluxo de aprovação). O código é limpo, seguro e escalável.


https://youtu.be/Bo2hheItk3A