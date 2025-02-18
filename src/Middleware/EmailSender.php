<?php

namespace GabineteMvc\Middleware;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use GabineteMvc\Middleware\Logger;

/**
 * Classe EmailSender
 * 
 * A classe `EmailSender` é responsável pelo envio de e-mails utilizando a biblioteca PHPMailer.
 * Além disso, ela registra erros de envio de e-mails em logs utilizando a classe `Logger`.
 * A configuração de e-mail é carregada a partir de um arquivo de configuração (`config.php`), 
 * garantindo que as credenciais e parâmetros de SMTP sejam armazenados de forma segura.
 * 
 * @package GabineteMvc\Middleware
 */
class EmailSender {
    /** 
     * @var PHPMailer Instância do PHPMailer para envio de e-mails.
     */
    private $mailer;

    /** 
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /** 
     * @var array Configurações de e-mail carregadas do arquivo `config.php`.
     * Contém as credenciais do SMTP, informações de remetente, etc.
     */
    private $config;

    /**
     * Construtor da classe EmailSender.
     * 
     * Carrega as configurações de e-mail do arquivo `config.php` e inicializa o PHPMailer e o Logger.
     * 
     * O construtor é responsável por verificar se o arquivo de configuração existe, se a configuração de e-mail está presente 
     * e por instanciar as dependências da classe.
     * 
     * @throws \Exception Lança uma exceção caso o arquivo de configuração não seja encontrado ou esteja ausente.
     */
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->logger = new Logger();

        $configPath = dirname(__DIR__, 2) . '/src/Configs/config.php';

        // Verifica se o arquivo de configuração existe
        if (!file_exists($configPath)) {
            $this->logger->novoLog('email_log', 'Arquivo de configuração do e-mail não encontrado.');
            throw new \Exception('Arquivo de configuração não encontrado.');
        }

        $config = require $configPath;

        // Verifica se a configuração de e-mail está presente
        if (!isset($config['email'])) {
            $this->logger->novoLog('email_log', 'Configuração de e-mail ausente no arquivo de configuração.');
            throw new \Exception('Configuração de e-mail ausente.');
        }

        // Atribui as configurações de e-mail
        $this->config = $config['email'];
    }

    /**
     * Método responsável pelo envio de um e-mail.
     * 
     * Este método configura o PHPMailer com as credenciais e parâmetros definidos na configuração,
     * define o remetente, destinatário, assunto e corpo do e-mail, e tenta enviar o e-mail.
     * Se o envio for bem-sucedido, retorna um status de sucesso.
     * Caso ocorra algum erro, ele será registrado no log, e uma mensagem de erro será retornada.
     * 
     * @param string $toEmail Endereço de e-mail do destinatário.
     * @param string $assunto Assunto do e-mail.
     * @param string $message Conteúdo da mensagem a ser enviada no corpo do e-mail.
     * 
     * @return array Retorna um array associativo contendo o status da operação (`'success'` ou `'email_send_failed'`)
     *               e uma mensagem explicativa. Em caso de erro, também retorna um `error_id` único para rastreamento.
     */
    public function sendEmail(string $toEmail, string $assunto, string $message): array {
        try {
            // Configuração do PHPMailer com as credenciais e parâmetros do arquivo de configuração
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['smtp_host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Port = $this->config['smtp_port'];
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Username = $this->config['smtp_user'];
            $this->mailer->Password = $this->config['smtp_password'];
            $this->mailer->Sender = $this->config['smtp_sender'];
            $this->mailer->From = $this->config['smtp_from'];
            $this->mailer->FromName = $this->config['smtp_from_name'];
            $this->mailer->addAddress($toEmail); // Destinatário
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';

            // Definição do corpo e assunto do e-mail
            $this->mailer->isHTML(true); // Define que o corpo do e-mail é HTML
            $this->mailer->Subject = $assunto;
            $this->mailer->Body = $message;

            // Envia o e-mail
            $this->mailer->send();

            // Retorna status de sucesso
            return ['status' => 'success', 'message' => 'E-mail enviado com sucesso.'];
        } catch (Exception $e) {
            // Em caso de erro, gera um ID único para o erro
            $erro_id = uniqid();

            // Registra o erro no log
            $this->logger->novoLog('email_log', 'Erro ao enviar e-mail: ' . $e->getMessage() . ' | ID: ' . $erro_id);

            // Retorna status de erro com ID para rastreamento
            return ['status' => 'email_send_failed', 'message' => 'Erro ao enviar e-mail.', 'error_id' => $erro_id];
        }
    }
}
