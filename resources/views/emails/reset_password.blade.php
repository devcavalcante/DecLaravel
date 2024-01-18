@component('mail::message')
    # Redefinição de senha

    Olá,

    Você está recebendo este e-mail porque solicitou a redefinição de senha da sua conta no sistema de Gerenciamento de Grupos e Documentos da UFOPA.

    Para redefinir sua senha, clique no link abaixo:

    [Link para redefinição de senha]

    Esse link expirará em 24 horas.

    Se você não solicitou a redefinição de senha, ignore este e-mail.

Atenciosamente,<br>
{{ config('app.name') }}
@endcomponent
