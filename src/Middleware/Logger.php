<?php

namespace GabineteMvc\Middleware;

/**
 * Classe Logger
 * 
 * A classe `Logger` é responsável por registrar mensagens de log em arquivos. 
 * O objetivo principal é fornecer uma maneira fácil de registrar informações relevantes no sistema,
 * seja para depuração, auditoria ou monitoramento, com a possibilidade de categorizar os logs por níveis (INFO, ERROR, DEBUG, etc).
 * 
 * Os logs são armazenados em arquivos específicos dentro de um diretório de logs, com a data no nome do arquivo, 
 * e cada mensagem de log é formatada com timestamp e nível.
 * 
 * @package GabineteMvc\Middleware
 */
class Logger {

    /**
     * Registra uma mensagem de log em um arquivo específico.
     *
     * Este método grava uma mensagem de log em um arquivo dentro do diretório 'logs'. O nome do arquivo é gerado 
     * a partir da data atual e do nome fornecido como base. Caso o diretório 'logs' não exista, ele será criado automaticamente.
     * 
     * A mensagem de log inclui um timestamp e o nível de log especificado (INFO, ERROR, DEBUG, etc.).
     * O nível padrão é 'INFO'. O nome do arquivo também é sanitizado para garantir que não haja caracteres inválidos.
     * 
     * @param string $filename Nome base do arquivo de log (sem extensão). Será sanitizado para evitar caracteres inválidos.
     *                         O nome do arquivo será composto pela data e o nome fornecido.
     * 
     * @param string $message Mensagem a ser registrada no log. Pode ser qualquer texto ou variável que precise ser registrada.
     * 
     * @param string $level Nível do log (INFO, ERROR, DEBUG, etc.). O nível ajuda a categorizar as mensagens. O valor padrão é 'INFO'.
     *                      Exemplos de níveis incluem:
     *                      - 'INFO': mensagens informativas, geralmente de rotina.
     *                      - 'ERROR': mensagens de erro ou falhas.
     *                      - 'DEBUG': informações detalhadas para depuração.
     * 
     * @return void Este método não retorna nenhum valor. O registro é feito diretamente no arquivo de log.
     * 
     * @throws \Exception Caso haja algum problema ao criar o diretório de logs ou ao gravar no arquivo de log, uma exceção pode ser lançada.
     */
    public static function novoLog(string $filename, string $message, string $level = 'INFO'): void {
        // Caminho do diretório onde os logs serão armazenados
        $logDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'logs';

        // Verifica se o diretório de logs existe. Caso não, cria o diretório com permissões adequadas
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Sanitiza o nome do arquivo para garantir que ele só contenha caracteres válidos
        $safeFilename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $filename);

        // Gera o nome completo do arquivo de log, com a data atual e o nome do arquivo sanitizado
        $logFile = sprintf('%s/%s_%s.log', $logDir, date('Y-m-d'), $safeFilename);

        // Formata a mensagem de log, incluindo o timestamp e o nível de log
        $formattedMessage = sprintf("[%s] [%s] %s%s", date('Y-m-d H:i:s'), strtoupper($level), $message, PHP_EOL);

        // Grava a mensagem formatada no arquivo de log, com a opção de adicionar ao final do arquivo sem sobrescrever
        file_put_contents($logFile, $formattedMessage, FILE_APPEND | LOCK_EX);
    }
}
