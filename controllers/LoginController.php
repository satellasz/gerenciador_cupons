<?php
class LoginController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function login() {
        $erro = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim($_POST['login']);
            $senha = trim($_POST['senha']);

            // verifica se é cpf ou cnpj (simplicado por enquanto, mudar)
            $isCpf = strlen(preg_replace('/\D/', '', $login)) <= 11;

            if ($isCpf) {
                $stmt = $this->conn->prepare("SELECT cpf, senha_hash FROM associado WHERE cpf = ?");
            } else {
                $stmt = $this->conn->prepare("SELECT cnpj, senha_hash FROM comercio WHERE cnpj = ?");
            }

            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                if (password_verify($senha, $row['senha_hash'])) {
                    // echo $row;
                    if ($isCpf) {
                        $_SESSION['perfil'] = 'associado';
                        $_SESSION['id'] = $row['cpf'];
                        header("Location: ../public/associado/home.php");
                        exit;
                    } else {
                        $_SESSION['perfil'] = 'comerciante';
                        $_SESSION['id'] = $row['cnpj'];
                        header("Location: .../../../public/comercio/home.php");
                        exit;
                    }
                } else {
                    $erro = "Senha incorreta.";
                }
            } else {
                $erro = "Usuário não encontrado.";
            }
        }


        //chama a view
        include '../views/auth/login.php';
    }
}
