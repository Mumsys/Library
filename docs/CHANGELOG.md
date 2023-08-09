<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of contents**

+ [Changes](#changes)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->



# Changes

2023-08

General fixes:
+ Improves CS, SCA which came up with some tests, e.g:
  Context, Array2Xml, 

Dependencies:
+ Updates/ Set dependency 
    - `phpstan` to version: ^1.10
      ./runStaticCodeAnalysis.sh --level=4 ../src/ ./src # => 0 issues
      ./runStaticCodeAnalysis.sh --level=8 ../src/ ./src # => 1.7k issues
      ./runStaticCodeAnalysis.sh --level=8 ../src/ # => 698 issues
    - phpunit/phpunit (9.5.16 => 9.6.10)
    - phing/phing (2.17.2 => 2.17.4)
    - phpmailer/phpmailer (v6.6.0 => v6.8.0)
    - phpunit/php-code-coverage (9.2.14 => 9.2.27)
    - squizlabs/php_codesniffer (3.6.2 => 3.7.2)

+ Mumsys_Service_Spss*
    - Relocates testfiles/Service => testfiles/Domain/Service
    - Updates tests

+ Mumsys_Service_Ssh*
    - Relocates testfiles/Service => testfiles/Domain/Service
    - Updates tests

+ Mumsys_GetOpts
    - Fixes SCA (Static Code Analyis) (phpstan level=8; src/! not for tests!)
    - Fixes CS

+ Mumsys_Parser_*
    - Fixes parse() methode in error handling.
        - Adds second parameter `parse($logline, bool $stayStrict = true)` for
          beeing/ stay more strict (prev. and default behavior now: throw
          exception on regex error OR if no match found
    - Moves complete base functions to abstract class
    - Adds new classes:
        - `Mumsys_Parser_Abstract` Base implementation now
        - `Mumsys_Parser_Default`: With default patters as feature/ offer you may like
          Each variant/ flavour now only contains configs for that flavour.
          E.g. Logline has common configs dealing with a line of a webserver or
          simular log files (syslog, nginx...).
        - `Mumsys_Parser_Logline`: VERSION 2.0.0
          Fixes also type hints so some usage may not work anymore.
    - Each variant/adapter: `Logline`, `Default` can be still used for own behavior
      when adding format and patterns from your side on construction.
    - Adds and updates tests/ code coverage: 100%
    - Fixes SCA (Static Code Analyis) (phpstan level=8; src/! not for tests!)
    - Fixes CS



2023-07

+ Mumsys_GetOpts
    - VERSION 4.0.0
    - Re-implementation of the parser and sub handling (currently beta for
      version: 4.0.0 as introduction)
    - Fixes all todo tags like:
        - global and action params now handled
        - `--no-[arg]` in-validates positiv defaults in shell args
        - flags are now always bool true for 'is set', false for 'not set'
          `getResult()` will give answer.
        - actions do not require options anymore: `run.php action1 action2` works now
        - options in shell like `run.php --file=/location/to/file` now possible
        - detection of required values are improved! required: only if given.
        - Improved handling in options of descriptions for arguments. With
          but's, but improved. See tests for posibilities. Check `getHelp()` or
          `getHelpLong()`
        - Mixed args of the same name are possible now if different to different
          actions. E.g: `run.php action1 --help action2 --help`. Also with
          globals args: Fifo. Fist comes, first seves (there where set: that
          action or the global takes acount).
          `run.php --help action1 --help` will output global --help (if a global
          help was set in options)
    - Fixes issue in construction. argv are forced to be set only if input is
      `null` (default now)
    - Wont fix: options having e.g: `'-1'` ... `'-[-](int)'` keys or values.
      php parser always tries to convert them to `int`.
      Use aliases if you need things like this. E.g: --one, --two or '+' sign
      which is not converted to `int`
    - One config/ options list of key/value pairs per instance can be set.
        - Make private:
          `verfiyOptions() => _verfiyOptions()`
          `setMappingOptions() => _setMappingOptions()`
          Per instance only!
    - Updates, renew tests also for documentation for other issues
    - Fixes SCA (Static Code Analyis) level=6 src/! not for tests!
    - Fixes CS
    - Tests status: 100%
    - Todo: Simplify the code. still some mess.

+ Set dependency `phpstan` to fixed version: 0.12.83
    - Fixed some issues regarding previous version



2023-01

+ Mumsys_FileSystem_Default
    - Fixes usage in `unlink()` for symlinks
    - Updates tests



2022-03-03

PHP 8.1 (and 8.0 update) branch

    Important changes to know about:

    + Mumsys_Fileystem_Default
      mkdir():
      May have a different behavior depending on error_reporting() level
      Test are updated. Maybe an invalid usage on you side? Otherwise file a bug.

+ Updates phing config
+ Updates composer config
    - Enables php8.1
    - Updates dependencies
+ Fixes SCA (Static Code Analyis) for implementation and tests (level=4)
  Which told everthing to upgrade :-)
   - Fixes subparts of implementation/ stucture faults
   - Fixes type annotations
   - Fixes tests
   - Updates sca config
        - Init local config
        - disables some classes which are out of date, depricated or still in
          real development mode
+ Fixes CS
+ Fixes PHPdoc
+ Makes git submudules (misc) branch independent (submodule bindings counting
  from now on, other branches needs to be updated)



2021-07

    + Fixes validateRegex() e.g when nummeric values end in a TypeError



2021-03

    + Introduce Version 2.0.0-beta[1|2|3]
    + flobee/spss:^3 -> ^4 Updates Service/Spss + tests
    + Updates Mumsys_Unittest_Testcase[_Interface]
    + Updates tests to use Mumsys_Unittest_Testcase_Interface

      Find default methodes to be wrapped:

          grep -R "assert" tests/src | grep -v "asserting"

      Update test methods:
        ```
        cd tests
        find . -type f -iname \*.php | xargs sed -i 's,assertRegExp(,assertingRegExp(,g'
        find . -type f -iname \*.php | xargs sed -i 's,assertTrue(,assertingTrue(,g';
        find . -type f -iname \*.php | xargs sed -i 's,assertFalse(,assertingFalse(,g';
        find . -type f -iname \*.php | xargs sed -i 's,assertEquals(,assertingEquals(,g';
        find . -type f -iname \*.php | xargs sed -i 's,assertNotEquals(,assertingNotEquals(,g';
        find . -type f -iname \*.php | xargs sed -i 's,assertEmpty(,assertingEmpty(,g';
        find . -type f -iname \*.php | xargs sed -i 's,assertNull(,assertingNull(,g';
        find . -type f -iname \*.php | xargs sed -i 's,assertSame(,assertingSame(,g';
        find . -type f -iname \*.php | xargs sed -i 's,assertNotSame(,assertingNotSame(,g';
        find . -type f -iname \*.php | xargs sed -i 's,assertInstanceOf(,assertingInstanceOf(,g';
        find . -type f -iname \*.php | xargs sed -i 's,assertInstanceof(,assertingInstanceOf(,g';
        find . -type f -iname \*.php | xargs sed -i 's,assertRegExp(,assertingRegExp(,g';
        find . -type f -iname \*.php | xargs sed -i 's,assertRegExpPlural(,assertingRegExpPlural(,g';
        find . -type f -iname \*.php | xargs sed -i 's,expectException(,expectingException(,g';
        find . -type f -iname \*.php | xargs sed -i 's,expectExceptionMessage(,expectingExceptionMessage(,g';
        find . -type f -iname \*.php | xargs sed -i 's,expectingExceptionMessageRegExp(,expectingExceptionMessageRegex(,g';
        find . -type f -iname \*.php | xargs sed -i 's,expectExceptionMessageRegExp(,expectingExceptionMessageRegex(,g';
        ```
    + Sets phpunit 9.5.4


2021-02

    + Sets php 8, phpunit 9.5.2
    + Adds Mumsys_Unittest_Testcase_Interface to solve dependencies/ version
      changes on phpunit updates/ method changes


2020-11

    - Moves Assets domain to local branch


2019-11

    + Updates tests tools which come now from `misc` repository
    + Updates phpunit to 8.4

    + Mumsys_Cache
        + API changes in Mumsys_Cache_Interface Version 2.3.1
        + Improvments in Mumsys_Cache_[Interface|File]
        + Improves CS, phpdoc, SCA checks; Updates tests
        + Sets VERSION in Mumsys_Cache_Default
        + Sets strict_types to all classes
        + Removes @category, @package, @subpackage from class header; see file
          header

    + Mumsys_Abstract
        + Version 3.0.3
        + Fixes getVersions() unknown class version string to be valid with
          https://semver.org
        + Sets strict_types
        + Removes @category, @package, @subpackage from class header; seen file
          header

    + MumsysTestHelper
        + Sets strict_types
        + Removes @category, @package, @subpackage from class header; seen file
          header

    + Mumsys_Semver
        + Init Mumsys_Semver + Tests for semver.org version checks of mumsys objects

    + Mumsys_Variable*

        + API change in Mumsys_Variable_Manager_Default::registerItem() to handle
          inconsisteny for the manager when using createItem() before and have
          no 'name' attribute
        + Init Mumsys_Variable_Manager_Abstract

        + Adds typehints but breaks versions: New major/ minor versions:

            + Mumsys_Variable_Item_Default: 2.1.4 -> 3.2.4

              - Simplyfies getRegex() which take account only if validation will
                be used within the manager

            + Mumsys_Variable_Item_Abstract: 1.3.1 -> 2.3.2
              - Improves setErrorMessages()
              - Removes 2nd parameter of _initExternalCalls(); Not needed
              - Changes handling of callbacksGet( null| string )

            + Mumsys_Variable_Item_Interface -> 2.2.4
              - Adds missing methods from abstract class
              - Updates method signatures with default values for the getters

            + Mumsys_Variable_Manager_Default 2.3.5 -> 2.3.6
              - createItem() returns Mumsys_Variable_Item_Interface
              - getItem() returns null if not available
              - moves constants to Mumsys_Variable_Manager_Abstract

            + Mumsys_Variable_Manager_Factory 1.1.2 -> 1.1.3
              - Fixes typehints

            + Mumsys_Variable_Manager_Interface 1.1.2 -> 2.2.4
              - createItem() returns Mumsys_Variable_Item_Interface
              - getItem() returns null if not available


2019-10

    + API change in Mumsys_Php_Globals pls check if relevant for you (see below)!
    + Updates phpunit V 8.4.1, update test config beeing more strict
    + Improves tests/bootstrap
    + Updates phpstan config and sca runner (starting in a low level=1 )
    + Updates tests and implementation when VERSION was not set
    + Fixes typehints in phpdoc and updates inline doc
    + Improves CS
    + Updates travis setup

    + Mumsys_Mail_*
        + Adds strict_types to tests + implementation
        + Updates interface,
        + Updates tests
        + Improves phpdoc using phpstan up to level 5 now

    + Mumsys_Php_Globals
        + Merges from stable (1.0.8) to add missing methodes: Adds getSessionVar().
        + Improves implementation + tests
        + Adds strict_types to tests + implementation
        + API change: changes/toggles getServerServerVar() getServerVar() implementation:
          getServerVar() is for the _SERVER now
          getServerServerVar() for all possible server and enviroment variables
          This is better communication :-) for the usage.
        + Updates typehints

    + Mumsys_Session_*
        + Update usage of Mumsys_Php_Globals

    + Mumsys_Multirename
        + Improves check of logger usage on init.
        + Improves phpdoc using phpstan up to level 5 now
        + Fixes getVersionLong()

    + Mumsys_Cookie
        + Fixes phpdoc, updates test
        + Improves phpdoc using phpstan up to level 7 now


