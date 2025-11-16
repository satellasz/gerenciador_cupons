<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Cupons</title>
    <link rel="stylesheet" href="../assets/css/associado.css">
</head>
<body>
    <h2>Meus Cupons</h2>
    <p><a href="home.php">Voltar</a> | <a href="../logout.php">Sair</a></p>

    <form method="GET" action="">
        <label>Filtrar:</label>
        <select name="filtro">
            <option value="ativos" <?php if($filtro==='ativos') echo 'selected'; ?>>Ativos</option>
            <option value="utilizados" <?php if($filtro==='utilizados') echo 'selected'; ?>>Utilizados</option>
            <option value="vencidos" <?php if($filtro==='vencidos') echo 'selected'; ?>>Vencidos</option>
        </select>
        <button type="submit">Aplicar</button>
    </form>

    <?php if (!empty($meus_cupons)): ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>Código Reserva</th>
                <th>Título</th>
                <th>Comércio</th>
                <th>Categoria</th>
                <th>Validade</th>
                <th>Desconto</th>
                <th>Data Reserva</th>
                <th>Status</th>
            </tr>
            <?php foreach ($meus_cupons as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['codigo_reserva']); ?></td>
                    <td><?php echo htmlspecialchars($c['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($c['nome_fantasia']); ?></td>
                    <td><?php echo htmlspecialchars($c['categoria']); ?></td>
                    <td><?php echo $c['data_inicio'] . " até " . $c['data_termino']; ?></td>
                    <td><?php echo $c['percentual_desc'] . "%"; ?></td>
                    <td><?php echo $c['data_reserva']; ?></td>
                    <td>
                        <?php
                            if (!empty($c['data_uso'])) {
                                echo "Utilizado em " . $c['data_uso'];
                            } elseif ($c['data_termino'] < date('Y-m-d')) {
                                echo "Vencido";
                            } else {
                                echo "Ativo";
                            }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum cupom encontrado.</p>
    <?php endif; ?>
</body>
</html>
