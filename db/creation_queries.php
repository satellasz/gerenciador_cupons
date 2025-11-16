<?php
include_once '../db/db_connection.php';

// QUERIES DE CRIAÇÃO E INSERTS
$queries = [
    // ====== Tabela: categoria ======
    "CREATE TABLE IF NOT EXISTS categoria (
      id INT AUTO_INCREMENT PRIMARY KEY,
      nome VARCHAR(50) NOT NULL UNIQUE
    )",

    // ====== Tabela: associado ======
    "CREATE TABLE IF NOT EXISTS associado (
      cpf VARCHAR(14) PRIMARY KEY,
      nome VARCHAR(60) NOT NULL,
      data_nascimento DATE NOT NULL,
      celular VARCHAR(20),
      email VARCHAR(100) NOT NULL UNIQUE,
      senha_hash VARCHAR(255) NOT NULL,
      endereco VARCHAR(80),
      bairro VARCHAR(50),
      cidade VARCHAR(60),
      uf VARCHAR(20),
      cep VARCHAR(10)
    )",

    // ====== Tabela: comercio ======
    "CREATE TABLE IF NOT EXISTS comercio (
      cnpj VARCHAR(18) PRIMARY KEY,
      razao_social VARCHAR(80) NOT NULL,
      nome_fantasia VARCHAR(60),
      email VARCHAR(100) NOT NULL UNIQUE,
      senha_hash VARCHAR(255) NOT NULL,
      contato VARCHAR(20),
      endereco VARCHAR(80),
      bairro VARCHAR(50),
      cidade VARCHAR(60),
      uf VARCHAR(20),
      cep VARCHAR(20),
      categoria_id INT NOT NULL,
      FOREIGN KEY (categoria_id) REFERENCES categoria(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
    )",


    // ====== Tabela: cupom ======
    "CREATE TABLE IF NOT EXISTS cupom (
      num_cupom VARCHAR(12) PRIMARY KEY,
      comercio_cnpj VARCHAR(18) NOT NULL,
      titulo VARCHAR(80) NOT NULL,
      data_emissao DATE NOT NULL DEFAULT (CURRENT_DATE),
      data_inicio DATE NOT NULL,
      data_termino DATE NOT NULL,
      percentual_desc DECIMAL(5,2) NOT NULL,
      quantidade INT NOT NULL DEFAULT 1,
      FOREIGN KEY (comercio_cnpj) REFERENCES comercio(cnpj)
        ON DELETE CASCADE ON UPDATE CASCADE
    )",

    // ====== Tabela: associado_cupom ======
    "CREATE TABLE IF NOT EXISTS associado_cupom (
      id INT AUTO_INCREMENT PRIMARY KEY,
      num_cupom VARCHAR(12) NOT NULL,
      associado_cpf VARCHAR(14) NOT NULL,
      codigo_reserva VARCHAR(20) NOT NULL UNIQUE,
      data_reserva DATE NOT NULL DEFAULT (CURRENT_DATE),
      data_uso DATE NULL,
      FOREIGN KEY (num_cupom) REFERENCES cupom(num_cupom)
        ON DELETE CASCADE ON UPDATE CASCADE,
      FOREIGN KEY (associado_cpf) REFERENCES associado(cpf)
        ON DELETE CASCADE ON UPDATE CASCADE,
      UNIQUE KEY uq_cupom_associado (num_cupom, associado_cpf)
    )",


    // ===== INSERTS ======


    // Inserts categoria (ids explícitos para facilitar FKs depois)
    "INSERT INTO categoria (id, nome) VALUES
      (1, 'Supermercado'),
      (2, 'Restaurante'),
      (3, 'Farmácia')
    ON DUPLICATE KEY UPDATE nome = VALUES(nome)",

    // Inserts associado
    "INSERT INTO associado (cpf, nome, data_nascimento, celular, email, senha_hash, endereco, bairro, cidade, uf, cep) VALUES
      ('111.222.333-44', 'Lucas Silva', '1998-05-12', '11 90000-0001', 'lucas@example.com', '\$2y\$10\$E6SS79DaMeo8M/mPfaJtueSTf15IhhblVSSG8hVvBLS6F.zfaNsdO', 'Rua A, 123', 'Centro', 'Dumont', 'SP', '14000-000'),
      ('555.666.777-88', 'Mariana Souza', '1995-09-30', '11 90000-0002', 'mariana@example.com', '\$2y\$10\$J1x696N.Zz6y69SO4qPtUedMdnXE87Q9rchdLYE.Sk2ONa1hreLei', 'Av. B, 456', 'Jardins', 'São Paulo', 'SP', '01000-000'),
      ('999.000.111-22', 'João Pereira', '2000-01-20', '11 90000-0003', 'joao@example.com', '\$2y\$10\$gnolgg93Y1UZTRIRKookf.8YdSKzqrcahvSv4iRBFnlRSKYgz4r7e', 'Rua C, 789', 'Centro', 'Ribeirão Preto', 'SP', '14010-000')
    ON DUPLICATE KEY UPDATE nome = VALUES(nome), email = VALUES(email)",

    // Inserts comercio (categoria_id existentes: 1,2,3)
    "INSERT INTO comercio (cnpj, razao_social, nome_fantasia, email, senha_hash, contato, endereco, bairro, cidade, uf, cep, categoria_id) VALUES
      ('12.345.678/0001-90', 'Mercado Bom Preço LTDA', 'Bom Preço', 'mercado@example.com', '\$2y\$10\$avt4v7hIeMiWDwPHiA4s4uyLpDnpYNclOGTnzpRbRDRJkZf3EE3Gu', '16 90000-1000', 'Rua das Flores, 10', 'Centro', 'Dumont', 'SP', '14000-100', 1),
      ('98.765.432/0001-10', 'Restaurante Sabor Ltda', 'Sabor & Cia', 'restaurante@example.com', '\$2y\$10\$jxemf2n9hscbSlTeiYvyS.hwq3DNEN4mbyeezkZoX7XTutqbuAgxi', '16 90000-2000', 'Av. Principal, 200', 'Jardins', 'Ribeirão Preto', 'SP', '14020-200', 2),
      ('11.222.333/0001-55', 'Farmácia Saúde S/A', 'Saúde Farma', 'farmacia@example.com', '\$2y\$10\$TkDU6l6SCCqHhWZJ4z37qO9YkpR.288duzlrH1H5CGNnHkPnG/K3e', '16 90000-3000', 'Rua Verde, 30', 'Centro', 'São Paulo', 'SP', '01020-300', 3)
    ON DUPLICATE KEY UPDATE razao_social = VALUES(razao_social), email = VALUES(email)",

    // Inserts cupom (referenciam cnpjs acima)
    "INSERT INTO cupom (num_cupom, comercio_cnpj, titulo, data_emissao, data_inicio, data_termino, percentual_desc, quantidade) VALUES
      ('ABCDEF123456', '12.345.678/0001-90', 'Desconto Mercado 10%', CURRENT_DATE, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 10.00, 5),
      ('ZXCVBN987654', '98.765.432/0001-10', 'Almoço 15% OFF', CURRENT_DATE, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 20 DAY), 15.00, 3),
      ('QWERTY112233', '11.222.333/0001-55', 'Medicamentos 5% OFF', CURRENT_DATE, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 15 DAY), 5.00, 10)
    ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), percentual_desc = VALUES(percentual_desc), quantidade = VALUES(quantidade)",

    // Inserts associado_cupom (exemplos de reservas)
    "INSERT INTO associado_cupom (num_cupom, associado_cpf, codigo_reserva, data_reserva, data_uso) VALUES
      ('ABCDEF123456', '111.222.333-44', 'RESERVA001', CURDATE(), NULL),      -- reservado, não usado
      ('ZXCVBN987654', '555.666.777-88', 'RESERVA002', DATE_ADD(CURDATE(), INTERVAL -2 DAY), CURDATE()), -- usado
      ('QWERTY112233', '999.000.111-22', 'RESERVA003', DATE_ADD(CURDATE(), INTERVAL -1 DAY), NULL)       -- reservado, não usado
    ON DUPLICATE KEY UPDATE 
      associado_cpf = VALUES(associado_cpf), 
      data_uso = VALUES(data_uso),
      codigo_reserva = VALUES(codigo_reserva);"

];

//executa todas as queries
foreach ($queries as $q) {
    if ($conn->query($q) === TRUE) {
        echo "SUCESSO: " . htmlspecialchars(substr($q, 0, 60)) . "...<br>";
    } else {
        echo "ERRO: " . $conn->error . "<br>";
    }
}

echo "<br><a href='../public/index.php'>Ir para o Menu</a>";
?>
