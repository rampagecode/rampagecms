<?php

namespace Sys\Mail;

use Swift;
use Swift_ByteStream_FileByteStream;
use Swift_Mailer;
use Swift_Message;
use Sys\Config\ConfigManager;
use Sys\Log\LoggerInterface;

class SwiftAdapter implements MailAdapter {
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Swift_Message
     */
    private $email;

    /**
     * @var ConfigManager
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $files = [];

    /**
     * @var array
     */
    private $texts = [];

    /**
     * @var array
     */
    private $htmls = [];

    /**
     * @var string
     */
    private $charset = 'utf-8';

    /**
     * @param ConfigManager $config
     * @param LoggerInterface $logger
     */
    public function __construct( ConfigManager $config, LoggerInterface $logger ) {
        $this->config = $config;
        $this->logger = $logger;

        if( $config->getVar('email_use_smtp' )) {
            $host = $config->getVar('smtp_server_addr' );
            $port = $config->getVar('smtp_server_port' );
            $transport = new \Swift_SmtpTransport( $host, $port );
        } else {
            $transport = new \Swift_SendmailTransport();
        }

        $this->mailer = new Swift_Mailer( $transport );
        $this->email = new Swift_Message();
        $this->email->setCharset( $this->charset );
    }

    /**
     * @param $text
     * @return $this
     */
    function setText( $text ) {
        $this->texts[] = $text;
        $this->email->attach( new Swift_Message( $this->email->getSubject(), $text, 'text/plain', $this->charset ));
        return $this;
    }

    /**
     * @param $html
     * @return $this
     */
    function setHTML( $html ) {
        $this->htmls[] = $html;

        if( !stristr( $html, '<html>' ) && !stristr( $html, '<body>' )) {
            $html = $this->wrapHTML( $html );
        }

        $this->email->attach( new Swift_Message( $this->email->getSubject(), $html, 'text/html', $this->charset ));
        return $this;
    }

    /**
     * @param $fileName
     * @param $displayName
     * @return $this
     * @throws \Swift_IoException
     */
    function addFile( $fileName, $displayName ) {
        $this->files[] = $fileName;
        $this->email->attach( new \Swift_Attachment( new Swift_ByteStream_FileByteStream( $fileName ), $displayName ));
        return $this;
    }

    /**
     * @param $email
     * @return $this
     */
    function setTo( $email ) {
        $this->email->setTo( $email );
        return $this;
    }

    /**
     * @param $email
     * @param $person
     * @return $this
     */
    function setFrom( $email, $person ) {
        $this->email->setFrom( $email, $person );
        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    function setSubject( $text ) {
        $this->setSubject( $text );
        return $this;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    function send() {
        if( empty( $this->_files ) and empty( $this->_texts ) and empty( $this->_htmls )) {
            throw new \Exception('Нечего отправлять');
        }

        $recipient = $this->email->getTo();

        if( empty( $recipient )) {
            throw new \Exception('Не указан адресат сообщения');
        }

        $from = $this->email->getFrom();

        if( empty( $from )) {
            $this->email->setFrom(
                $this->config->getVar('email_out'),
                $this->config->getVar('email_site_person')
            );
        }

        $subject = $this->email->getSubject();

        if( empty( $subject )) {
            $this->email->setSubject('Сообщение с сайта '.$this->config->getVar('site_name'));
        }

        return (bool)$this->mailer->send( $this->email );
    }

    /**
     * @param string $html
     * @return string
     */
    private function wrapHTML( $html ) {
        $title   = 'Письмо с сайта '.$this->config->getVar('site_name');
        $charset = $this->charset;

        return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$charset}">
    <title>{$title}</title>    
</head>
<body>
    {$html}
</body>
</html>
EOF;
    }
}