2019-09

    + Adds performance tests unsing [phpbench](github.com/phpbench) for the
      library and also for common or private intresting php performance tests

    + Mumsys_Variable_*
        + Enables 'strict_types'
        + Fixes valiable manager default to be more strict in 'allowEmpty' flag
        + Improves tests

    + Mumsys_Variable_Abstract
        + Adds 'unixtime' property

    + Mumsys_Variable_Item_Default
        + Improves __construct()

    + Mumsys_Variable_Manager_Default
        + Enables 'strict_types'
        + Adds TYPE_INVALID_UNIXTIME for unixtime property/ validation
        + Fixes CS
        + Fixes missing $data param to externalsApply() to callbacksApply( $data )


2018-02-02

    - Adds misc path as external for coding style, vc checks


2018-01-06/07

    - Merges open tasks to unstable branch
    - Updates composer bindings. You can use: #> ./composer for maintaining
      external code from now on.


2018-01-05

    Mumsys_GetOpts
    - VERSION 3.6.0
    - Adds action groups (or optional parameters handling as groups) like:
        eg: script.php --globalOption action1 --host ab action2 --host cd
        eg: script.php doThis -p 123 doThat now
        - Existing configurations will be used as is as global whitelist or
          needs to be extended to new action groups.
        - Return values will return like befor when not using the addition.
    - Improves phpdoc/ code
    - Updates Tests


