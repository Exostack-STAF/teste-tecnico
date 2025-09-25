# 🧪 Desafio Técnico – Desenvolvedor PHP/Laravel

Bem-vindo(a)!  
Este desafio tem como objetivo avaliar **organização de código**, **boas práticas** e **capacidade de tomada de decisão** utilizando as tecnologias solicitadas.

---

## 🎯 Objetivo
Desenvolver um **mini sistema de gerenciamento de pedidos** com as seguintes stacks:

- **PHP 8.1+**
- **Laravel 12**
- **Filament v3**
- **Livewire v3**
- **MySQL**
- **Filas (Queues)**

---

## 📋 Escopo do Projeto

### Entidades
- **Products**
    - Campos: `id`, `name`, `price`, `stock_quantity`.
- **Orders**
    - Campos: `id`, `customer_name`, `status` (`pending`, `paid`, `shipped`), `created_at`.
- **OrderItems**
    - Campos: `id`, `order_id`, `product_id`, `quantity`, `price`.

### Funcionalidades
1. **Cadastro de Produtos**
    - CRUD completo.
    - Validação para impedir estoque negativo.

2. **Criação de Pedidos**
    - Selecionar produtos e quantidade.
    - Atualizar estoque automaticamente.
    - Status inicial: `pending`.

3. **Atualização de Status**
    - Permitir mudar de `pending` → `paid` → `shipped`.
    - Regra: só é possível marcar como `shipped` se estiver `paid`.

4. **Fila de Processamento**
    - Ao mudar status para `paid`, enfileirar um Job que:
        - Simule o envio de um **e-mail de confirmação**.
        - Salve um registro em uma tabela `notifications` (`order_id`, `message`, `created_at`).

5. **Painel Administrativo (Filament + Livewire)**
    - CRUD de Produtos.
    - CRUD de Pedidos com mudança de status.
    - Tela de listagem das notificações.

---

## 🚀 Diferenciais (não obrigatórios)
- Uso de **Events & Listeners** para disparar o job da fila.
- Policies/Gates para controle de permissões.
- Testes automatizados adicionais (Jobs, Components, Policies).

---

## 🕐 Tempo Estimado
Até **8 horas** de desenvolvimento.  
Não é necessário concluir todos os diferenciais, mas é importante manter **qualidade de código** e **organização**.

---

## 🧩 Entrega
1. Crie um repositório **público** no GitHub ou GitLab.
2. Inclua neste repositório:
    - Código completo do projeto.
    - Este README atualizado com:
        - **Passo a passo de instalação e execução**.
        - Observações sobre decisões técnicas.
3. Envie o link do repositório.

---

## ⚡ Instruções de Setup
Inclua aqui (no seu envio) os comandos para rodar o projeto, por exemplo:

```bash
git clone <seu-repositorio>
cd <seu-repositorio>
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
php artisan queue:work

```


## Instalação Rápida

1. Clone o repositório:
   ```bash
   git clone <url-do-repositorio>
   cd teste-tecnico
   ```

2. Instale as dependências:
   ```bash
   composer install
   ```

3. Configure o ambiente:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
4. Configure o banco de dados no arquivo `.env`

5. Execute as migrações com seed:
   ```bash
   php artisan migrate --seed
   ```

6. Execute os testes:
   ```bash
   php artisan test
   ```

7. Inicie o servidor:
   ```bash
   php artisan serve


   8. Execute o worker da fila:

   php artisan queue:work

   ```

## Comandos Importantes

- Migrar banco de dados com seed: `php artisan migrate --seed`
- Executar todos os testes: `php artisan test`
- Executar testes unitários: `php artisan test --testsuite=Unit`
- Executar testes de funcionalidade: `php artisan test --testsuite=Feature`

## Acesso

Após iniciar o servidor, acesse `http://localhost:8000/admin` para acessar o painel administrativo.

 👉 "Se as seeds forem executadas, este login será criado:"

'email' => 'test@example.com',
'password' => 'password123',
