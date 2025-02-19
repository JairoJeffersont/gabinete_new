<?php

namespace GabineteMvc\Database;

use GabineteMvc\Middleware\Logger;
use PDO;
use PDOException;

/**
 * Classe Database
 * 
 * Esta classe é responsável por gerenciar a conexão com o banco de dados utilizando a biblioteca PDO (PHP Data Objects).
 * Ela implementa o padrão **Singleton**, garantindo que apenas uma única conexão seja estabelecida com o banco de dados 
 * durante a execução da aplicação. Isso otimiza o uso de recursos e evita a criação de múltiplas instâncias de conexão.
 * 
 * A classe também lida com a configuração do banco de dados, onde os parâmetros de conexão são carregados de um arquivo 
 * de configuração. Caso ocorra algum erro ao conectar-se ao banco, a classe registra o erro usando a classe `Logger`.
 * 
 * @package GabineteMvc\Database
 */
class Database {

    /**
     * @var PDO|null Instância única da conexão com o banco de dados.
     * 
     * A conexão é inicializada uma única vez e reutilizada ao longo de toda a execução do programa, 
     * garantindo que não haja múltiplas conexões abertas para o mesmo banco de dados.
     */
    private static ?PDO $connection = null;

    /**
     * Construtor privado para impedir a criação de instâncias da classe.
     * 
     * O padrão Singleton impede que a classe seja instanciada diretamente, garantindo que a conexão 
     * seja acessada somente por meio do método estático `getConnection()`.
     */
    private function __construct() {
        // Construtor privado, portanto não pode ser instanciado diretamente
    }

    /**
     * Obtém a conexão com o banco de dados.
     * 
     * Este método cria e retorna uma instância única da conexão com o banco de dados utilizando o padrão Singleton.
     * Caso a conexão ainda não tenha sido estabelecida, o método irá carregar as configurações do banco de dados a partir 
     * de um arquivo de configuração e, em seguida, criar a conexão.
     * 
     * Se ocorrer algum erro durante a criação da conexão, uma mensagem de erro é registrada, e o método retorna `null`.
     * 
     * @return PDO|null Retorna a instância da conexão PDO se a conexão for bem-sucedida, ou `null` em caso de falha.
     */
    public static function getConnection(): ?PDO {
        // Verifica se a conexão já foi criada. Se não, cria a conexão.
        if (self::$connection === null) {
            // Instancia o Logger para registrar mensagens de erro
            $log = new Logger();

            // Caminho do arquivo de configuração do banco de dados
            $configPath = dirname(__DIR__, 2) . '/src/Configs/config.php';

            // Verifica se o arquivo de configuração existe
            if (!file_exists($configPath)) {
                $log->novoLog('db_error', 'Arquivo de configuração do banco não encontrado.');
                return null; // Retorna null caso não consiga encontrar o arquivo de configuração
            }

            // Carrega as configurações do banco de dados a partir do arquivo de configuração
            $config = require $configPath;

            // Verifica se a chave 'database' existe na configuração
            if (!isset($config['database'])) {
                $log->novoLog('db_error', 'Configuração do banco de dados inválida.');
                return null; // Retorna null se a configuração estiver ausente ou inválida
            }

            // Obtém as configurações do banco de dados
            $host = $config['database']['host'] ?? 'localhost'; // Host do banco de dados
            $dbname = $config['database']['name'] ?? ''; // Nome do banco de dados
            $username = $config['database']['user'] ?? ''; // Nome de usuário para autenticação
            $password = $config['database']['password'] ?? ''; // Senha do banco de dados

            try {
                // Cria a string DSN para conexão com o banco de dados
                $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

                // Tenta criar a conexão com o banco de dados
                self::$connection = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lança exceções em caso de erro
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retorna os resultados como array associativo
                    PDO::ATTR_PERSISTENT => true // Conexão persistente para otimizar a performance
                ]);
            } catch (PDOException $e) {
                // Caso ocorra algum erro, registra o erro e retorna null
                $log->novoLog('db_error', $e->getMessage());
                header('Location: ?secao=fatal-error');
                return null; // Retorna null caso a conexão falhe
            }
        }

        // Retorna a conexão PDO estabelecida
        return self::$connection;
    }
}
