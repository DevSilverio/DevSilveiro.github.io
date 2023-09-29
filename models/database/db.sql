-- CRIANDO O BANCO DE DADOS
CREATE DATABASE dashboard;

-- CRIANDO TABELA DE USUÁRIOS 
CREATE TABLE dashboard.usuarios (
    id int primary key auto_increment,
    nome VARCHAR(100) ,
    data_nascimento DATE ,
    email VARCHAR(100) ,
    cpf VARCHAR(14) ,
    senha VARCHAR(250) ,
    ip_cliente VARCHAR(45),
    nivel varchar(5),
    primeiro_acesso INT
);

CREATE TABLE dashboard.security (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) ,
    ip_cliente VARCHAR(45),
    nivel varchar(5)
);

-- CRIANDO PROCEDURES PARA FACILITAR NO CRUD
USE dashboard;

-- INSERIR USUÁRIO NO BANCO DE DADOS
DELIMITER //
CREATE PROCEDURE InserirUsuario(
    IN p_nome VARCHAR(100),
    IN p_data_nascimento DATE,
    IN p_email VARCHAR(100),
    IN p_cpf VARCHAR(14),
    IN p_senha VARCHAR(250),
    IN p_nivel VARCHAR(5),
    IN p_primeiro_acesso INT
)
BEGIN
    INSERT INTO dashboard.usuarios (nome, data_nascimento, email, cpf, senha, nivel, primeiro_acesso)
    VALUES (p_nome, p_data_nascimento, p_email, p_cpf, p_senha, p_nivel, p_primeiro_acesso);
END //
DELIMITER ;

-- RECUPERAR INFORMAÇÕES DE UM USUÁRIO PELO ID 
DELIMITER //
CREATE PROCEDURE ObterUsuarioPorID(
    IN p_id INT
)
BEGIN
    SELECT * FROM dashboard.usuarios
    WHERE id = p_id;
END //
DELIMITER ;

-- ATUALIZANDO INFORMAÇÕES DE UM USUÁRIO PELO ID
DELIMITER //
CREATE PROCEDURE AtualizarUsuario(
    IN p_id INT,
    IN p_nome VARCHAR(100),
    IN p_data_nascimento DATE,
    IN p_email VARCHAR(100),
    IN p_cpf VARCHAR(14),
    IN p_senha VARCHAR(250),
    IN p_ip_cliente VARCHAR(45),
    IN p_nivel VARCHAR(5)
)
BEGIN
    UPDATE dashboard.usuarios
    SET nome = p_nome,
        data_nascimento = p_data_nascimento,
        email = p_email,
        cpf = p_cpf,
        senha = p_senha,
        ip_cliente = p_ip_cliente,
        nivel = p_nivel
    WHERE id = p_id;
END //
DELIMITER ;

-- EXCLUINDO USUARIO PELO ID 
DELIMITER //
CREATE PROCEDURE ExcluirUsuario(
    IN p_id INT
)
BEGIN
    DELETE FROM dashboard.usuarios
    WHERE id = p_id;
END //
DELIMITER ;

-- VERIFICÇÃO DE USUARIO 
DELIMITER //
CREATE PROCEDURE VerificarUsuario(
    IN p_email VARCHAR(100),
    IN p_senha VARCHAR(250),
    OUT p_result INT
)
BEGIN
    DECLARE user_id INT;

    SELECT id INTO user_id
    FROM usuarios
    WHERE email = p_email AND senha = p_senha;

    IF user_id IS NOT NULL THEN
        SET p_result = 1; 
    ELSE
        SET p_result = 0; 
    END IF;
END //
DELIMITER ;

-- ATUALIZAÇÃO DE IP DO CLIENTE AO LOGAR 
DELIMITER //

CREATE PROCEDURE AtualizarIpCliente(IN p_email VARCHAR(100), IN p_ip_cliente VARCHAR(45))
BEGIN
    UPDATE usuarios
    SET ip_cliente = p_ip_cliente
    WHERE email = p_email;
END//

DELIMITER ;

-- REDEFINIR SENHA DO USUÁRIO DEPOIS DO PRIMEIRO ACESSO
DELIMITER //
CREATE PROCEDURE AtualizarSenhaEPrimeiroAcesso(
    IN p_id INT,
    IN p_nova_senha VARCHAR(250),
    IN p_novo_primeiro_acesso INT,
    IN p_ip_cliente VARCHAR(45)
)
BEGIN
    UPDATE dashboard.usuarios
    SET senha = p_nova_senha
    WHERE id = p_id;

    UPDATE dashboard.usuarios
    SET primeiro_acesso = p_novo_primeiro_acesso
    WHERE id = p_id;

    UPDATE dashboard.usuarios
    SET ip_cliente = p_ip_cliente
    WHERE id = p_id;
END //
DELIMITER ;


-- COMO CHAMAR OS PROCEDURE

-- CHAMANDO PROCEDURE PARA INSERIR UM NOVO USUÁRIO
CALL InserirUsuario('Pedro Henrique Silvério Hipólito', '2003-11-01', 'pedroh.shipolito@gmail.com', '123.456.789-01', 'senha_padrao', '127.0.0.1', 'admin');

-- CHAMANDO PROCEDURE PARA OBTER INFORMAÇÕES DE UM USUÁRIO POR ID
CALL ObterUsuarioPorID(1);

-- CHAMANDO PROCEDURE PARA ATUALIZAR INFORMAÇÕES DE UM USUÁRIO POR ID
CALL AtualizarUsuario(*, 'Novo Nome', '1995-07-20', 'novo_email@email.com', '987.654.321-09', 'nova_senha', '192.168.1.2', '2');

-- CHAMANDO PROCEDURE PARA EXCLUIR UM USUÁRIO PELO ID
CALL ExcluirUsuario(*);

-- CHAMANDO PROCEDURE PARA ATUALIZAR SENHA DE USUÁRIO 
CALL AtualizarSenhaEPrimeiroAcesso(*, 'senha_padrao', 1);

-- VIZUALIZANDO INFORMAÇÕES DA TABELA DO BANCO
SELECT * FROM dashboard.usuarios;