2017-12

    Mumsys_Logger_*
    - Improves setup and tests


2016-07-14

    Mumsys_Variable_*
    Mumsys_Variable_Item_*
    Mumsys_Variable_Manager_*
    - Migrate from old Mumsys_Field classes
    - Adds tests


2016-05-27

    Mumsys_Cache
    - marked as depricated
    - Adds removeCache() methode
    - Updates tests
    - Improves coding style
    Mumsys_Cache_Default
    - Init class which uses Mumsys_Cache_File
    Mumsys_Cache_File
    - Init class
    - Implemention like Mumsys_Cache BUT now un/serialise will be used to also
      cache objects
    Mumsys_Cache_Interface
    - Init the interface for futher drivers e.g.: db cache


2016-05-17

    Mumsys_Session_Default
    Mumsys_Session_Nome
    Mumsys_Session_Abstract
    - Init new wersion of session handling where the old session class will be
      maked as deprecated soon

    Mumsys_Session_Interface
    - update methodes to be full featured now

    Mumsys_Session
    - Improves class to fit the extended interface


2016-05-16

    - Mumsys_I18n_None
      - Init default driver to implement without translation (just the wrapper)
      - Adds test, code coverage 100%
    - Mumsys_Html
      Mumsys_Html_Table
      Mumsys_Xml_Abstract
      - Init classes
      - adds tests, code coverage 100%


