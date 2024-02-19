@component('mail::message')
# Bem-vindo ao Grupo de Trabalho na UFOPA!

Olá,

Você foi adicionado a um novo grupo de trabalho como {{$role}} no sistema de Gerenciamento de Grupos e Documentos da UFOPA.

**Se você já possui cadastro no SIGAA, por favor, faça login utilizando suas credenciais do SIGAA para acessar a plataforma. Caso contrário, clique no link abaixo para criar sua conta com o mesmo e-mail que foi adicionado ao grupo:**

[Crie Sua Conta Agora](http://localhost:8001/api/register)

**Ao explorar a plataforma, você terá acesso a informações exclusivas e recursos relacionados ao grupo de trabalho.**

Atenciosamente,<br>
{{ config('app.name') }}
@endcomponent
