# system-pov

Sistema Projeto Oberland JR Vive — Laravel 12 + AdminLTE 3 (PHP 8.4, MySQL 8).

## Módulos

- **Login** com bloqueio de usuários inativos e limite de tentativas
- **Usuários** (somente administradores): perfis *Administrador* e *Operador*
- **Beneficiários**: cadastro das famílias atendidas pelo projeto — identificação (CPF, NIS, nome social),
  contato e endereço (CEP via ViaCEP), composição familiar, situação socioeconômica (moradia, trabalho,
  renda e benefícios), saúde, necessidades e autorização LGPD. Busca por nome/CPF/NIS/bairro e filtro por necessidade.
- **Atendimentos**: histórico de entregas (cesta básica, roupas, higiene, medicamentos…), visitas,
  encaminhamentos e orientações de cada família, com data, quantidade, quem atendeu e até 5 fotos
  (reduzidas no navegador, guardadas em disco privado no volume `pov-storage`). Registro rápido
  pela ficha da família (linha do tempo) ou pela tela de Atendimentos, com filtros por período e tipo.
  Só o autor do registro ou um administrador pode editar/excluir.
- **Ficha em PDF** do beneficiário (DomPDF): dados, composição familiar, situação socioeconômica e
  histórico de atendimentos, opcionalmente com as fotos. Rodapé com aviso de confidencialidade (LGPD).
- **Painel** com famílias ativas, pessoas alcançadas, atendimentos do mês, o que as famílias mais
  precisam e as famílias sem atendimento há mais de 60 dias

Identidade visual (cores, logos e fonte Geist) igual à landing page do projeto: `public/css/pov.css` e `public/img/`.

## Rodando localmente (Docker)

Requer apenas o Docker Desktop.

```bash
docker compose up --build
```

Acesse http://localhost:8000. Na primeira subida o container cria o `.env`, instala as dependências,
gera a `APP_KEY`, roda as migrations e cria o administrador e 30 pessoas de exemplo.
O login do administrador é o definido em `ADMIN_EMAIL` / `ADMIN_PASSWORD` no `.env.example`.

O MySQL fica exposto em `localhost:3307` para ferramentas como DBeaver/HeidiSQL.

## Deploy no Dokploy

1. **Banco**: em *Create Service → Database → MySQL*, crie o banco e anote host interno, usuário, senha e nome.
2. **Aplicação**: *Create Service → Application*, conecte este repositório (branch `main`).
   - *Build Type*: **Dockerfile** (caminho `Dockerfile`)
3. **Environment** da aplicação:

   ```env
   APP_NAME="Oberland JR Vive"
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:...            # gere uma e não mude mais (veja abaixo)
   APP_URL=https://system.projetoberlandjrvive.online
   APP_LOCALE=pt-br
   SESSION_SECURE_COOKIE=true
   LOG_CHANNEL=stderr

   DB_CONNECTION=mysql
   DB_HOST=<host interno do MySQL no Dokploy>
   DB_PORT=3306
   DB_DATABASE=<banco>
   DB_USERNAME=<usuário>
   DB_PASSWORD=<senha>

   SESSION_DRIVER=database
   CACHE_STORE=database
   QUEUE_CONNECTION=sync

   ADMIN_EMAIL=seu-email@dominio.com.br
   ADMIN_PASSWORD=<senha forte>
   ```

   Para gerar a `APP_KEY` sem PHP instalado:

   ```bash
   docker run --rm php:8.4-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
   ```

4. **DNS**: no painel do domínio `projetoberlandjrvive.online`, crie um registro **A** com nome `system`
   apontando para o IP do servidor do Dokploy.
5. **Domains** (na aplicação): host `system.projetoberlandjrvive.online`, path `/`, **porta 80**,
   HTTPS ligado com certificado **Let's Encrypt**.
6. **Deploy**. A cada start o container roda `migrate --force`, o seeder (que só cria o admin se ele
   ainda não existir) e faz cache de config/rotas/views.

> Depois do primeiro acesso, troque a senha do admin pela tela de Usuários.
> A variável `ADMIN_PASSWORD` só é usada para criar o usuário inicial.

## Estrutura principal

| Caminho | Conteúdo |
|---|---|
| `app/Http/Controllers` | Login, Dashboard, Pessoas, Usuários |
| `app/Http/Requests` | Validações dos formulários |
| `app/Rules/Cpf.php` | Validação de CPF |
| `config/adminlte.php` | Título, logo e **menu lateral** |
| `resources/views` | Telas Blade (estendem `adminlte::page`) |
| `docker/` | nginx, php.ini, supervisor e entrypoint |