2016-05-15

    Mumsys_Request_*
    - Improves input handling, extend interface, updates classes to fit the
      interface
    - Improves coding style
    - updates tests: code coverage 100%

    Mumsys_Logger_*
    - adds method to replace loglevel during runtime
    - Updates tests to be 100% of code coverage


2016-04-24

    Mumsys_Multirename
    - Adding --stats flag to output some stats
    Mumsys_Mvc_Router*
    - Init Mumsys_Mvc_Router_Abstract
    - Init Mumsys_Mvc_Router_Default
    - Init Mumsys_Mvc_Router_Interface
    Mumsys_PriorityQueue_Simple
    - Init Mumsys_PriorityQueue_Simple + tests


2016-04-19

    Mumsys_Request
    - Init Mumsys_Request_Interface
    - Init Mumsys_Request_Abstract
    - Init Mumsys_Request_Default
    - Init Mumsys_Request_Console
    Mumsys_Session
    - Rename Mumsys_Session to Mumsys_Session_Default
    - Updates tests
    Mumsys_Timer
    - Improves timer, updates tests
    Mumsys_FileSystem
    - Doesnt flollow symlinks now
    Mumsys_Multirename
    - Updates in coding style
    Mumsys_PriorityQueue_Simple
    - Init class and tests


2016-03-27

    Mumsys_Multirename
    - Improves history handling
    - Sets logger property to be private
    - version 1.3.3
    - Update/ improves tests
    - codecoverage 100%
    - removes old code eg: _trackConfigDir()


2016-03-10

    Mumsys_Multirename
    - Improves handling of stored config files
        A config can contain selveral sub configs e.g: one config for this file
        extension and one config for that file extension and so on.
        there is no config management at the moment. add some config or delete
        the config file is currently supported. The thing at all: this config
        will be called from now on (next version) the default "preset" and it
        containts one of more configs.
    - Update/ improves tests
    - codecoverage 100%
    - VERSION 1.4.0


2016-03-01

    Mumsys_I18n (init from svn to git)
    - Init Mumsys_I18n_Abstract
    - Init Mumsys_I18n_Default
    - Updates Mumsys_I18n_Interface
    - Updates Mumsys_I18n_*Tests


