<?php
class CriarContaController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function criarConta() {
        $msg = null;
        $categorias = [];

        // busca categorias no banco
        $res = $this->conn->query("SELECT id, nome FROM categoria ORDER BY nome");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $categorias[] = $row;
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $perfil = $_POST['perfil'];
            $senha = $_POST['senha'];
            $confirmar = $_POST['confirmar_senha'];

            if ($senha !== $confirmar) {
                $msg = "As senhas não conferem.";
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                if ($perfil === 'associado') {
                    $cpf = $_POST['cpf'];
                    $nome = $_POST['nome'];
                    $data_nascimento = $_POST['data_nascimento'];
                    $celular = $_POST['celular'];
                    $email = $_POST['email'];
                    $endereco = $_POST['endereco'];
                    $bairro = $_POST['bairro'];
                    $cidade = $_POST['cidade'];
                    $uf = $_POST['uf'];
                    $cep = $_POST['cep'];

                    $stmt = $this->conn->prepare(
                        "INSERT INTO associado (cpf, nome, data_nascimento, celular, email, senha_hash, endereco, bairro, cidade, uf, cep)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                    );
                    $stmt->bind_param("sssssssssss", $cpf, $nome, $data_nascimento, $celular, $email, $senha_hash, $endereco, $bairro, $cidade, $uf, $cep);

                    if ($stmt->execute()) {
                        $msg = "Conta de associado criada com sucesso!";
                    } else {
                        $msg = "Erro ao criar conta de associado: " . $this->conn->error;
                    }

                } elseif ($perfil === 'comerciante') {
                    $cnpj = $_POST['cnpj'];
                    $razao_social = $_POST['razao_social'];
                    $nome_fantasia = $_POST['nome_fantasia'];
                    $email = $_POST['email'];
                    $contato = $_POST['contato'];
                    $endereco = $_POST['endereco'];
                    $bairro = $_POST['bairro'];
                    $cidade = $_POST['cidade'];
                    $uf = $_POST['uf'];
                    $cep = $_POST['cep'];
                    $categoria_id = intval($_POST['categoria_id']);

                    $stmt = $this->conn->prepare(
                        "INSERT INTO comercio (cnpj, razao_social, nome_fantasia, email, senha_hash, contato, endereco, bairro, cidade, uf, cep, categoria_id)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                    );
                    $stmt->bind_param("sssssssssssi", $cnpj, $razao_social, $nome_fantasia, $email, $senha_hash, $contato, $endereco, $bairro, $cidade, $uf, $cep, $categoria_id);

                    if ($stmt->execute()) {
                        $msg = "Conta de comerciante criada com sucesso!";
                    } else {
                        $msg = "Erro ao criar conta de comerciante: " . $this->conn->error;
                    }
                } else {
                    $msg = "Perfil inválido.";
                }
            }
        }

        include __DIR__ . '/../views/auth/criar_conta.php';
    }
}
