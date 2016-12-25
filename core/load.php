<?php

/**
 * @link https://github.com/rogertiongdev/RTphp RTphp GitHub project
 * @license https://rogertiongdev.github.io/MIT-License/
 */

namespace RTdev\RTphp;

/**
 * RTphp classes loader.<br>
 * All the class is independent.
 *
 * @version 0.1
 * @author Roger Tiong RTdev
 */

/**
 * Load all the classes
 */
function RTphpLoad() {

    $lib = array(
        '/src/RTmysqli.php',
        '/src/RTpassword.php',
        '/src/RTutil.php',
        '/src/RTslugify.php'
    );

    foreach ($lib as $class) {

        require_once dirname(__FILE__) . $class;
    }
}
