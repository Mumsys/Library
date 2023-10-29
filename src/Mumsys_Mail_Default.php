<?php

/**
 * Mumsys_Mail_Default
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2017 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mail
 * Created on 01.12.2006 improved since 2016, init interface
 * $Id: class.mailsys.php 2369 2011-12-08 22:02:37Z flobee $
 */


/**
 * Default mailer as wrapper currently to PHPMailer
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mail
 */
class Mumsys_Mail_Default
    extends Mumsys_Mail_PHPMailer
    implements Mumsys_Mail_Interface
{
    /**
     * Version ID information
     */
    const VERSION = '3.0.0';

}
