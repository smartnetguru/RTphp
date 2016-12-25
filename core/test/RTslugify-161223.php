<?php

/**
 * @link https://github.com/rogertiongdev/RTphp RTphp GitHub project
 * @license https://rogertiongdev.github.io/MIT-License/
 */
/**
 * Testing RTslugify on PHP 5.3.29
 *
 * @version 0.1
 * @author Roger Tiong RTdev
 */

/**
 * Print values in new line
 *
 * @param type $v
 */
function nl($v) {

    print sprintf('%s<br><br>', $v);
}

/**
 * Print hr
 */
function nlhr() {

    nl('---------------------------------------------------------------------');
}

require_once '../src/RTslugify.php';

$obj = new RTdev\RTphp\RTslugify();

nlhr();
nl('<h1>Original</h1>');
nlhr();

nl($list = $obj->viewList());

nlhr();
nl('After (only apply arabic and default)');
nlhr();

$obj->config(false, array('arabic', 'default'));
nl($obj->slugify($list));

nlhr();
nl('After (apply all)');
nlhr();

$obj->config(true);
nl($obj->slugify($list));
nlhr();
