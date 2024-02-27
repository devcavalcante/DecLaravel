<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Informações do Grupo</title>
<style>
    .attributes-container {
        display: inline-block;
        margin-bottom: 20px; /* Espaçamento abaixo da linha */
        border-bottom: 1px solid #ccc; /* Adiciona uma linha na parte inferior */
        padding-bottom: 10px; /* Espaçamento abaixo da linha */
    }
    .attribute {
        display: inline-block;
        margin-right: 20px;
    }
    .attribute p {
        margin: 0;
        padding-bottom: 5px; /* Adiciona espaço entre a informação e o valor */
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }
    th {
        background-color: #f2f2f2;
    }
</style>
</head>
<body>

<div class="attributes-container">
    <h2>Informações do Grupo</h2>
    <div class="attribute">
        <p><strong>Nome do Gerente:</strong></p>
        <p>{{ $manager }}</p>
    </div>
    <div class="attribute">
        <p><strong>Email do Representante:</strong></p>
        <p> {{ $representative }} </p>
    </div>
    <div class="attribute">
        <p><strong>Quantidade de Membros:</strong></p>
        <p>{{ $membersCount }}</p>
    </div>
</div>
<table>
    <tr>
        <th>Nome do Grupo</th>
        <td>{{ $typeGroup->name }}</td>
    </tr>
    <tr>
        <th>Tipo de Grupo</th>
        <td>{{ $typeGroup->type_group }}</td>
    </tr>
    <tr>
        <th>Entidade</th>
        <td>{{ $group->entity }}</td>
    </tr>
    <tr>
        <th>Organização</th>
        <td>{{ $group->organ }}</td>
    </tr>
    <tr>
        <th>Conselho</th>
        <td>{{ $group->council}}</td>
    </tr>
    <tr>
        <th>SIGLA</th>
        <td>{{ $group->acronym }}</td>
    </tr>
    <tr>
        <th>Time</th>
        <td>{{ $group->team }}</td>
    </tr>
    <tr>
        <th>Unidade</th>
        <td>{{ $group->unit }}</td>
    </tr>
    <tr>
        <th>Email</th>
        <td>{{ $group->email }}</td>
    </tr>
    <tr>
        <th>Oficio Solicitado</th>
        <td>{{ $group->office_requested }}</td>
    </tr>
    <tr>
        <th>Escritório Indicado</th>
        <td>{{ $group->office_indicated }}</td>
    </tr>
    <tr>
        <th>Conselho Interno</th>
        <td>{{ $group->internal_concierge }}</td>
    </tr>
    <tr>
        <th>Observações</th>
        <td>{{ $group->observations }}</td>
    </tr>
    <tr>
        <th>Status</th>
        <td>{{ $group->status }}</td>
    </tr>
</table>

<h2>Informações dos Membros do Grupo</h2>
<table>
@foreach ($members as $member)
    <tr>
        <td>{{ $member->name }}</td>
        <td>{{ $member->email }}</td>
        <td>{{ $member->role }}</td>
        <td>{{ $member->phone }}</td>
        <td>{{ $member->entry_date->format('d-m-Y') }}</td>
        <td>{{ $member->departure_date->format('d-m-Y') }}</td>
    </tr>
    @endforeach
</table>

</body>
</html>
