<!DOCTYPE html>
<html>
<head>
    <title>Nouveau message de contact</title>
</head>
<body>
    <h1>Nouveau message de contact</h1>
    <p><strong>Nom :</strong> {{ $data['name'] }}</p>
    <p><strong>Email :</strong> {{ $data['email'] }}</p>
    <p><strong>Sujet :</strong> {{ $data['subject'] }}</p>
    <p><strong>Message :</strong></p>
    <p>{{ $data['message'] }}</p>
</body>
</html>