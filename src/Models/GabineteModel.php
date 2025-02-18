<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

/**
 * Classe GabineteModel
 * 
 * Esta classe fornece métodos para interagir com a tabela `gabinete` no banco de dados. 
 * Ela permite realizar operações como criar, atualizar, listar, buscar e apagar registros de gabinetes.
 * Utiliza PDO para interação com o banco de dados, e cada método foi projetado para realizar 
 * uma operação específica sobre os dados dos gabinetes.
 * 
 * @package GabineteMvc\Models
 */
class GabineteModel {

    /**
     * @var PDO Conexão com o banco de dados.
     */
    private $conn;

    /**
     * Construtor da classe.
     * 
     * Inicializa a conexão com o banco de dados utilizando o método `getConnection` da classe `Database`.
     */
    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Cria um novo gabinete no banco de dados.
     * 
     * Este método insere um novo registro de gabinete na tabela `gabinete`. Ele utiliza UUID para garantir 
     * que cada gabinete tenha um identificador único.
     * 
     * @param array $dados Array contendo os dados do gabinete a ser criado. Deve incluir as chaves:
     * - 'gabinete_cliente' (string)
     * - 'gabinete_tipo' (string)
     * - 'gabinete_politico' (string)
     * - 'gabinete_estado' (string)
     * - 'gabinete_endereco' (string)
     * - 'gabinete_telefone' (string)
     * 
     * @return bool Retorna `true` se a operação for bem-sucedida, ou `false` em caso de erro.
     */
    public function criar($dados) {
        $query = 'INSERT INTO gabinete(gabinete_id, gabinete_cliente, gabinete_tipo, gabinete_politico, gabinete_estado, gabinete_endereco, gabinete_municipio, gabinete_telefone) 
                  VALUES (UUID(), :gabinete_cliente, :gabinete_tipo, :gabinete_politico, :gabinete_estado, :gabinete_endereco, :gabinete_municipio,  :gabinete_telefone);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':gabinete_cliente', $dados['gabinete_cliente'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_tipo', $dados['gabinete_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_politico', $dados['gabinete_politico'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_estado', $dados['gabinete_estado'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_endereco', $dados['gabinete_endereco'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_municipio', $dados['gabinete_municipio'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_telefone', $dados['gabinete_telefone'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Atualiza as informações de um gabinete no banco de dados.
     * 
     * Este método atualiza o registro de um gabinete existente na tabela `gabinete`. O gabinete é identificado 
     * pelo `gabinete_id`, e os dados fornecidos são atualizados de acordo com as informações fornecidas no array `$dados`.
     * 
     * @param array $dados Array contendo os dados a serem atualizados do gabinete. Deve incluir as chaves:
     * - 'gabinete_id' (string)
     * - 'gabinete_cliente' (string)
     * - 'gabinete_tipo' (string)
     * - 'gabinete_politico' (string)
     * - 'gabinete_estado' (string)
     * - 'gabinete_endereco' (string)
     * - 'gabinete_telefone' (string)
     * 
     * @return bool Retorna `true` se a operação for bem-sucedida, ou `false` em caso de erro.
     */
    public function atualizar($dados) {
        $query = 'UPDATE gabinete SET gabinete_cliente = :gabinete_cliente, gabinete_tipo = :gabinete_tipo, gabinete_politico = :gabinete_politico, 
                  gabinete_estado = :gabinete_estado, gabinete_endereco = :gabinete_endereco, gabinete_municipio = :gabinete_municipio,  gabinete_telefone = :gabinete_telefone 
                  WHERE gabinete_id = :gabinete_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':gabinete_id', $dados['gabinete_id'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_cliente', $dados['gabinete_cliente'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_tipo', $dados['gabinete_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_politico', $dados['gabinete_politico'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_estado', $dados['gabinete_estado'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_endereco', $dados['gabinete_endereco'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_municipio', $dados['gabinete_municipio'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_telefone', $dados['gabinete_telefone'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Lista os gabinetes cadastrados no banco de dados com suporte a paginação e ordenação.
     * 
     * Este método retorna os registros de gabinetes da tabela `gabinete`, permitindo a limitação dos resultados 
     * com base na página atual, número de itens por página, e ordenação dos resultados por uma coluna específica.
     * 
     * @param int $itens Número de itens a serem retornados por página.
     * @param int $pagina Número da página a ser retornada.
     * @param string $ordem Direção da ordenação: 'ASC' para crescente ou 'DESC' para decrescente.
     * @param string $ordenarPor Nome da coluna para ordenação.
     * 
     * @return array Retorna um array com os dados dos gabinetes, ordenados e paginados. Se não houver gabinetes, 
     * retorna um array vazio.
     */
    public function listar($itens, $pagina, $ordem, $ordenarPor) {
        $offset = ($pagina - 1) * $itens;

        $query = "SELECT gabinete.*, (SELECT COUNT(gabinete_id) FROM gabinete) as total_gabinete FROM gabinete ORDER BY $ordenarPor $ordem LIMIT :itens OFFSET :offset";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um gabinete específico com base em uma coluna e valor.
     * 
     * Este método permite buscar um gabinete específico utilizando uma coluna e valor para a filtragem. 
     * O nome da coluna e o valor são passados como parâmetros, e a consulta retorna o primeiro gabinete encontrado 
     * que corresponde ao valor da coluna especificada.
     * 
     * @param string $coluna Nome da coluna para a busca, como 'gabinete_id' ou 'gabinete_cliente'.
     * @param mixed $valor Valor a ser buscado na coluna especificada.
     * 
     * @return array|null Retorna um array associativo com os dados do gabinete encontrado, ou `null` caso nenhum gabinete seja encontrado.
     */
    public function busca($coluna, $valor) {
        $query = "SELECT * FROM gabinete WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Apaga um gabinete do banco de dados.
     * 
     * Este método apaga o registro de um gabinete da tabela `gabinete`, utilizando o `gabinete_id` como referência.
     * 
     * @param string $gabinete_id O ID do gabinete a ser apagado.
     * 
     * @return bool Retorna `true` se o gabinete for apagado com sucesso, ou `false` em caso de erro.
     */
    public function apagar($gabinete_id) {
        $query = 'DELETE FROM gabinete WHERE gabinete_id = :gabinete_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':gabinete_id', $gabinete_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
