<?php declare(strict_types=1);

/**
 * Mumsys_Mvc_Helper_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Mvc
 * Created: 2016-01-30
 */

/**
 * Abstact display helper for additional functionality
 */
abstract class Mumsys_Mvc_Helper_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '2.0.0';

    /**
     * Display interface which init this class.
     *
     * @var Mumsys_Mvc_Display_Control_Interface
     */
    protected $_display;

    /**
     * Context item.
     *
     * @var Mumsys_Context_Interface
     */
    protected $_context;

    /**
     * Helper options/ configuration vars.
     *
     * @var array
     */
    protected $_options;


    /**
     * Initialize the helper controller
     *
     * @param Mumsys_Mvc_Display_Control_Interface $display Display which inits this helper
     * @param Mumsys_Context_Interface $context Context object
     * @param array $helperOptions Options to set on construction
     */
    public function __construct( Mumsys_Mvc_Display_Control_Interface $display,
        Mumsys_Context_Interface $context,
        array $helperOptions = array() )
    {
        $this->_display = $display;
        $this->_options = $helperOptions;
        $this->_context = $context;
    }
}
