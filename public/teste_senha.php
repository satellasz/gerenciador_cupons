<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerador de Hash</title>
</head>
<body>
    <h2>Gerar Hash de Senha</h2>
    <form method="post">
        <input type="text" name="senha" placeholder="Digite a senha" required>
        <button type="submit">Gerar Hash</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $senha = $_POST["senha"];
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        echo "<p><strong>Senha original:</strong> " . htmlspecialchars($senha) . "</p>";
        echo "<p><strong>Hash gerado:</strong> " . $senha_hash . "</p>";
    }
    ?>
</body>
</html>
