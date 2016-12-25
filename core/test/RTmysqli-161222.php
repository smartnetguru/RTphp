<?php

/**
 * @link https://github.com/rogertiongdev/RTphp RTphp GitHub project
 * @license https://rogertiongdev.github.io/MIT-License/
 */
/**
 * Testing RTmysqli on PHP 5.3.29
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

require_once '../src/RTmysqli.php';

$db = new RTdev\RTphp\RTmysqli();

nlhr();
nl('Configure database ... ');
nlhr();

$db->config('localhost', 'rogertiong', '', 'rogertiongdev');

nlhr();
nl('INSERT single row.');
nlhr();

$sql = 'INSERT INTO test_tbl_001(fld_title, fld_body) VALUES(?,?)';
$param = array('st', 'Testing', '<p>I am just testing.</p>');
$inserted_id = $db->query($sql, $param);

nl(sprintf('SQL: %s', $db->sql));
nl(sprintf('Param: %s', json_encode($db->param)));
nl(sprintf('Last inserted id: %d', $inserted_id));
nl(sprintf('Duration microseconds: %s', $db->duration));

nlhr();
nl('SELECT single row.');
nlhr();

$result = $db->query('SELECT * FROM test_tbl_001 WHERE id = ?', array('i', $inserted_id));

nl(sprintf('SQL: %s', $db->sql));
nl(sprintf('Param: %s', json_encode($db->param)));
nl(sprintf('Selected data:  %s', json_encode($result)));
nl(sprintf('Duration microseconds: %s', $db->duration));

nlhr();
nl('INSERT multiple rows');
nlhr();

$data = array(
    array($inserted_id, 'Testing sub 1', '<p>I am just testing sub 1.</p>'),
    array($inserted_id, 'Testing sub 2', '<p>I am just testing sub 2.</p>'),
    array($inserted_id, 'Testing sub 3', '<p>I am just testing sub 3.</p>'),
    array($inserted_id, 'Testing sub 4', '<p>I am just testing sub 4.</p>'),
    array($inserted_id, 'Testing sub 5', '<p>I am just testing sub 5.</p>'),
    array($inserted_id, 'Testing sub 6', '<p>I am just testing sub 6.</p>'),
);

$inserted_ids = $db->query('INSERT INTO test_tbl_002(tbl_001_id, fld_title, fld_body) VALUES(?,?,?)', array('ist', $data), true);

nl(sprintf('SQL: %s', $db->sql));
nl(sprintf('Param: %s', json_encode($db->param)));
nl(sprintf('Last inserted ids: %s', json_encode($inserted_ids)));
nl(sprintf('Duration microseconds: %s', $db->duration));

nlhr();
nl('SELECT INNER JOIN');
nlhr();

$result2 = $db->query('SELECT main.*, sub.* FROM test_tbl_001 AS main INNER JOIN test_tbl_002 AS sub ON main.id = sub.tbl_001_id');

nl(sprintf('SQL: %s', $db->sql));
nl(sprintf('Selected data: %s', json_encode($result2)));
nl(sprintf('Duration microseconds: %s', $db->duration));

nlhr();
nl('UPDATE');
nlhr();

$affected_row = $db->query('UPDATE test_tbl_002 SET fld_body = ? WHERE id IN (?,?,?)', array('tiii', '<p>Body updated.</p>', 1, 3, 5));

nl(sprintf('SQL: %s', $db->sql));
nl(sprintf('Param: %s', json_encode($db->param)));
nl(sprintf('Number of affected row(s): %d', $affected_row));
nl(sprintf('Duration microseconds: %s', $db->duration));

nlhr();
nl('DELETE');
nlhr();

$affected_row2 = $db->query('DELETE FROM test_tbl_002 WHERE id IN (2,4)');

nl(sprintf('SQL: %s', $db->sql));
nl(sprintf('Number of affected row(s):  %d', $affected_row2));
nl(sprintf('Duration microseconds: %s', $db->duration));

nl(sprintf('SHOW Connected database table(s):  %s', json_encode($db->SHOW_TABLES())));
nl(sprintf('LAST SHOW SQL: %s', $db->sql));
nl(sprintf('Duration microseconds: %s', $db->duration));

nl(sprintf('SHOW fields in a database table: %s', json_encode($db->SHOW_COLUMNS_FROM('test_tbl_002'))));
nl(sprintf('LAST SHOW SQL: %s', $db->sql));
nl(sprintf('Duration microseconds: %s', $db->duration));

nl(sprintf('SHOW fields in a database table: %s', json_encode($db->SHOW_COLUMNS_FROM(array('test_tbl_001', 'test_tbl_002')))));
nl(sprintf('LAST SHOW SQL: %s', $db->sql));
nl(sprintf('Duration microseconds: %s', $db->duration));

$db->query('TRUNCATE test_tbl_001');
$db->query('TRUNCATE test_tbl_002');
