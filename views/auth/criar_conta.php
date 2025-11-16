<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Conta</title>
    <link rel="stylesheet" href="../views/assets/css/comerciante.css">
    <style>
        .form-section { display: none; margin-top: 15px; }
        .form-section.active { display: block; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>Criar Conta</h2>
    <p><a href="index.php">Voltar ao Login</a></p>

    <?php if (!empty($msg)): ?>
        <p class="<?php echo strpos($msg, 'sucesso') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($msg); ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="perfil">Você é:</label>
        <select id="perfil" name="perfil" required>
            <option value="">Selecione...</option>
            <option value="associado">Associado</option>
            <option value="comerciante">Comerciante</option>
        </select>

        <!-- Associado -->
        <div id="form-associado" class="form-section">
            <h3>Dados do Associado</h3>
            <input type="text" id="cpf" name="cpf" placeholder="CPF"><br>
            <input type="text" id="nome" name="nome" placeholder="Nome"><br>
            <input type="date" id="data_nascimento" name="data_nascimento"><br>
            <input type="text" id="celular" name="celular" placeholder="Celular"><br>
            <input type="email" id="email_associado" name="email" placeholder="E-mail"><br>
            <input type="text" id="endereco_associado" name="endereco" placeholder="Endereço"><br>
            <input type="text" id="bairro_associado" name="bairro" placeholder="Bairro"><br>
            <input type="text" id="cidade_associado" name="cidade" placeholder="Cidade"><br>
            <input type="text" id="uf_associado" name="uf" placeholder="UF" maxlength="2"><br>
            <input type="text" id="cep_associado" name="cep" placeholder="CEP"><br>
        </div>

        <!-- Comerciante -->
        <div id="form-comerciante" class="form-section">
            <h3>Dados do Comércio</h3>
            <input type="text" id="cnpj" name="cnpj" placeholder="CNPJ"><br>
            <input type="text" id="razao_social" name="razao_social" placeholder="Razão Social"><br>
            <input type="text" id="nome_fantasia" name="nome_fantasia" placeholder="Nome Fantasia"><br>
            <input type="email" id="email_comercio" name="email" placeholder="E-mail"><br>
            <input type="text" id="contato" name="contato" placeholder="Contato"><br>
            <input type="text" id="endereco_comercio" name="endereco" placeholder="Endereço"><br>
            <input type="text" id="bairro_comercio" name="bairro" placeholder="Bairro"><br>
            <input type="text" id="cidade_comercio" name="cidade" placeholder="Cidade"><br>
            <input type="text" id="uf_comercio" name="uf" placeholder="UF" maxlength="2"><br>
            <input type="text" id="cep_comercio" name="cep" placeholder="CEP"><br>
            <select id="categoria_id" name="categoria_id">
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>">
                        <?php echo htmlspecialchars($cat['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

        </div>

        <!-- campos comuns -->
        <div id="form-comum" class="form-section">
            <h3>Dados de Acesso</h3>
            <input type="password" id="senha" name="senha" placeholder="Senha"><br>
            <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirmar Senha"
             required oninput="this.setCustomValidity(this.value !== document.getElementById('senha').value ? 'As senhas não conferem' : '')">
            <br>
            <button type="submit">Criar Conta</button>
        </div>
        <br>

    </form>

    <script>
        const perfilSelect = document.getElementById('perfil');
        const formAssociado = document.getElementById('form-associado');
        const formComerciante = document.getElementById('form-comerciante');
        const formComum = document.getElementById('form-comum');

        function toggleRequired(section, enable) {
            section.querySelectorAll('input, select').forEach(el => {
                if (enable) {
                    el.removeAttribute('disabled');
                    el.setAttribute('required', 'required');
                } else {
                    el.setAttribute('disabled', 'disabled');
                    el.removeAttribute('required');
                }
            });
        }

        perfilSelect.addEventListener('change', function () {
            // esconde todos
            formAssociado.classList.remove('active');
            formComerciante.classList.remove('active');
            formComum.classList.remove('active');

            // desliga todos
            toggleRequired(formAssociado, false);
            toggleRequired(formComerciante, false);
            toggleRequired(formComum, false);

            if (this.value === 'associado') {
                formAssociado.classList.add('active');
                formComum.classList.add('active');
                toggleRequired(formAssociado, true);
                toggleRequired(formComum, true);
            } else if (this.value === 'comerciante') {
                formComerciante.classList.add('active');
                formComum.classList.add('active');
                toggleRequired(formComerciante, true);
                toggleRequired(formComum, true);
            }
        });
    </script>


</body>
</html>
