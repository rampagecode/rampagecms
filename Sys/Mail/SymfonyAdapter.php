<?php

namespace Sys\Mail;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Sys\Config\ConfigManager;
use Sys\Log\LoggerInterface;

class SymfonyAdapter implements MailAdapter {
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var Email
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
     * @param ConfigManager $config
     * @param LoggerInterface $logger
     */
    public function __construct( ConfigManager $config, LoggerInterface $logger ) {
        $this->config = $config;
        $this->logger = $logger;

        if( $config->getVar('email_use_smtp' )) {
            $host = $config->getVar('smtp_server_addr' );
            $port = $config->getVar('smtp_server_port' );

            $transport = Transport::fromDsn("smtp://{$host}:{$port}");

            //Alternative way:
            //$transport = new Transport\Smtp\SmtpTransport( $this->createSocketStream( $host, $port ));
        } else {
            $transport = new SendmailTransport();
        }

        $this->mailer = new Mailer( $transport );
        $this->email = new Email();
    }

    /**
     * @param $text
     * @return $this
     */
    public function setText( $text ) {
        $this->email->text( $text );
        return $this;
    }

    /**
     * @param $html
     * @return $this
     */
    public function setHTML( $html ) {
        $this->email->html( $html );
        return $this;
    }

    /**
     * @param $email
     * @return $this
     */
    public function setTo( $email ) {
        $this->email->to( $email );
        return $this;
    }

    /**
     * @param $email
     * @param $person
     * @return $this
     */
    public function setFrom( $email, $person ) {
        $this->email->from( new Address( $email, $person ));
        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function setSubject( $text ) {
        $this->email->subject( $text );
        return $this;
    }

    /**
     * @param $fileName
     * @param $displayName
     * @return $this
     */
    public function addFile( $fileName, $displayName ) {
        $this->email->attachFromPath( $fileName, $displayName );
        return $this;
    }

    /**
     * @return bool
     */
    public function send() {
        $from = $this->email->getFrom();

        if( empty( $from )) {
            $this->email->from(
                new Address(
                    $this->config->getVar('email_out'),
                    $this->config->getVar('email_site_person')
                )
            );
        }

        $subject = $this->email->getSubject();

        if( empty( $subject )) {
            $this->email->subject('Сообщение с сайта '.$this->config->getVar('site_name'));
        }

        try {
            $this->mailer->send( $this->email );
            return true;
        }
        catch( TransportExceptionInterface $e ) {
            $this->logger->add( $e->getMessage() );
            return false;
        }
        catch( \Exception $e ) {
            $this->logger->add($e->getMessage());
            return false;
        }
    }

    /**
     * @param $host
     * @param $port
     * @return Transport\Smtp\Stream\SocketStream
     */
    private function createSocketStream( $host, $port ) {
        $socketStream = new Transport\Smtp\Stream\SocketStream();
        $socketStream->setHost($host);
        $socketStream->setPort($port);

        if (465 !== $port) {
            $socketStream->disableTls();
        }

        return $socketStream;
    }
}