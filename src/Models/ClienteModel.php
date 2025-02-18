<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

/**
 * Classe ClienteModel
 * 
 * Esta classe fornece métodos para interagir com a tabela `cliente` no banco de dados. 
 * Ela permite realizar operações como criar, atualizar, listar, buscar e apagar registros de clientes.
 * Utiliza PDO para interação com o banco de dados, e cada método foi projetado para realizar 
 * uma operação específica sobre os dados dos clientes.
 * 
 * @package GabineteMvc\Models
 */
class ClienteModel {

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
     * Cria um novo cliente no banco de dados.
     * 
     * Este método insere um novo registro de cliente na tabela `cliente`. Ele utiliza UUID para garantir 
     * que cada cliente tenha um identificador único.
     * 
     * @param array $dados Array contendo os dados do cliente a ser criado. Deve incluir as chaves:
     * - 'cliente_nome' (string)
     * - 'cliente_email' (string)
     * - 'cliente_telefone' (string)
     * - 'cliente_ativo' (bool)
     * - 'cliente_endereco' (string)
     * - 'cliente_cep' (string)
     * - 'cliente_cpf' (string)
     * 
     * @return bool Retorna `true` se a operação for bem-sucedida, ou `false` em caso de erro.
     */
    public function criar($dados) {
        $query = 'INSERT INTO cliente(cliente_id, cliente_nome, cliente_email, cliente_telefone, cliente_ativo, cliente_endereco, cliente_cep, cliente_cpf) VALUES (UUID(), :cliente_nome, :cliente_email, :cliente_telefone, :cliente_ativo, :cliente_endereco, :cliente_cep, :cliente_cpf);';


        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cliente_nome', $dados['cliente_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_email', $dados['cliente_email'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_telefone', $dados['cliente_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_ativo', $dados['cliente_ativo'], PDO::PARAM_BOOL);
        $stmt->bindValue(':cliente_endereco', $dados['cliente_endereco'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_cep', $dados['cliente_cep'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_cpf', $dados['cliente_cpf'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Atualiza as informações de um cliente no banco de dados.
     * 
     * Este método atualiza o registro de um cliente existente na tabela `cliente`. O cliente é identificado 
     * pelo `cliente_id`, e os dados fornecidos são atualizados de acordo com as informações fornecidas no array `$dados`.
     * 
     * @param array $dados Array contendo os dados a serem atualizados do cliente. Deve incluir as chaves:
     * - 'cliente_id' (string)
     * - 'cliente_nome' (string)
     * - 'cliente_email' (string)
     * - 'cliente_telefone' (string)
     * - 'cliente_ativo' (bool)
     * - 'cliente_endereco' (string)
     * - 'cliente_cep' (string)
     * - 'cliente_cpf' (string)
     * 
     * @return bool Retorna `true` se a operação for bem-sucedida, ou `false` em caso de erro.
     */
    public function atualizar($dados) {
        $query = 'UPDATE cliente SET cliente_nome = :cliente_nome, cliente_email = :cliente_email, cliente_telefone = :cliente_telefone, cliente_ativo = :cliente_ativo, cliente_endereco = :cliente_endereco, cliente_cep = :cliente_cep, cliente_cpf = :cliente_cpf WHERE cliente_id = :cliente_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':cliente_id', $dados['cliente_id'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_nome', $dados['cliente_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_email', $dados['cliente_email'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_telefone', $dados['cliente_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_ativo', $dados['cliente_ativo'], PDO::PARAM_BOOL);
        $stmt->bindValue(':cliente_endereco', $dados['cliente_endereco'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_cep', $dados['cliente_cep'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_cpf', $dados['cliente_cpf'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Lista os clientes cadastrados no banco de dados com suporte a paginação e ordenação.
     * 
     * Este método retorna os registros de clientes da tabela `cliente`, permitindo a limitação dos resultados 
     * com base na página atual, número de itens por página, e ordenação dos resultados por uma coluna específica.
     * 
     * @param int $itens Número de itens a serem retornados por página.
     * @param int $pagina Número da página a ser retornada.
     * @param string $ordem Direção da ordenação: 'ASC' para crescente ou 'DESC' para decrescente.
     * @param string $ordenarPor Nome da coluna para ordenação.
     * 
     * @return array Retorna um array com os dados dos clientes, ordenados e paginados. Se não houver clientes, 
     * retorna um array vazio.
     */
    public function listar($itens, $pagina, $ordem, $ordenarPor) {

        $offset = ($pagina - 1) * $itens;

        $query = "SELECT cliente.*, (SELECT COUNT(cliente_id) FROM cliente) as total_cliente FROM cliente ORDER BY $ordenarPor $ordem LIMIT :itens OFFSET :offset";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Busca um cliente específico com base em uma coluna e valor.
     * 
     * Este método permite buscar um cliente específico utilizando uma coluna e valor para a filtragem. 
     * O nome da coluna e o valor são passados como parâmetros, e a consulta retorna o primeiro cliente encontrado 
     * que corresponde ao valor da coluna especificada.
     * 
     * @param string $coluna Nome da coluna para a busca, como 'cliente_id' ou 'cliente_email'.
     * @param mixed $valor Valor a ser buscado na coluna especificada.
     * 
     * @return array|null Retorna um array associativo com os dados do cliente encontrado, ou `null` caso nenhum cliente seja encontrado.
     */
    public function busca($coluna, $valor) {
        $query = "SELECT * FROM cliente WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Apaga um cliente do banco de dados.
     * 
     * Este método apaga o registro de um cliente da tabela `cliente`, utilizando o `cliente_id` como referência.
     * 
     * @param string $cliente_id O ID do cliente a ser apagado.
     * 
     * @return bool Retorna `true` se o cliente for apagado com sucesso, ou `false` em caso de erro.
     */
    public function apagar($cliente_id) {
        $query = 'DELETE FROM cliente WHERE cliente_id = :cliente_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cliente_id', $cliente_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
