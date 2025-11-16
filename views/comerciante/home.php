<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Home Comerciante</title>
    <link rel="stylesheet" href="../assets/css/comerciante.css">
</head>
<body>
    <h2>Bem-vindo, Comerciante</h2>
    <p>
        <a href="cadastrar_cupom.php">Cadastrar Novo Cupom</a> | 
        <a href="validar_cupom.php">Validar Cupom</a> | 
        <a href="../logout.php">Sair</a>
    </p>

    <?php if (!empty($_GET['msg'])): ?>
        <p style="color:green;"><?php echo htmlspecialchars($_GET['msg']); ?></p>
    <?php endif; ?>
    <?php if (!empty($_GET['err'])): ?>
        <p style="color:red;"><?php echo htmlspecialchars($_GET['err']); ?></p>
    <?php endif; ?>

    <h3>Filtrar e Pesquisar Cupons</h3>
    <form method="GET" action="">
        <select name="filtro">
            <option value="" <?php if(empty($filtro)) echo 'selected'; ?>>Todos</option>
            <option value="ativos" <?php if($filtro==='ativos') echo 'selected'; ?>>Ativos</option>
            <option value="utilizados" <?php if($filtro==='utilizados') echo 'selected'; ?>>Utilizados</option>
            <option value="vencidos" <?php if($filtro==='vencidos') echo 'selected'; ?>>Vencidos</option>
        </select>
        <input type="text" name="q" placeholder="Pesquisar por título ou número" 
               value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
        <button type="submit">Aplicar</button>
    </form>

    <h3>Meus Cupons</h3>
    <?php if (!empty($cupons)): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Número</th>
                <th>Título</th>
                <th>Validade</th>
                <th>Desconto</th>
                <th>Quantidade disponível</th>
                <th>Status</th>
            </tr>
            <?php foreach ($cupons as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['num_cupom']); ?></td>
                    <td><?php echo htmlspecialchars($c['titulo']); ?></td>
                    <td><?php echo $c['data_inicio'] . " até " . $c['data_termino']; ?></td>
                    <td><?php echo $c['percentual_desc'] . "%"; ?></td>
                    <td><?php echo (int)$c['quantidade']; ?></td>
                    <td>
                        <?php
                            $hoje = date('Y-m-d');
                            if ($hoje < $c['data_inicio']) {
                                echo "Agendado";
                            } elseif ($hoje > $c['data_termino']) {
                                echo "Vencido";
                            } elseif ((int)$c['quantidade'] <= 0) {
                                echo "Esgotado";
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
