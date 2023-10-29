<?php declare(strict_types=1);

/**
 * Mumsys_Mail_PHPMailer
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2017 by Florian Blasel for FloWorks Company
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
 * Mumsys PHPMailer interface
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mail
 */
class Mumsys_Mail_PHPMailer
    implements Mumsys_Mail_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '3.0.1';

    /**
     * Mail driver to be used.
     * @var \PHPMailer\PHPMailer\PHPMailer
     */
    private $_mailer;


    /**
     * Initialize the mailer object.
     *
     * @param array $config Config array containing credetials/ mail interface
     * properties like:
     *  adapter - mail, smtp, sendmail
     *  username - login name for the mail server
     *  password - Password for the mailserver
     *  hostname - Hostname or IP of the mailserver
     *  port - Port of the mail sever
     *  smtp_auth - boolean using smtp auth or not
     *  smtp_keepalive - boolean keep connection alive or not
     *  smtp_debug - Debugging options, driver specific 0=Off,1=client,2=server
     * and client
     *  smtp_secure - Sets the encryption system to use - ssl (deprecated) or
     * tls (new)
     *  smtp_options - Futher smtp option driver specific
     *  wordwrap -  Mail text wordwrap. Leave it (default is 78) change it only
     *              if you know what you are doing,
     * mail_from_email - Sender email address. Uses setFrom() on construction
     * mail_from_name  - Sender name. Uses setFrom() on construction
     *  xmailer     X-Mailer header to replace.
     *  charset     mail character set defaut: utf-8
     * certificate (array)  certOptions
     *      'cert' The location of your certificate file e.g '/path/to/cert.crt',
     *      'privateKey' - The location of your private key file e.g:
     * '/path/to/cert.key',
     *      'pass' - The password you protected your private key with (not the
     * Import Password! may be empty but parameter must not be omitted!)
     *      'chain' - Optional path to chain certificateThe location to your
     *              chain file e.g.: '/path/to/certchain.pem'
     * @throws Exception If config not set or loading of base mailer fails
     */
    public function __construct( array $config )
    {
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
        $this->_mailer = new \PHPMailer\PHPMailer\PHPMailer( true );

        if ( isset( $config['adapter'] ) ) {
            switch ( $config['adapter'] ) {
                case 'smtp':
                    require_once '../vendor/phpmailer/phpmailer/src/POP3.php';
                    require_once '../vendor/phpmailer/phpmailer/src/SMTP.php';
                    break;
            }

            $this->setTransportWay( (string) $config['adapter'] );
        }

        if ( isset( $config['username'] ) ) {
            $this->_mailer->Username = (string) $config['username'];
        }

        if ( isset( $config['password'] ) ) {
            $this->_mailer->Password = (string) $config['password'];
        }

        if ( isset( $config['hostname'] ) ) {
            $this->_mailer->Host = (string) $config['hostname'];
        }

        if ( isset( $config['port'] ) ) {
            $this->_mailer->Port = (int) $config['port'];
        }

        if ( isset( $config['smtp_auth'] ) ) {
            $this->_mailer->SMTPAuth = (boolean) $config['smtp_auth'];
        }

        if ( isset( $config['smtp_keepalive'] ) ) {
            $this->_mailer->SMTPKeepAlive = (boolean) $config['smtp_keepalive'];
        }

        if ( isset( $config['smtp_debug'] ) ) {
            $this->_mailer->SMTPDebug = (int) $config['smtp_debug'];
        }

        if ( isset( $config['smtp_secure'] ) ) {
            $this->_mailer->SMTPSecure = (string) $config['smtp_secure'];
        }

        if ( isset( $config['smtp_options'] ) ) {
            $this->_mailer->SMTPOptions = (array) $config['smtp_options'];
        }

        if ( isset( $config['certificate'] ) && $config['certificate'] ) {
            $opts = $config['certificate'];
            $this->_mailer->sign(
                $opts['cert'], $opts['privateKey'], $opts['pass'], $opts['chain']
            );
        }

        if ( isset( $config['wordwrap'] ) ) {
            $this->_mailer->WordWrap = (int) $config['wordwrap'];
        }

        if ( isset( $config['mail_from_email'] ) && $config['mail_from_email'] ) {
            if ( isset( $config['mail_from_name'] ) ) {
                $fromName = $config['mail_from_name'];
            } else {
                $fromName = $config['mail_from_email'];
            }

            $this->setFrom( $config['mail_from_email'], $fromName );
        }

        if ( isset( $config['xmailer'] ) ) {
            $this->_mailer->XMailer = (string) $config['xmailer'];
        }

        if ( isset( $config['charset'] ) ) {
            $this->setCharset( (string) $config['charset'] );
        }

        $this->_mailer->Debugoutput = 'error_log';
    }


    /**
     * Magic method call to the mailer driver.
     *
     * E.g.: $mailer = $oMumsysMail->callSomeMethode();
     * $mailer->addCc('email');
     *
     * @param string $name Name of the methode of the driver to be called
     * @param array $params Parameters to pipe the method
     *
     * @return mixed
     * @throws Mumsys_Mail_Exception If method not exists/ implemented
     */
    public function __call( $name, array $params = array() )
    {
        if ( method_exists( $this->_mailer, $name ) ) {
            /** @var callable $cb 4SCA */
            $cb = array($this->_mailer, $name);
            return call_user_func_array( $cb, $params );
        }

        $mesg = sprintf( 'Method "%1$s" not implemented', $name );
        throw new Mumsys_Mail_Exception( $mesg );
    }


    /**
     * Returns the mailer object.
     *
     * @return mixed Implemented mailer driver
     */
    public function getMailer()
    {
        return $this->_mailer;
    }


    /**
     * Adds a reciepient (To) address.
     *
     * @param string $email The email address to send to
     * @param string $name Name of the person
     * @return boolean true on success, false if address already used or invalid
     * in some way
     */
    public function addTo( $email, $name = '' )
    {
        return $this->_mailer->addAddress( $email, $name );
    }


    /**
     * Add a CC reciepent address.
     *
     * @note: This function works with the SMTP mailer on win32, not with the
     * "mail" mailer.
     *
     * @param string $email The email address to send to
     * @param string $name Name of the person
     *
     * @return boolean true on success, false if address already used or invalid
     * in some way
     */
    public function addCc( $email, $name = '' )
    {
        return $this->_mailer->addCC( $email, $name );
    }


    /**
     * Add a blind copy to address (BCC).
     *
     * @note: This function works with the SMTP mailer on win32, not with the
     * "mail" mailer.
     *
     * @param string $email The email address to send to
     * @param string $name Name of the person
     *
     * @return boolean true on success, false if address already used or invalid
     * in some way
     */
    public function addBcc( $email, $name = '' )
    {
        return $this->_mailer->addBCC( $email, $name );
    }


    /**
     * Set the From and FromName properties.
     *
     * @param string $email The "From" email address
     * @param string $name The "From Name" name
     * @param boolean $auto Whether to also set the Sender address, defaults to
     * false (server specific)
     *
     * @return boolean True on success, false on failure
     *
     * @throws \PHPMailer\PHPMailer\Exception Phpmailer Exception
     */
    public function setFrom( $email, $name = '', $auto = false ): bool
    {
        return $this->_mailer->setFrom( $email, $name, $auto );
    }


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
    public function setReturnTo( string $email ): bool
    {
        $this->_mailer->Sender = $email;

        return true;
    }


    /**
     * Adds a "Reply-To" address.
     *
     * @param string $email The email address to reply to
     * @param string $name Name of the person the email belongs to
     *
     * @return boolean true on success, false if address already used or invalid
     * in some way
     */
    public function addReplyTo( string $email, string $name = '' )
    {
        return $this->_mailer->addReplyTo( $email, $name );
    }


    /**
     * Sets the subject of the mail.
     *
     * @param string $subject
     */
    public function setSubject( $subject )
    {
        $this->_mailer->Subject = (string) $subject;
    }


    /**
     * Sets the mail message (default from an HTML string).
     *
     * Automatically makes modifications for inline images and backgrounds
     * and creates a plain-text version by converting the HTML.
     * Overwrites any existing values in $this->Body and $this->AltBody
     * @see setMessageText()
     *
     * @param string $htmlMsg HTML message string
     * @param string $pathInlineAtt base directory for inline attachments
     * @param boolean|callable $advanced Whether to use the internal HTML to text
     * converter or your own custom converter @see PHPMailer::html2text()
     *
     * @return string Html message string
     */
    public function setMessage( string $htmlMsg, string $pathInlineAtt = '',
        $advanced = false ): string
    {
        return $this->_mailer->msgHTML( $htmlMsg, $pathInlineAtt, $advanced );
    }


    /**
     * Sets the mail message (default from an HTML string).
     *
     * Automatically makes modifications for inline images and backgrounds
     * and creates a plain-text version by converting the HTML.
     * Overwrites any existing values in $this->Body and $this->AltBody
     * @see setMessageText()
     *
     * @param string $htmlMsg HTML message string
     * @param string $pathInlineAtt base directory for inline attachments
     * @param false|callable $advanced Whether to use the internal HTML to
     * text converter or your own custom converter @see PHPMailer::html2text()
     *
     * @return string Html message string
     */
    public function setMessageHtml( string $htmlMsg, string $pathInlineAtt = '',
        $advanced = false ): string
    {
        return $this->_mailer->msgHTML( $htmlMsg, $pathInlineAtt, $advanced );
    }


    /**
     * Adds the plaintext/ alternativ message.
     * Default is html message @see setMessage().
     *
     * @param string $message Plain text string
     *
     * @return string $message Message text
     */
    public function setMessageText( $message ): string
    {
        return $this->_mailer->AltBody = $message;
    }


    /**
     * Add an attachment from a path on the filesystem.
     *
     * @param string $location Location to the attachment
     * @param string $name Overrides the attachment name
     * @param string $encoding File encoding (eg: base64)
     * @param string $type File extension (MIME) type.
     * @param string $disposition Disposition to use
     *
     * @return boolean Returns false if the file could not be found or read.
     * @throws \PHPMailer\PHPMailer\Exception Phpmailer exception
     */
    public function addAttachment( $location, $name = '', $encoding = 'base64',
        $type = '', $disposition = 'attachment' ): bool
    {
        return $this->_mailer->addAttachment(
            $location, $name, $encoding, $type, $disposition
        );
    }


    /**
     * Return the list of attachments.
     *
     * @return array
     */
    public function getAttachments(): array
    {
        return $this->_mailer->getAttachments();
    }


    /**
     * Sets the content type of the mail: text (plain text) or html for html code.
     *
     * @param string $type Type to set: text|html
     */
    public function setContentType( $type = 'text' )
    {
        $test = false;
        if ( $type != 'text' ) {
            $test = true;
        }

        $this->_mailer->isHTML( $test );
    }


    /**
     * Sets the character set of the mail message content.
     * Default: iso-8859-1
     *
     * @param string $charset Character set
     */
    public function setCharset( $charset = 'iso-8859-1' )
    {
        $this->_mailer->CharSet = $charset;
    }


    /**
     * Sets the transport way.
     *
     * @param string $way Transport way to be set, one of mail, smtp, sendmail,
     * qmail
     */
    public function setTransportWay( $way = 'mail' )
    {
        switch ( $way )
        {
            case 'mail':
                $this->_mailer->isMail();
                break;

            case 'smtp':
                $this->_mailer->isSMTP();
                break;

            case 'sendmail':
                $this->_mailer->isSendmail();
                break;

            default:
                // use default: php mail()
                break;
        }
    }


    /**
     * Set the language for error messages.
     *
     * Returns false if it cannot load the language file.
     * The default language is English.
     *
     * @param string $langCode ISO 639-1 2-character language code (e.g. French
     * is "fr")
     * @param string $pathLang Path to the language file directory, with
     * trailing separator (slash)
     *
     * @return boolean Returns false if it cannot load the language file.
     */
    public function setLanguage( $langCode = 'en', $pathLang = '' ): bool
    {
        return $this->_mailer->setLanguage( $langCode, $pathLang );
    }


    /**
     * Sends the mail message.
     *
     * @return boolean Returns true on success
     *
     * @throws Exception On errors
     */
    public function send(): bool
    {
        return $this->_mailer->send();
    }


    /**
     * Set certificate for private/public key authentication e.g. for S/MIME.
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
        string $keyPwd = '', string $chain = '' ): bool
    {
        $this->_mailer->sign( $certFile, $privateKeyFile, $keyPwd, $chain );

        return true;
    }


    /**
     * Adds a custom header.
     *
     * @param string $name Header name
     * @param string|null $value Header value
     */
    public function addCustomHeader( string $name, string $value = null ): bool
    {
        $this->_mailer->addCustomHeader( $name, $value );

        return true;
    }


    /**
     * Returns all custom headers.
     *
     * @return array List of key value pairs of all headers set
     */
    public function getCustomHeaders(): array
    {
        return $this->_mailer->getCustomHeaders();
    }

}
