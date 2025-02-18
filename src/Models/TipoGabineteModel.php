<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

/**
 * Classe TipoGabineteModel
 * 
 * Esta classe fornece métodos para interagir com a tabela `tipo_gabinete` no banco de dados. 
 * Ela permite realizar operações como criar, atualizar, listar, buscar e apagar registros de tipos de gabinetes.
 * Utiliza PDO para interação com o banco de dados, e cada método foi projetado para realizar 
 * uma operação específica sobre os dados dos tipos de gabinetes.
 * 
 * @package GabineteMvc\Models
 */
class TipoGabineteModel {

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
     * Cria um novo tipo de gabinete no banco de dados.
     * 
     * Este método insere um novo registro de tipo de gabinete na tabela `tipo_gabinete`. 
     * Ele utiliza UUID para garantir que cada tipo de gabinete tenha um identificador único.
     * 
     * @param array $dados Array contendo os dados do tipo de gabinete a ser criado. Deve incluir as chaves:
     * - 'tipo_gabinete_nome' (string)
     * - 'tipo_gabinete_informacoes' (string, opcional)
     * 
     * @return bool Retorna `true` se a operação for bem-sucedida, ou `false` em caso de erro.
     */
    public function criar($dados) {
        $query = 'INSERT INTO tipo_gabinete(tipo_gabinete_id, tipo_gabinete_nome, tipo_gabinete_informacoes) 
                  VALUES (UUID(), :tipo_gabinete_nome, :tipo_gabinete_informacoes);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':tipo_gabinete_nome', $dados['tipo_gabinete_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':tipo_gabinete_informacoes', $dados['tipo_gabinete_informacoes'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Atualiza as informações de um tipo de gabinete no banco de dados.
     * 
     * Este método atualiza o registro de um tipo de gabinete existente na tabela `tipo_gabinete`. O tipo de gabinete 
     * é identificado pelo `tipo_gabinete_id`, e os dados fornecidos são atualizados de acordo com as informações fornecidas no array `$dados`.
     * 
     * @param array $dados Array contendo os dados a serem atualizados do tipo de gabinete. Deve incluir as chaves:
     * - 'tipo_gabinete_id' (string)
     * - 'tipo_gabinete_nome' (string)
     * - 'tipo_gabinete_informacoes' (string, opcional)
     * 
     * @return bool Retorna `true` se a operação for bem-sucedida, ou `false` em caso de erro.
     */
    public function atualizar($dados) {
        $query = 'UPDATE tipo_gabinete 
                  SET tipo_gabinete_nome = :tipo_gabinete_nome, tipo_gabinete_informacoes = :tipo_gabinete_informacoes 
                  WHERE tipo_gabinete_id = :tipo_gabinete_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':tipo_gabinete_id', $dados['tipo_gabinete_id'], PDO::PARAM_STR);
        $stmt->bindValue(':tipo_gabinete_nome', $dados['tipo_gabinete_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':tipo_gabinete_informacoes', $dados['tipo_gabinete_informacoes'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Lista os tipos de gabinetes cadastrados no banco de dados com suporte a paginação e ordenação.
     * 
     * Este método retorna os registros de tipos de gabinetes da tabela `tipo_gabinete`, permitindo a limitação dos resultados 
     * com base na página atual, número de itens por página, e ordenação dos resultados por uma coluna específica.
     * 
     * @param int $itens Número de itens a serem retornados por página.
     * @param int $pagina Número da página a ser retornada.
     * @param string $ordem Direção da ordenação: 'ASC' para crescente ou 'DESC' para decrescente.
     * @param string $ordenarPor Nome da coluna para ordenação.
     * 
     * @return array Retorna um array com os dados dos tipos de gabinetes, ordenados e paginados. Se não houver tipos de gabinetes, 
     * retorna um array vazio.
     */
    public function listar($itens, $pagina, $ordem, $ordenarPor) {

        $offset = ($pagina - 1) * $itens;

        $query = "SELECT tipo_gabinete.*, (SELECT COUNT(tipo_gabinete_id) FROM tipo_gabinete) as total_tipo_gabinete 
                  FROM tipo_gabinete ORDER BY $ordenarPor $ordem LIMIT :itens OFFSET :offset";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um tipo de gabinete específico com base em uma coluna e valor.
     * 
     * Este método permite buscar um tipo de gabinete específico utilizando uma coluna e valor para a filtragem. 
     * O nome da coluna e o valor são passados como parâmetros, e a consulta retorna o primeiro tipo de gabinete encontrado 
     * que corresponde ao valor da coluna especificada.
     * 
     * @param string $coluna Nome da coluna para a busca, como 'tipo_gabinete_id' ou 'tipo_gabinete_nome'.
     * @param mixed $valor Valor a ser buscado na coluna especificada.
     * 
     * @return array|null Retorna um array associativo com os dados do tipo de gabinete encontrado, ou `null` caso nenhum tipo de gabinete seja encontrado.
     */
    public function busca($coluna, $valor) {
        $query = "SELECT * FROM tipo_gabinete WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Apaga um tipo de gabinete do banco de dados.
     * 
     * Este método apaga o registro de um tipo de gabinete da tabela `tipo_gabinete`, utilizando o `tipo_gabinete_id` como referência.
     * 
     * @param string $tipo_gabinete_id O ID do tipo de gabinete a ser apagado.
     * 
     * @return bool Retorna `true` se o tipo de gabinete for apagado com sucesso, ou `false` em caso de erro.
     */
    public function apagar($tipo_gabinete_id) {
        $query = 'DELETE FROM tipo_gabinete WHERE tipo_gabinete_id = :tipo_gabinete_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':tipo_gabinete_id', $tipo_gabinete_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
