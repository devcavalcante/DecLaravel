@component('mail::message')
    # Verificação de email

    Seu usuário foi criado no sistema de Gerenciamento de comissões e documentos
    Seu codigo de 6 digitos para criação da senha: {{$pin}}

    Obrigada,<br>
    {{ config('app.name') }}
@endcomponent