2016-02-19

    Mumsys_Logger
    - Init Mumsys_Logger_Writer_Interface
    - Init Mumsys_Logger_Abstract
    - Init Mumsys_Logger_Default
    - Init Mumsys_Logger_File
    - Mark "Mumsys_Logger" to be deprecated


2016-02-14

    Mumsys_GetOpts
    - Bugfix to work with php7+
    - Improves tests with versions checks



2016-01-31

    Mumsys_Registry
    - Init class and tests for LGPL
    - Improves tests
    - codecoverage 100%


2016-01-30

    Mumsys_Session
    - Add session handling
    - Adds tests
    - codecoverage 100%
    Mumsys_Timer
    - Updates tests
    - extending by Mumsys_Abstract
    - codecoverage 100%
    Mumsys_Logger
    - Improves logger tests
    - codecoverage 100%
    - Adds todo: change visibility of class properties
    Mumsys_Lock
    - Init class and tests for LGPL
    - Improves tests
    - codecoverage 100%
    Mumsys_Counter
    - Init class and tests for LGPL
    - Improves tests
    - codecoverage 100%
    Mumsys_Cache
    - Init class and tests for LGPL
    - Improves tests
    - codecoverage 100%


2016-01-22

    Mumsys_Parser
    - version 1.1.1
    - Init class for LGPL
    - Init tests
    - Fixes some bugs
    - codecoveage 100%


2015-10-25

    Mumsys_Multirename
    - Adds --exclude option, Updates tests
    - version 1.3.1
    Mumsys_GetOpts
    - Improves getHelp() output, updates test


2015-09-14

    Mumsys_SVDRP
    - Init Mumsys_SVDRP class to deal with vdr's svdrpsend command
    Mumsys_Multirename
    - Init version 1.3.0
    Mumsys_Logger
    - Improves message output echo messages if data is an array
    Mumsys_Loader
    - Optimise code handling
    tests/createApi.sh
    - Updates usage because of update of phpdoc from version 1.4.4 to 2.8.*


2015-08-09

    Mumsys_Multirename
    - start implementation of addActionHistory, getActionHistory,
    - to be droped: setActionHistory
    - fixed some tests
    - fixed getRelevantFiles() of version 1.2.5
    - adds "history-size" limit to avoid memory problems when using huge listings
    - Improves statistics output after a rename/ test


2015-08-06

    Mumsys_Multirename
    - new Version: 1.2.5
    - Fixed incomplete bugfix version 1.2.4 for --find option in
      _getRelevantFiles()


2015-08-01

    Mumsys_Multirename
    - new Version: 1.2.4
    - Fixes a fault for --find option in _getRelevantFiles() when trying to
      find files with several keywords


2015-06-24

    Mumsys_Multirename
    - new Version: 1.2.3
    - Improves output messages in test mode
    - Adds --find option
    - Improves crap index of run() method
    - Adds method _substitution()
    - Improves/ Updates tests
    Mumsys_Timer
    - Adds Timer class tests


2015-05-24

    Mumsys_Multirename
    - new Version: 1.2.1 Improves set/unset parameters
    - Updates tests
    Mumsys_GetOpts
    - Adds handling to set/unset flags;
    - Updates tests


2015-05-09

    Mumsys_FileSystem
    - V 3.0.6; Bugfix in scanDirInfo() force skipping folder "." or ".."
    Mumsys_Multirename
    - new Version: 1.2.0-RC1
    - Implements scanning and renaming hidden (dot) files
    - Extends tests for new feature


2015-05-03

    Mumsys_GetOpts
    - Improves output for Mumsys_GetOpts::getHelp()
    Mumsys_Multirename
    - Imporves version output; adds version 1.1.0-RC1


2015-04-28/29

    Core:
    - Improves docs and License informations
    Mumsys_Multirename
    - Adds version number and handling version with getVersion(), showVersion()
      methodes. Now: 1.0.0-RC1, Using http://semver.org/
    - Adds tests for new methodes.


2015-04-27

    - Improves runtests.sh
    - Adds Mumsys_Csv and Mumsys_Lock to a new LICENSE, Improves tests
    - Init Php test class to tests/src/phpTest.php


2015-04-24

    - Adds /tests/tmp dir and change all tests to use that directory
    - Improves tests and code coverage
