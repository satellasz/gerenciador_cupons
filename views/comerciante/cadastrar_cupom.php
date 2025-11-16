<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Cupom</title>
    <link rel="stylesheet" href="../assets/css/comerciante.css">
</head>
<body>
    <h2>Cadastrar Novo Cupom</h2>
    <p><a href="home.php">Voltar</a> | <a href="../logout.php">Sair</a></p>

    <?php if (!empty($msg)): ?>
        <p style="color:green;"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>

    <form method="POST" action="../../public/comercio/cadastrar_cupom.php">
        <label for="titulo">Título da promoção:</label><br>
        <input type="text" id="titulo" name="titulo" required maxlength="80"><br><br>

        <label for="data_inicio">Data de início:</label><br>
        <input type="date" id="data_inicio" name="data_inicio" required><br><br>

        <label for="data_termino">Data de término:</label><br>
        <input type="date" id="data_termino" name="data_termino" required><br><br>

        <label for="percentual_desc">Percentual de desconto (%):</label><br>
        <input type="number" id="percentual_desc" name="percentual_desc" step="0.01" min="1" max="100" required><br><br>

        <label for="quantidade">Quantidade de cupons:</label><br>
        <input type="number" id="quantidade" name="quantidade" min="1" required><br><br>

        <button type="submit">Cadastrar Cupom</button>
    </form>

</body>
</html>
