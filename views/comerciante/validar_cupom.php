<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Validar Cupom</title>
    <link rel="stylesheet" href="../assets/css/comerciante.css">
</head>
<body>
    <h2>Validar Cupom de Associado</h2>
    <p><a href="home.php">Voltar</a> | <a href="../logout.php">Sair</a></p>

    <!-- formulário de busca por código -->
    <form id="formBusca" method="GET" action="../../public/comercio/validar_cupom.php">
        <label for="codigo">Digite o código de reserva apresentado pelo associado:</label><br>
        <input type="text" id="codigo" name="codigo" required
               value="<?php echo isset($_GET['codigo']) ? htmlspecialchars($_GET['codigo']) : ''; ?>">
        <button type="submit">Buscar</button>
    </form>

    <!-- bloco de resultado sendo atualizado com ajax -->
    <div id="resultado">
        <?php if (!empty($msg)): ?>
            <p style="color:<?php echo (strpos($msg, 'sucesso') !== false) ? 'green' : 'red'; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($cupom)): ?>
            <h3>Detalhes do Cupom</h3>
            <table border="1" cellpadding="5" cellspacing="0">
                <tr><th>Código Reserva</th><td><?php echo htmlspecialchars($cupom['codigo_reserva']); ?></td></tr>
                <tr><th>Título</th><td><?php echo htmlspecialchars($cupom['titulo']); ?></td></tr>
                <tr><th>Associado</th><td><?php echo htmlspecialchars($cupom['nome']); ?></td></tr>
                <tr><th>Comércio</th><td><?php echo htmlspecialchars($cupom['nome_fantasia']); ?></td></tr>
                <tr><th>Validade</th><td><?php echo $cupom['data_inicio'] . " até " . $cupom['data_termino']; ?></td></tr>
                <tr><th>Data da Reserva</th><td><?php echo $cupom['data_reserva']; ?></td></tr>
                <tr><th>Status</th>
                    <td>
                        <?php
                            if (!empty($cupom['data_uso'])) {
                                echo "Já utilizado em " . $cupom['data_uso'];
                            } elseif ($cupom['data_termino'] < date('Y-m-d')) {
                                echo "Vencido";
                            } else {
                                echo "Ativo";
                            }
                        ?>
                    </td>
                </tr>
            </table>

            <?php if (empty($cupom['data_uso']) && $cupom['data_termino'] >= date('Y-m-d')): ?>
                <form id="formValidar" method="POST" action="../../public/comercio/validar_cupom.php">
                    <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($cupom['codigo_reserva']); ?>">
                    <button type="submit">Confirmar Uso</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
    // ajax para busca
    document.getElementById('formBusca').addEventListener('submit', function(e) {
        e.preventDefault();
        const codigo = document.getElementById('codigo').value;

        fetch(this.action + '?codigo=' + encodeURIComponent(codigo))
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const resultado = doc.querySelector('#resultado');
                document.getElementById('resultado').innerHTML = resultado.innerHTML;
                bindFormValidar(); // reativa ajax no form de validação
            });
    });

    // função para ativar ajax no form de validação
    function bindFormValidar() {
        const formValidar = document.getElementById('formValidar');
        if (formValidar) {
            formValidar.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const resultado = doc.querySelector('#resultado');
                    document.getElementById('resultado').innerHTML = resultado.innerHTML;
                    bindFormValidar(); // reativa caso o botão apareça de novo
                });
            });
        }
    }

    //ativa no carregamento inicial
    bindFormValidar();
    </script>
</body>
</html>
