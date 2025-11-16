<?php
class AssociadoController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function home() {
        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'associado') {
            header("Location: ../index.php");
            exit;
        }

        $cpf = $_SESSION['id'];
        $categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;

        // Carregar categorias
        $categorias = [];
        $resCat = $this->conn->query("SELECT id, nome FROM categoria ORDER BY nome");
        while ($row = $resCat->fetch_assoc()) {
            $categorias[] = $row;
        }

        // query
        $sql = "SELECT c.num_cupom, c.titulo, c.data_inicio, c.data_termino, c.percentual_desc,
                    com.nome_fantasia, cat.nome AS categoria, c.quantidade
                FROM cupom c
                JOIN comercio com ON c.comercio_cnpj = com.cnpj
                JOIN categoria cat ON com.categoria_id = cat.id
                WHERE CURDATE() BETWEEN c.data_inicio AND c.data_termino
                AND c.quantidade > 0
                AND c.num_cupom NOT IN (
                    SELECT num_cupom FROM associado_cupom WHERE associado_cpf = ?
                )";

        // se tiver filtro de categoria, adiciona mais um parâmetro
        $params = ["s", $cpf];
        if ($categoria_id > 0) {
            $sql .= " AND com.categoria_id = ?";
            $params[0] .= "i";
            $params[] = $categoria_id;
        }

        $sql .= " ORDER BY c.data_inicio DESC, c.titulo";

        // roda a query
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(...$params);
        $stmt->execute();
        $resCupom = $stmt->get_result();

        $cupons = [];
        while ($row = $resCupom->fetch_assoc()) {
            $cupons[] = $row;
        }

        include __DIR__ . '/../views/associado/home.php';
    }


    public function reservar() {
        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'associado') {
            header("Location: ../index.php");
            exit;
        }

        $cpf = $_SESSION['id'];
        $msg = "Operação inválida.";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['num_cupom'])) {
            $num_cupom = $_POST['num_cupom'];

            // verifica se o associado ja reservou esse cupom
            $check = $this->conn->prepare("SELECT id FROM associado_cupom WHERE num_cupom = ? AND associado_cpf = ?");
            $check->bind_param("ss", $num_cupom, $cpf);
            $check->execute();
            $res = $check->get_result();

            if ($res->num_rows == 0) {
                // ve se ainda ha quantidade disponivel
                $q = $this->conn->prepare("SELECT quantidade FROM cupom WHERE num_cupom = ?");
                $q->bind_param("s", $num_cupom);
                $q->execute();
                $cupom = $q->get_result()->fetch_assoc();

                if ($cupom && $cupom['quantidade'] > 0) {
                    // gera código de reserva
                    do {
                        $codigo_reserva = strtoupper(bin2hex(random_bytes(4)));
                        $stmt = $this->conn->prepare(
                            "INSERT INTO associado_cupom (num_cupom, associado_cpf, codigo_reserva, data_reserva) 
                             VALUES (?, ?, ?, CURDATE())"
                        );
                        $stmt->bind_param("sss", $num_cupom, $cpf, $codigo_reserva);
                        $ok = $stmt->execute();
                    } while (!$ok && $this->conn->errno == 1062); // 1062 é codigo de entrada duplicada

                    if ($ok) {
                        // diminui a quantidade
                        $upd = $this->conn->prepare("UPDATE cupom SET quantidade = quantidade - 1 WHERE num_cupom = ?");
                        $upd->bind_param("s", $num_cupom);
                        $upd->execute();

                        $msg = "Cupom reservado com sucesso! Código: " . $codigo_reserva;
                    } else {
                        $msg = "Erro ao reservar cupom.";
                    }
                } else {
                    $msg = "Cupom esgotado.";
                }
            } else {
                $msg = "Você já reservou este cupom.";
            }
        }

        header("Location: home.php?msg=" . urlencode($msg));
        exit;
    }

    public function meusCupons() {
        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'associado') {
            header("Location: ../index.php");
            exit;
        }

        $cpf = $_SESSION['id'];
        $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'ativos';

        $sql = "SELECT ac.id, ac.data_reserva, ac.data_uso, ac.codigo_reserva,
                    c.num_cupom, c.titulo, c.data_inicio, c.data_termino, 
                    c.percentual_desc, com.nome_fantasia, cat.nome AS categoria
                FROM associado_cupom ac
                JOIN cupom c ON ac.num_cupom = c.num_cupom
                JOIN comercio com ON c.comercio_cnpj = com.cnpj
                JOIN categoria cat ON com.categoria_id = cat.id
                WHERE ac.associado_cpf = ?";

        if ($filtro === 'ativos') {
            $sql .= " AND ac.data_uso IS NULL AND CURDATE() BETWEEN c.data_inicio AND c.data_termino";
        } elseif ($filtro === 'utilizados') {
            $sql .= " AND ac.data_uso IS NOT NULL";
        } elseif ($filtro === 'vencidos') {
            $sql .= " AND ac.data_uso IS NULL AND c.data_termino < CURDATE()";
        }

        $sql .= " ORDER BY c.data_inicio DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $cpf);
        $stmt->execute();
        $result = $stmt->get_result();

        $meus_cupons = [];
        while ($row = $result->fetch_assoc()) {
            $meus_cupons[] = $row;
        }

        include __DIR__ . '/../views/associado/meus_cupons.php';
    }
}
