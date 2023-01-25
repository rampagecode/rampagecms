<?php

namespace Sys\Mail;

interface MailAdapter {
    /**
     * @param $text
     * @return $this
     */
    function setText( $text );

    /**
     * @param $html
     * @return $this
     */
    function setHTML( $html );

    /**
     * @param $email
     * @return $this
     */
    function setTo( $email );

    /**
     * @param $email
     * @param $person
     * @return $this
     */
    function setFrom( $email, $person );

    /**
     * @param $text
     * @return $this
     */
    function setSubject( $text );

    /**
     * @param $fileName
     * @param $displayName
     * @return $this
     */
    function addFile( $fileName, $displayName );

    /**
     * @return bool
     */
    function send();
}