# Sobre o projeto

API criada em Laravel responsável por gerenciar documentos e comissões criadas pela pró-reitoria da UFOPA.



## Requisitos
É necessário ter o Docker instalado na máquina para conseguir buildar o projeto.
## Rodando localmente

Clone o projeto

```bash
  git clone https://github.com/hiki-1/DecLaravel.git
```

Entre no diretório do projeto

```bash
  cd DecLaravel
```

Copie a env
```bash
  cp .env.example .env
```

Construção das imagens

```bash
  docker-compose build
```

Inicie o serviço

```bash
  docker-compose up -d
```

Instalando as dependências

```bash
  docker exec -it plataforma.dev composer install
```

Subindo as migrations

```bash
  docker exec -it plataforma.dev php artisan migrate
```

Criando chaves de acesso

```bash
  docker exec -it plataforma.dev php artisan passport:install
```

Gerando documentação

```bash
  docker exec -it plataforma.dev composer swagger
```
# Acessando com outro banco
Basta mudar as infomações da env.example que atualmente usam postgres mas se for necessario trocar ou acessar com outro banco:
```bash
    DB_CONNECTION=pgsql
    DB_HOST=plataforma.db
    DB_PORT=5432
    DB_DATABASE=plataform
    DB_USERNAME=postgres
    DB_PASSWORD=postgres
```

