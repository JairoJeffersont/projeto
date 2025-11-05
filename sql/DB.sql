    
    CREATE TABLE
        tipo_gabinete (
            id VARCHAR(36) PRIMARY KEY,
            nome VARCHAR(50) NOT NULL UNIQUE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

    INSERT INTO
        tipo_gabinete (id, nome)
    VALUES
        ('1', 'Vereador'),
        ('2', 'Deputado Estadual'),
        ('3', 'Deputado Federal'),
        ('4', 'Senador');

    CREATE TABLE
        gabinete (
            id VARCHAR(36) PRIMARY KEY,
            nome VARCHAR(100) NOT NULL UNIQUE,
            nome_slug VARCHAR(100) NOT NULL,
            estado VARCHAR(2) NOT NULL,
            cidade VARCHAR(100) DEFAULT NULL,
            ativo BOOLEAN NOT NULL DEFAULT TRUE,
            tipo_gabinete_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (tipo_gabinete_id) REFERENCES tipo_gabinete (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

    INSERT INTO
        gabinete (id, nome, nome_slug, estado, cidade, tipo_gabinete_id) 
    VALUES 
        ('1', 'Gabinete Sistema', 'gabinete-sistema', 'DF', 'BRASILIA', '1');

    CREATE TABLE
        tipo_usuario (
            id VARCHAR(36) PRIMARY KEY,
            nome VARCHAR(50) NOT NULL UNIQUE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

    INSERT INTO
        tipo_usuario (id, nome)
    VALUES
        ('1', 'Administrador'),
        ('2', 'Usuário Comum');

    CREATE TABLE
        usuario (
            id VARCHAR(36) PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL,
            telefone VARCHAR(20) NOT NULL UNIQUE,
            foto VARCHAR(255) DEFAULT NULL,
            data_nascimento VARCHAR(5) DEFAULT NULL,
            token TEXT DEFAULT NULL,
            ativo BOOLEAN NOT NULL DEFAULT TRUE,
            gestor BOOLEAN NOT NULL DEFAULT TRUE,
            tipo_usuario_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (tipo_usuario_id) REFERENCES tipo_usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            gabinete_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (gabinete_id) REFERENCES gabinete (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

    INSERT INTO
        usuario (id, nome, email, senha, telefone, data_nascimento, gabinete_id, tipo_usuario_id) 
    VALUES 
        ('1', 'Usuário Sistema', 'email@email.com', '$2y$10$e0NRG7k8bq5lYFh3H8jzUuJ8mZ6kF1O9H1Z1Z1Z1Z1Z1Z1Z1Z1Z1', '00000000000', '01/01', '1', '1');

    CREATE TABLE
        tipo_orgao (
            id VARCHAR(36) PRIMARY KEY,
            nome VARCHAR(50) NOT NULL UNIQUE,
            gabinete_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (gabinete_id) REFERENCES gabinete (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            usuario_id VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

    INSERT INTO
        tipo_orgao (id, nome, gabinete_id)
    VALUES
        ('1', 'Sem tipo definido', '1');

    CREATE TABLE
        orgao(
            id VARCHAR(36) PRIMARY KEY,
            nome VARCHAR(100) NOT NULL UNIQUE,
            email VARCHAR(100) DEFAULT NULL,
            telefone VARCHAR(20) DEFAULT NULL,
            
            cidade VARCHAR(100) NOT NULL,
            estado VARCHAR(2) NOT NULL,
            site VARCHAR(100) DEFAULT NULL,
            instagram VARCHAR(100) DEFAULT NULL,
            facebook VARCHAR(100) DEFAULT NULL,
            informacoes_adicionais TEXT DEFAULT NULL,
            tipo_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (tipo_id) REFERENCES tipo_orgao (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            gabinete_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (gabinete_id) REFERENCES gabinete (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            usuario_id VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP        
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

    INSERT INTO
        orgao (id, nome, cidade, estado, tipo_id, gabinete_id)
    VALUES
        ('1', 'Órgão Sistema', 'BRASILIA', 'DF', '1', '1');

    CREATE TABLE
        tipo_pessoa (
            id VARCHAR(36) PRIMARY KEY,
            nome VARCHAR(50) NOT NULL UNIQUE,
            gabinete_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (gabinete_id) REFERENCES gabinete (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            usuario_id VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

    INSERT INTO
        tipo_pessoa (id, nome, gabinete_id)
    VALUES
        ('1', 'Sem tipo definido', '1');


    CREATE TABLE
        profissao (
            id VARCHAR(36) PRIMARY KEY,
            nome VARCHAR(50) NOT NULL UNIQUE,
            gabinete_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (gabinete_id) REFERENCES gabinete (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            usuario_id VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

    INSERT INTO
        profissao (id, nome, gabinete_id)
    VALUES
        ('1', 'Profissão não informada', '1');


    CREATE TABLE
        pessoa (
            id VARCHAR(36) PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(100) DEFAULT NULL,
            telefone VARCHAR(20) DEFAULT NULL,            
            cidade VARCHAR(100) NOT NULL,
            estado VARCHAR(2) NOT NULL,
            data_nascimento VARCHAR(5) DEFAULT NULL,
            profissao VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (profissao) REFERENCES profissao (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            partido VARCHAR(100) DEFAULT NULL,
            instagram VARCHAR(100) DEFAULT NULL,
            facebook VARCHAR(100) DEFAULT NULL,
            sexo VARCHAR(20) DEFAULT NULL,
            importancia VARCHAR(20) DEFAULT NULL,
            foto VARCHAR(255) DEFAULT NULL,
            informacoes_adicionais TEXT DEFAULT NULL,
            orgao_id VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (orgao_id) REFERENCES orgao (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            tipo_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (tipo_id) REFERENCES tipo_pessoa (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            gabinete_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (gabinete_id) REFERENCES gabinete (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            usuario_id VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP        
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


    CREATE TABLE
        tipo_documento (
            id VARCHAR(36) PRIMARY KEY,
            nome VARCHAR(50) NOT NULL UNIQUE,
            gabinete_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (gabinete_id) REFERENCES gabinete (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            usuario_id VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

    INSERT INTO
        tipo_documento (id, nome, gabinete_id, usuario_id)
        VALUES
            ('1', 'Sem tipo definido', '1', '1');

    CREATE TABLE
        documento (
            id VARCHAR(36) PRIMARY KEY,
            titulo VARCHAR(100) NOT NULL,
            ano VARCHAR(4) DEFAULT NULL,
            arquivo VARCHAR(255) NOT NULL,
            resumo TEXT DEFAULT NULL,
            orgao_id VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (orgao_id) REFERENCES orgao (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            tipo_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (tipo_id) REFERENCES tipo_documento (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            usuario_id VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            gabinete_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (gabinete_id) REFERENCES gabinete (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP        
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


    CREATE TABLE
        situacao_emenda (
            id VARCHAR(36) PRIMARY KEY,
            nome VARCHAR(50) NOT NULL UNIQUE,
            gabinete_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (gabinete_id) REFERENCES gabinete (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            usuario_id VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

    INSERT INTO
        situacao_emenda (id, nome, gabinete_id, usuario_id)
    VALUES
        ('1', 'Situação não definida', '1', '1');

    CREATE TABLE
        area_emenda (
            id VARCHAR(36) PRIMARY KEY,
            nome VARCHAR(50) NOT NULL UNIQUE,
            gabinete_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (gabinete_id) REFERENCES gabinete (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            usuario_id VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

    INSERT INTO
        area_emenda (id, nome, gabinete_id, usuario_id)
    VALUES
        ('1', 'Sem área definido', '1', '1');


    CREATE TABLE
        tipo_emenda (
            id VARCHAR(36) PRIMARY KEY,
            nome VARCHAR(50) NOT NULL UNIQUE,
            gabinete_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (gabinete_id) REFERENCES gabinete (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            usuario_id VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

    INSERT INTO
        tipo_emenda (id, nome, gabinete_id, usuario_id)
    VALUES
        ('1', 'Emenda individual', '1', '1'),
        ('2', 'Emenda de bancada', '1', '1');

    CREATE TABLE
        emenda (
            id VARCHAR(36) PRIMARY KEY,
            numero INT NOT NULL,
            ano VARCHAR(4) NOT NULL,
            valor DECIMAL(15,2) NOT NULL,
            descricao TEXT NOT NULL,
            informacoes TEXT DEFAULT NULL,
            estado VARCHAR(2) NOT NULL,
            cidade VARCHAR(100) DEFAULT NULL,
            situacao_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (situacao_id) REFERENCES situacao_emenda (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            area_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (area_id) REFERENCES area_emenda (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            tipo_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (tipo_id) REFERENCES tipo_emenda (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            gabinete_id VARCHAR(36) NOT NULL,
            FOREIGN KEY (gabinete_id) REFERENCES gabinete (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            usuario_id VARCHAR(36) DEFAULT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP        
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;