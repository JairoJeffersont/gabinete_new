<?php

namespace GabineteMvc\Middleware;

/**
 * Classe FileUploader
 * 
 * A classe `FileUploader` é responsável pelo gerenciamento de uploads de arquivos, incluindo:
 * - Verificação do tipo e tamanho dos arquivos.
 * - Armazenamento dos arquivos em um diretório específico.
 * - Exclusão de arquivos do servidor.
 * 
 * Ela também oferece suporte a verificação de erros no processo de upload e garantias de segurança, 
 * como verificação de tipo MIME e a criação automática de diretórios de destino quando necessário.
 * 
 * @package GabineteMvc\Middleware
 */
class FileUploader {

    /**
     * Faz o upload de um arquivo para um diretório especificado.
     *
     * Este método realiza o upload de um arquivo enviado via formulário, verificando se o tipo de arquivo é permitido,
     * se o tamanho do arquivo está dentro do limite estipulado e se o diretório de destino existe ou pode ser criado.
     * Ele também pode gerar um nome único para o arquivo para evitar conflitos com arquivos já existentes.
     * 
     * @param string $directory Diretório onde o arquivo será armazenado no servidor.
     * 
     * @param array $file Dados do arquivo, normalmente provenientes do array $_FILES.
     * 
     * @param array $allowedTypes Tipos MIME permitidos para upload. Exemplos: ['image/jpeg', 'image/png'].
     * 
     * @param int $maxSize Tamanho máximo do arquivo em MB. O arquivo não pode ultrapassar esse tamanho.
     * 
     * @param bool $uniqueFlag Se verdadeiro, um nome único será gerado para o arquivo, evitando conflitos de nomes.
     * 
     * @return array Retorna um array associativo com o status do upload e uma mensagem. Em caso de sucesso, inclui também o caminho do arquivo.
     *               - 'status' (string): o status da operação, que pode ser 'success' ou diferentes mensagens de erro.
     *               - 'message' (string): uma mensagem explicativa sobre o status da operação.
     *               - 'file_path' (string, opcional): caminho do arquivo no servidor, disponível apenas quando o upload é bem-sucedido.
     */
    public function uploadFile(string $directory, array $file, array $allowedTypes, int $maxSize, bool $uniqueFlag = true): array {
        // Verifica se houve erro no upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['status' => 'upload_error', 'message' => 'Erro ao fazer upload.'];
        }

        // Obtém a extensão real do arquivo e verifica o tipo MIME
        $fileMime = mime_content_type($file['tmp_name']);
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Verifica se o tipo do arquivo é permitido
        if (!in_array($fileMime, $allowedTypes, true)) {
            return ['status' => 'format_not_allowed', 'message' => 'Tipo de arquivo não permitido.'];
        }

        // Verifica se o tamanho do arquivo excede o limite
        if ($file['size'] > $maxSize * 1024 * 1024) {
            return ['status' => 'max_file_size_exceeded', 'message' => "O arquivo excede o limite de {$maxSize} MB."];
        }

        // Garante que o diretório existe ou tenta criá-lo
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return ['status' => 'directory_creation_failed', 'message' => 'Não foi possível criar o diretório de destino.'];
        }

        // Gera um nome único ou mantém o nome original
        $fileName = $uniqueFlag ? uniqid('file_') . '.' . $fileExtension : $file['name'];
        $destination = $directory . DIRECTORY_SEPARATOR . $fileName;

        // Verifica se o arquivo já existe no diretório de destino
        if (file_exists($destination)) {
            return ['status' => 'file_already_exists', 'message' => 'Arquivo já existe no diretório.'];
        }

        // Move o arquivo para o diretório de destino
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['status' => 'move_failed', 'message' => 'Erro ao mover o arquivo.'];
        }

        // Retorna o status de sucesso junto com o caminho do arquivo
        return ['status' => 'success', 'message' => 'Upload realizado com sucesso.', 'file_path' => str_replace('\\', '/', $destination)];
    }

    /**
     * Exclui um arquivo do servidor.
     *
     * Este método remove o arquivo do servidor de acordo com o caminho fornecido. Ele verifica se o arquivo existe 
     * antes de tentar excluí-lo e retorna o status da operação.
     * 
     * @param string $filePath Caminho completo do arquivo a ser excluído, incluindo o diretório.
     * 
     * @return array Retorna um array associativo com o status da exclusão e uma mensagem explicativa.
     *               - 'status' (string): o status da operação, que pode ser 'success' ou diferentes mensagens de erro.
     *               - 'message' (string): uma mensagem explicativa sobre o status da operação.
     */
    public function deleteFile(string $filePath): array {
        // Ajusta o caminho para usar o separador correto de diretórios
        $filePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filePath);

        // Verifica se o arquivo existe no sistema
        if (!file_exists($filePath)) {
            return ['status' => 'file_not_found', 'message' => 'Arquivo não encontrado.'];
        }

        // Tenta excluir o arquivo
        if (!unlink($filePath)) {
            return ['status' => 'delete_failed', 'message' => 'Erro ao excluir o arquivo.'];
        }

        // Retorna o status de sucesso após excluir o arquivo
        return ['status' => 'success', 'message' => 'Arquivo excluído com sucesso.'];
    }
}
