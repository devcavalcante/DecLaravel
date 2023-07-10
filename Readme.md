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
  composer install
```

Subindo as migrations

```bash
  php artisan migrate
