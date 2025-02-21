<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class MensagemModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // CRIAR MENSAGEM
    public function criarMensagem($dados) {
        // Inserindo uma nova mensagem
        $query = 'INSERT INTO mensagem(mensagem_id, mensagem_titulo, mensagem_texto, mensagem_status, mensagem_remetente, mensagem_destinatario) 
                  VALUES (UUID(), :mensagem_titulo, :mensagem_texto, :mensagem_status, :mensagem_remetente, :mensagem_destinatario);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':mensagem_titulo', $dados['mensagem_titulo'], PDO::PARAM_STR);
        $stmt->bindValue(':mensagem_texto', $dados['mensagem_texto'], PDO::PARAM_STR);
        $stmt->bindValue(':mensagem_status', $dados['mensagem_status'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':mensagem_remetente', $dados['mensagem_remetente'], PDO::PARAM_STR);
        $stmt->bindValue(':mensagem_destinatario', $dados['mensagem_destinatario'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // LISTAR MENSAGENS
    public function listarMensagem($itens, $pagina, $ordem, $ordenarPor, $usuario) {
        // Listando mensagens com paginação
        $offset = ($pagina - 1) * $itens;

        $query = "SELECT view_mensagem.*, 
                         (SELECT COUNT(mensagem_id) FROM mensagem WHERE mensagem_destinatario = :mensagem_destinatario) as total_mensagem 
                  FROM view_mensagem 
                  ORDER BY $ordenarPor $ordem 
                  LIMIT :itens OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':mensagem_destinatario', $usuario, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BUSCAR MENSAGEM POR COLUNA
    public function buscaMensagem($coluna, $valor) {
        // Buscando mensagem por coluna e valor
        $query = "SELECT * FROM view_mensagem WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    // APAGAR MENSAGEM
    public function apagarMensagem($mensagem_id) {
        // Apagando mensagem
        $query = 'DELETE FROM mensagem WHERE mensagem_id = :mensagem_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':mensagem_id', $mensagem_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
