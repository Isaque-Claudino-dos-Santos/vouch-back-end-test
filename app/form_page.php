<?php
/** @var string|null $message */
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta
            name="viewport"
            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    >
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chat</title>
</head>
<body>

<form method="POST" action="/send">
    <label>
        <input type="text" name="message" id="message"/>
    </label>
    <button type="submit">SEND</button>
</form>


<div><?= $message ?? 'not found' ?></div>
</body>
</html>