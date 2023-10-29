<?php

/**
 * Mumsys_Mailer_Interface
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2014 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mail
 * @version     1.0.0
 * Created on 01.12.2006 improved since 2016, init interface
 * $Id: class.mailsys.php 2369 2011-12-08 22:02:37Z flobee $
 */


/**
 * Mumsys mail interface as part of MailSYS newsletter system.
 *
 * This is the interface which uses MUMSYS at all.
 * Calling the driver class directly is not prohibited
 * and possible but: dont use it! Improve the interface then!
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mail
 */
interface Mumsys_Mail_Interface
{
    /**
     * Initialize the mail object.
     *
     * @param array $config Config array containing credetials/ mail interface
     * properties like:
     *  - adapter - mail, smtp, sendmail
     *  - username - login name for the mail server
     *  - password - Password for the mailserver
     *  - hostname - Hostname or IP of the mailserver
     *  - port - Port of the mail sever
     *  - smtp_auth - boolean using smtp auth or not
     *  - smtp_keepalive - boolean keep connection alive or not
     *  - smtp_debug - Debugging options, driver specific 0=Off,1=client,2=
     * server and client
     *  - smtp_secure - Sets the encryption system to use - ssl (deprecated) or
     * tls (new)
     *  - smtp_options - Futher smtp option driver specific
     *  - wordwrap -  Mail text wordwrap. Leave it (default is 78) change it
     * only if you know what you are doing,
     *  - mail_from_email - Sender email address. Uses setFrom() on construction
     *  - mail_from_name  - Sender name. Uses setFrom() on construction
     *  - xmailer     X-Mailer header to replace.
     *  - charset     mail character set defaut: utf-8
     *  - certificate (array)  certOptions
     *      'cert' The location of your certificate file e.g '/path/to/cert.crt',
     *      'privateKey' - The location of your private key file e.g:
     * '/path/to/cert.key',
     *      'pass' - The password you protected your private key with (not the
     * Import Password! may be empty but parameter must not be omitted!)
     *      'chain' - Optional path to chain certificateThe location to your
     *              chain file e.g.: '/path/to/certchain.pem'
     *
     * @throws Exception If config not set
     */
    public function __construct( array $config );


    /**
     * Call a mail driver method which is not implemented in this interface.
     *
     * @note Dont use it! update the interface would be the best choice!
     *
     * @param string $name Name of the methode to call
     * @param array $params Parameter/s to pipe the the methode
     */
    public function __call( $name, array $params = array() );


    /**
     * Adds a recipient address and name.
     *
     * @param string $email Email address to add
     * @param string $name Name of the person the email belongs to
     */
    public function addTo( $email, $name );


    /**
     * Adds a "copy to" recipient address and name.
     *
     * @param string $email Email address to add
     * @param string $name Name of the person the email belongs to
     */
    public function addCc( string $email, string $name = '' );


    /**
     * Adds a "blind copy to" recipient address and name.
     *
     * @param string $email Email address to add
     * @param string $name Name of the person the email belongs to
     */
    public function addBcc( string $email, string $name = '' );


    /**
     * Adds a "Reply-To" address and name.
     *
     * @param string $email The email address to reply to
     * @param string $name Name of the person the email belongs to
     *
     * @return boolean True on success, false if address already used or invalid
     */
    public function addReplyTo( string $email, string $name = '' );


    /**
     * Sets the "from" and "from name" properties.
     *
     * @param string $email Email address
     * @param string $name Name of the person the email belongs to
     * @param boolean $auto On true (default) sets the Sender address (Return
     * -Path) of the message
     */
    public function setFrom( string $email, $name = '', $auto = true );


    /**
     * Sets the (Return-Path) of the message.
     *
     * The Sender email (Return-Path) will be sent via -f to sendmail or as
     * 'MAIL FROM' in smtp mode. For more @see setFrom() "auto" property
     *
     * @param string $email Return-Path email address
     *
     * @return bool Returns true on success
     */
    public function setReturnTo( string $email ): bool;


    /**
     * Sets the mail message as html code.
     *
     * @param string $htmlCode Html code to set as message
     * @param string $pathInlineAttachments Path to the files to add images as
     * inline attachments if given/set in html code
     *
     * @return string Html message string
     */
    public function setMessageHtml( string $htmlCode,
        string $pathInlineAttachments = '' ): string;


    /**
     * Sets the mail message as text format.
     *
     * @param string $text Plain text message string
     *
     * @return string $message Message text
     */
    public function setMessageText( $text ): string;


    /**
     * Adds an attachment from a path on the filesystem.
     *
     * @param string $location Location to the attachment on the filesystem.
     * @param string $encoding File encoding (e.g base64)
     * @param string $type File extension (MIME) type.
     * @param string $disposition Disposition to use
     *
     * @return boolean Returns false if the file could not be found or read.
     */
    public function addAttachment( $location, $encoding = 'base64', $type = '',
        $disposition = 'attachment' ): bool;


    /**
     * Return the list of attachments.
     * @return array
     */
    public function getAttachments(): array;


    /**
     * Sends a mail message.
     *
     * @return boolean True on success or false on error
     */
    public function send(): bool;


    /**
     * Set certificates for private/public key authentication e.g. for S/MIME.
     *
     * @param string $certFile Location to certificate file e.g.
     * "/etc/certs/cert.crt"
     * @param string $privateKeyFile Location to private key file
     * @param string $keyPwd Password for private key if set/needed
     * @param string $chain Optional path to chain certificate
     *
     * @return boolean Returns true on success
     */
    public function setCertificate( string $certFile, string $privateKeyFile,
        string $keyPwd = '', string $chain = '' ): bool;


    /**
     * Adds a custom header.
     *
     * @param string $name Header name
     * @param string $value Header value
     */
    public function addCustomHeader( string $name, string $value = null ): bool;


    /**
     * Returns all custom headers.
     *
     * @return array List of key value pairs of all headers set
     */
    public function getCustomHeaders(): array;

}
