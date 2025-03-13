<?php

namespace GabineteMvc\Middleware;

use GabineteMvc\Middleware\Logger;

class GetJson
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function pegarDadosURL($url)
    {
        try {
            $ch = curl_init();

            // Configurar CURL para incluir os cabeçalhos na resposta
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Accept: application/json"
            ]);
            curl_setopt($ch, CURLOPT_HEADER, true); // Incluir cabeçalhos na resposta

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);

            // Separar cabeçalhos do corpo da resposta
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);

            curl_close($ch);

            if ($response === false) {
                $erro_id = uniqid();
                $this->logger->novoLog('get_json_log', $curlError . ' | ' . $erro_id);
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }

            if ($httpCode < 200 || $httpCode >= 300) {
                $erro_id = uniqid();
                $this->logger->novoLog('get_json_log', 'http_code - ' . $httpCode . ' | ' . $erro_id);
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }

            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $erro_id = uniqid();
                $this->logger->novoLog('get_json_log', json_last_error_msg() . ' | ' . $erro_id);
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }

            return [
                "status" => "success",
                "dados" => isset($data['dados']) ? $data['dados'] : $data,
                "headers" => $this->parseHeaders($headers) // Parse cabeçalhos
            ];
        } catch (\Exception $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('get_json_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // Função para parsear os cabeçalhos
    private function parseHeaders($headers)
    {
        $headerArray = [];
        $headerLines = explode("\r\n", trim($headers));
        foreach ($headerLines as $line) {
            $parts = explode(": ", $line, 2);
            if (count($parts) === 2) {
                $headerArray[$parts[0]] = $parts[1];
            }
        }
        return $headerArray;
    }
}
