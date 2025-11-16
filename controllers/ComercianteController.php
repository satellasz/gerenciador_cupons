<?php
class ComercianteController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // home
    public function home() {
        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'comerciante') {
            header("Location: ../index.php");
            exit;
        }

        $cnpj = $_SESSION['id'];
        $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : '';
        $busca = isset($_GET['q']) ? "%".$_GET['q']."%" : null;

        $sql = "SELECT * FROM cupom WHERE comercio_cnpj = ?";
        $params = [$cnpj];
        $types = "s";

        if ($busca) {
            $sql .= " AND (titulo LIKE ? OR num_cupom LIKE ?)";
            $params[] = $busca;
            $params[] = $busca;
            $types .= "ss";
        }

        if ($filtro === 'ativos') {
            $sql .= " AND CURDATE() BETWEEN data_inicio AND data_termino AND quantidade > 0";
        } elseif ($filtro === 'utilizados') {
            $sql .= " AND num_cupom IN (SELECT num_cupom FROM associado_cupom WHERE data_uso IS NOT NULL)";
        } elseif ($filtro === 'vencidos') {
            $sql .= " AND data_termino < CURDATE()";
        }

        $sql .= " ORDER BY data_inicio DESC, titulo";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $cupons = [];
        while ($row = $result->fetch_assoc()) {
            $cupons[] = $row;
        }

        include __DIR__ . '/../views/comerciante/home.php';
    }


    public function cadastrarCupom() {
        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'comerciante') {
            header("Location: ../index.php");
            exit;
        }

        $msg = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = $_POST['titulo'];
            $data_inicio = $_POST['data_inicio'];
            $data_termino = $_POST['data_termino'];

            //teste
            //var_dump($_POST['data_inicio'], $_POST['data_termino']);
            //exit;

            try {
                $dt_inicio = new DateTime($data_inicio);
                $dt_termino = new DateTime($data_termino);
                $data_inicio = $dt_inicio->format('Y-m-d');
                $data_termino = $dt_termino->format('Y-m-d');
            } catch (Exception $e) {
                $msg = "Datas inválidas.";
            }

            $percentual = floatval($_POST['percentual_desc']);
            $quantidade = intval($_POST['quantidade']);
            $cnpj = $_SESSION['id']; // comerciante logado

            if ($data_inicio <= $data_termino && $data_inicio >= date('Y-m-d')) {
                // gera código hash de 12 caracteres
                $num_cupom = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 12));

                $stmt = $this->conn->prepare(
                    "INSERT INTO cupom (num_cupom, comercio_cnpj, titulo, data_emissao, data_inicio, data_termino, percentual_desc, quantidade)
                     VALUES (?, ?, ?, CURDATE(), ?, ?, ?, ?)"
                );
                $stmt->bind_param("sssssdi", $num_cupom, $cnpj, $titulo, $data_inicio, $data_termino, $percentual, $quantidade);


                if ($stmt->execute()) {
                    $msg = "Cupom cadastrado com sucesso!";
                } else {
                    $msg = "Erro ao cadastrar cupom: " . $this->conn->error;
                }
            } else {
                $msg = "Período inválido. Data início deve ser hoje ou futura e anterior à data término.";
            }
        }

        include __DIR__ . '/../views/comerciante/cadastrar_cupom.php';
    }

    // lista de cupons do comerciante
    public function meusCupons() {
        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'comerciante') {
            header("Location: ../index.php");
            exit;
        }

        $cnpj = $_SESSION['id'];
        $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : '';
        $busca = isset($_GET['q']) ? "%".$_GET['q']."%" : null;

        $sql = "SELECT * FROM cupom WHERE comercio_cnpj = ?";
        $params = [$cnpj];
        $types = "s";

        if ($busca) {
            $sql .= " AND (titulo LIKE ? OR num_cupom LIKE ?)";
            $params[] = $busca;
            $params[] = $busca;
            $types .= "ss";
        }

        if ($filtro === 'ativos') {
            $sql .= " AND CURDATE() BETWEEN data_inicio AND data_termino AND quantidade > 0";
        } elseif ($filtro === 'utilizados') {
            $sql .= " AND num_cupom IN (SELECT num_cupom FROM associado_cupom WHERE data_uso IS NOT NULL)";
        } elseif ($filtro === 'vencidos') {
            $sql .= " AND data_termino < CURDATE()";
        }

        $sql .= " ORDER BY data_inicio DESC, titulo";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $cupons = [];
        while ($row = $result->fetch_assoc()) {
            $cupons[] = $row;
        }

        include __DIR__ . '/../views/comerciante/meus_cupons.php';
    }

    public function validarCupom() {
        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'comerciante') {
            header("Location: ../index.php");
            exit;
        }

        $msg = null;
        $cupom = null;

        if (isset($_GET['codigo'])) {
            $codigo = $_GET['codigo'];

            $stmt = $this->conn->prepare(
                "SELECT ac.*, a.nome, c.titulo, c.data_inicio, c.data_termino, com.nome_fantasia
                FROM associado_cupom ac
                JOIN associado a ON ac.associado_cpf = a.cpf
                JOIN cupom c ON ac.num_cupom = c.num_cupom
                JOIN comercio com ON c.comercio_cnpj = com.cnpj
                WHERE ac.codigo_reserva = ? AND com.cnpj = ?"
            );
            $stmt->bind_param("ss", $codigo, $_SESSION['id']);
            $stmt->execute();
            $res = $stmt->get_result();
            $cupom = $res->fetch_assoc();

            if (!$cupom) {
                $msg = "Cupom não encontrado ou não pertence a este comércio.";
            } else {
                // avalia o status
                if (!empty($cupom['data_uso'])) {
                    $msg = "Este cupom já foi utilizado em " . $cupom['data_uso'] . ".";
                } elseif ($cupom['data_termino'] < date('Y-m-d')) {
                    $msg = "Este cupom venceu em " . $cupom['data_termino'] . ".";
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
            $codigo = $_POST['codigo'];

            // só tenta validar se ainda não tiver mensagem de erro
            if (!$msg) {
                $upd = $this->conn->prepare("
                    UPDATE associado_cupom ac
                    JOIN cupom c ON ac.num_cupom = c.num_cupom
                    SET ac.data_uso = CURDATE()
                    WHERE ac.codigo_reserva = ?
                    AND ac.data_uso IS NULL
                    AND CURDATE() BETWEEN c.data_inicio AND c.data_termino
                ");
                $upd->bind_param("s", $codigo);
                if ($upd->execute() && $upd->affected_rows > 0) {
                    $msg = "Cupom validado com sucesso!";
                } else {
                    $msg = "Não foi possível validar o cupom. Verifique se já foi usado ou se está vencido.";
                }
            }
        }

        include __DIR__ . '/../views/comerciante/validar_cupom.php';
    }

}
