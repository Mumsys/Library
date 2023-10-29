<?php declare(strict_types=1);

/**
 * Mumsys_Cache_Default
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <[ba|z|a]sh: echo 1l2b33.code@EmAil.c2m | tr 123AE foeag>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Cache
 * Created: 2013-12-10
 */


/**
 * Default class for file caching.
 *
 * This class uses Mumsys_Cache_File as default implementation. Future
 * implementations will use a factory to get the right cache class.
 *
 * Example:
 * <code>
 * $cache = new Mumsys_Cache_Default('group-ID', 'individual-ID');
 * $cache->setPath(__DIR__ . '/cache/');
 * if ($cache->isCached()) {
 *      // get the cached data
 *      $data = unserialize( $oCache->read() );
 * } else {
 *      $data = array('a' => 1, 'b' => 2, 'c' => 3);
 *      $cache->write( 3600, serialize($data) );
 * }
 * </code>
 */
class Mumsys_Cache_Default
    extends Mumsys_Cache_File
    implements Mumsys_Cache_Interface
{
    public const VERSION = Mumsys_Cache_File::VERSION;
}
