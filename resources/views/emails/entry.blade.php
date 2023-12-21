@component('mail::message')
# Bem-vindo ao Grupo de Trabalho na UFOPA!

Olá,

Você foi adicionado a um novo grupo de trabalho como {{$role}} no sistema de Gerenciamento de Grupos e Documentos da UFOPA.

**Ao explorar a plataforma, você terá acesso a informações exclusivas e recursos relacionados ao grupo de trabalho.**

Atenciosamente,<br>
{{ config('app.name') }}
@endcomponent
