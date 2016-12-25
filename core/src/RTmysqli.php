<?php

/**
 * @link https://github.com/rogertiongdev/RTphp RTphp GitHub project
 * @license https://rogertiongdev.github.io/MIT-License/
 */

namespace RTdev\RTphp;

use mysqli;

/**
 * Simple MySQLi wrapper with prepared statements.
 *
 * @version 0.1
 * @author Roger Tiong RTdev
 */
class RTmysqli {

    /**
     * The SQL statement given in last executed query.
     *
     * @var string
     */
    public $sql = '';

    /**
     * The parameter given in last executed query.
     *
     * @var array
     */
    public $param = array();

    /**
     * The duration of last executed statement (millisecond).
     *
     * @var string
     */
    public $duration = '';

    /**
     * The total number of rows changed, deleted, or inserted by the last executed statement.
     *
     * @var integer
     */
    public $affected_rows = 0;

    /**
     * ID generated from the previous INSERT operation.
     *
     * @var integer
     */
    public $insert_id = 0;

    /**
     * The number of rows in statements result set.
     *
     * @var integer
     */
    public $num_rows = 0;

    /**
     * The number of parameter for the given statement.
     *
     * @var integer
     */
    public $param_count = 0;

    /**
     * mysqli object.
     *
     * @var mysqli
     */
    protected $conn;

    /**
     * Last query start time.
     *
     * @var string
     */
    protected $startTime;

    /**
     * Configuration data
     *
     * @var array
     */
    protected $dbConfig = array(
        'host' => '',
        'username' => '',
        'password' => '',
        'name' => ''
    );

    /**
     * A variable temporary store a message to share with others methods.
     *
     * @var string
     */
    private $msgBox = '';

    /**
     * Configure database connection.
     *
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $name Database name
     */
    public function config($host, $username, $password, $name) {

        $this->dbConfig = array_combine(array_keys($this->dbConfig), array(
            (string) $host,
            (string) $username,
            (string) $password,
            (string) $name
        ));
    }

    /**
     * Configure database connection by using INI file.
     *
     * @param string $filepath
     * @param array $identifiers Keys to define host, username, password and database name.
     * @return boolean
     */
    public function configIni($filepath, $identifiers) {

        $nfp = (string) $filepath;

        if (empty($nfp) || !file_exists($nfp) || $data = parse_ini_file($nfp, TRUE) == FALSE) {

            trigger_error(sprintf('%s Given ini file path not found.', __METHOD__));
            return FALSE;
        }

        if (!is_array($identifiers) && count($identifiers) != count($this->dbConfig)) {

            $msg = '%s Expects parameter 2 to be array and have exactly 4 elements. %s given.';
            trigger_error(sprintf($msg, __METHOD__, gettype($identifiers)));
            return FALSE;
        }

        $this->dbConfig = array_combine(array_keys($this->dbConfig), array_values($data));
        return TRUE;
    }

    /**
     * Configure one of the value for database connection.
     *
     * @param string $option Option: host, username, password, name.
     * @param string $value
     * @return boolean
     */
    public function configSingle($option, $value) {

        $op = (string) $option;

        if (!array_key_exists($op, $this->dbConfig)) {

            $msg = '%s Expects parameter 1 match one of following list (%s) %s given.';
            trigger_error(sprintf($msg, __METHOD__, implode(', ', array_keys($this->dbConfig)), $op));
            return FALSE;
        }

        $this->dbConfig[$op] = (string) $value;
    }

    /**
     * Format data given by using stripslashes and real_escape_string.
     *
     * @param array|string $data
     * @param boolean $advMode True = run strip_tags, urldecode, nl2br and html_entity_decode.
     * @return array|string
     */
    public function cleaner($data, $advMode = FALSE) {

        if (is_array($data)) {

            foreach ($data as $k => $v) {

                $data[$k] = $this->cleaner($v);
            }
        }
        elseif (!is_numeric($data)) {

            if (get_magic_quotes_gpc()) {

                $data = stripslashes($data);
            }

            if ($advMode) {

                $data = strip_tags(urldecode(nl2br(html_entity_decode($data, ENT_QUOTES, 'UTF-8'))));
            }

            $data = $this->connect()->real_escape_string($data);
        }

        return $data;
    }

    /**
     * Close database connection.
     */
    public function close() {

        if (isset($this->conn)) {

            if (is_object($this->conn)) {

                $this->conn->close();
            }

            unset($this->conn);
        }
    }

    /**
     * Establish database connection.
     *
     * @return boolean|mysqli
     */
    public function connect() {

        if (!isset($this->conn)) {

            $param = array();

            foreach ($this->dbConfig as $v) {

                $param[] = (string) $v;
            }

            $this->conn = new mysqli($param[0], $param[1], $param[2], $param[3]);
        }

        if ($this->conn->connect_errno) {

            trigger_error(sprintf('%s [%s]: [%s]', __METHOD__, $this->conn->connect_errno, $this->conn->connect_error));
            die('Unable connect to database.');
        }

        if (!$this->conn->ping()) {

            trigger_error(sprintf('%s %s', __METHOD__, $this->conn->connect_error));
        }

        return $this->conn;
    }

    /**
     * Performs a query on the database by using multiple Class Methods and PHP Functions.<br>
     * Important: The following Class Method will be auto run :-<br>
     * - $this->query_prepare()<br>
     * - $this->query_bind_param()<br>
     * - $this->query_execute()<br>
     * - $this->close()<br>
     *
     * INSERT multi $param format:<br>
     * - FALSE: array(string $types, mixed &$var1 [, mixed &$... ])<br>
     * - TRUE: array(string $types, array(mixed &$var1 [, mixed &$... ]) [, array(mixed &$var1 [, mixed &$... ]) &$...])<br>
     *
     * @param string $sql
     * @param array $param Parameter for bind param
     * @param boolean $insert_multi True = run multiple insert sql
     * @return mixed
     */
    public function query($sql, $param = array(), $insert_multi = FALSE) {

        $this->startTime = microtime(TRUE);
        $results = FALSE;
        $this->sql = $sql;
        $this->param = $param;

        if (!is_string($sql) || empty($sql) || !is_array($param)) {

            $msg = '%s Expects parameter 1 to be not empty string and parameter 2 to be array. %s, %s given.';
            return $this->queryFail(sprintf($msg, __METHOD__, gettype($sql), gettype($param)));
        }

        if (!$stmt = $this->queryPrepare($sql)) {

            return $this->queryFail(sprintf('%s Prepare fail: %s', __METHOD__, $this->msgBox));
        }

        $this->param_count = $stmt->param_count;
        $bindfail = '%s Bind param fail. The number of parameter doesn\"t match the placeholders in the statement. Placeholders count: %d, Parameter count: %d, Parameter type count: %d.';

        if ($stmt->param_count <= 0) {

            $results = $this->queryExecute($stmt, $sql);
        }
        else {

            $pinfo = $this->qparamSpliter($param);

            if ($pinfo['param_count'] <= 0) {

                $msg = sprintf($bindfail, __METHOD__, $stmt->param_count, (int) $pinfo['param_count'], $pinfo['format_count']);
                return $this->queryFail($msg);
            }

            if ($insert_multi && ($sqltype = $this->getSqlType($sql)) == 'insert') {

                if ($pinfo['format_count'] != ($row1count = count(current(current($pinfo['param_arr']))))) {

                    $msg = sprintf($bindfail, __METHOD__, $stmt->param_count, $row1count, $pinfo['format_count']);
                    return $this->queryFail($msg);
                }

                $results = array();
                $param_arr = current($pinfo['param_arr']);

                foreach ($param_arr as $v) {

                    if ($row1count != count($v)) {

                        $msg = '%s Expects all array from parameter 2\'s element 2 have the same count.';
                        return $this->queryFail(sprintf($msg, __METHOD__));
                    }

                    if (!($stmt = $this->queryBindParam($stmt, $pinfo['format_chars'], $pinfo['format_str'], $v))) {

                        break;
                    }

                    $results[] = $this->queryExecute($stmt, $sql);
                }
            }
            else {

                if ($pinfo['format_count'] != $pinfo['param_count']) {

                    $msg = sprintf($bindfail, __METHOD__, $stmt->param_count, (int) $pinfo['param_count'], $pinfo['format_count']);
                    return $this->queryFail($msg);
                }

                $stmt = $this->queryBindParam($stmt, $pinfo['format_chars'], $pinfo['format_str'], $pinfo['param_arr']);
                $results = ($stmt) ? $this->queryExecute($stmt, $sql) : FALSE;
            }
        }

        $this->durationLap();
        $stmt->close();
        $this->close();
        return $results;
    }

    /**
     * Run query SHOW_TABLES
     *
     * @return array
     */
    public function SHOW_TABLES() {

        $result = array();
        $rows = $this->query('SHOW TABLES');

        foreach ($rows as $row) {

            $result[] = $row[sprintf('Tables_in_%s', $this->dbConfig['name'])];
        }

        return $result;
    }

    /**
     * Run query SHOW_COLUMNS_FROM
     *
     * @param array|string $data Table(s) name
     * @return array
     */
    public function SHOW_COLUMNS_FROM($data) {

        if (is_array($data)) {

            foreach ($data as $k => $v) {

                $data[$k] = $this->SHOW_COLUMNS_FROM($v);
            }

            return $data;
        }
        elseif (preg_match('/^[a-zA-Z0-9_-]*$/', (string) $data)) {

            $result = array();
            $rows = $this->query(sprintf('SHOW COLUMNS FROM %s', trim($this->cleaner($data))));

            foreach ($rows as $row) {

                $result[] = $row['Field'];
            }

            return $result;
        }

        trigger_error(sprintf('%s Expects parameter to be string or array. %s given', __METHOD__, gettype($data)));
        return array();
    }

    /**
     * Lap duration.
     */
    protected function durationLap() {

        $this->duration = (microtime(TRUE) - $this->startTime);
    }

    /**
     * Get SQL type.
     *
     * @param string $sql
     * @return string
     */
    protected function getSqlType($sql) {

        return strtolower(current(explode(' ', trim((string) $sql))));
    }

    /**
     * Parameter spliter.<br>
     * Split a given parameter to:<br>
     *  - $info['format_str'] parameter datatype in string format.<br>
     *  - $info['format_chars'] parameter datatype in array format. (included custom datatype 't' and 'a')<br>
     *  - $info['format_count'] Number of datatype given.<br>
     *  - $info['param_arr'] parameter without datatype in array format.<br>
     *  - $info['param_count'] Number of value given.<br>
     * 
     * This function also will duplicate the value (Element: 'format_str') and replace 't' and 'a' to s.<br>
     *
     * @param array $param Query parameter
     * @return array
     */
    protected function qparamSpliter($param) {

        $nparam = array_values($param);
        $paramKeys = array_keys($nparam);
        $info = array();

        $info['format_str'] = current($nparam);
        $info['format_chars'] = str_split($info['format_str']);
        $info['format_count'] = count($info['format_chars']);
        $info['format_str'] = str_replace(array('t', 'a'), 's', $info['format_str']);
        $info['param_arr'] = array_slice($nparam, 1, end($paramKeys));
        $info['param_count'] = count($info['param_arr']);

        return $info;
    }

    /**
     * Methods to handle failed query.
     *
     * @param string $msg
     * @return NULL
     */
    protected function queryFail($msg) {

        trigger_error($msg);
        $this->durationLap();
        return NULL;
    }

    /**
     * Prepare an SQL statement for execution.
     *
     * @param string $sql
     * @param string $method Remark for error. Default: Class Method name.
     * @return boolean|mysqli_stmt
     */
    protected function queryPrepare($sql) {

        $conn = $this->connect();

        if (!$stmt = $conn->prepare($sql)) {

            $this->msgBox = $conn->error;
            return FALSE;
        }

        return $stmt;
    }

    /**
     * Binds a parameter to the specified variable name.<br>
     * This method will automatic format the value based on type given.<br>
     * - i : implement (int).<br>
     * - d : implement (float).<br>
     * - t : implement stripslashes.<br>
     * - a : implement $this->cleaner() with advance mode.<br>
     * 
     * Default will implement $this->cleaner().<br>
     *
     * @param mysqli_stmt $stmt
     * @param array $format_chars
     * @param string $format_str
     * @param array $param_arr
     * @return boolean|mysqli_stmt
     */
    protected function queryBindParam($stmt, $format_chars, $format_str, $param_arr) {

        foreach ($param_arr as $k => $v) {

            $type = $format_chars[$k];

            switch ($type) {

                case 'i':
                    $param_arr[$k] = (int) $v;
                    break;

                case 'd':
                    $param_arr[$k] = (float) $v;
                    break;

                case 't':
                    $param_arr[$k] = stripslashes($v);
                    break;

                case 'a':
                    $param_arr[$k] = $this->cleaner($v, TRUE);
                    break;

                default: $this->cleaner($v);
            }

            $param_arr[$k] = & $param_arr[$k];
        }

        if (call_user_func_array(array($stmt, 'bind_param'), array_merge(array($format_str), $param_arr))) {

            return $stmt;
        }

        trigger_error(sprintf('%s [%s]: [%s]', __METHOD__, $stmt->errno, $stmt->error));
        return FALSE;
    }

    /**
     * Executes a query after prepared SQL OR bind_param.<br>
     * Return format:<br>
     * - INSERT, UPDATE and DELETE [integer].<br>
     * - SELECT and SHOW [array] Format: key = start from 0, value = array('field_name' => 'data', field_name2' => 'data2', ...).<br>
     *
     * @param mysqli_stmt $stmt
     * @param string $sql
     * @return mixed
     */
    protected function queryExecute($stmt, $sql) {

        if (!$stmt->execute() || $stmt->errno > 0) {

            trigger_error(sprintf('%s [%s]: [%s]', __METHOD__, $stmt->errno, $stmt->error));
            return FALSE;
        }

        $sqlType = $this->getSqlType($sql);

        if ($sqlType == 'insert') {

            return $this->insert_id = $stmt->insert_id;
        }
        elseif (in_array($sqlType, array('update', 'delete'))) {

            return $this->affected_rows = $stmt->affected_rows;
        }
        elseif (in_array($sqlType, array('select', 'show'))) {

            $stmt->store_result();

            if (($this->num_rows = $stmt->num_rows) <= 0) {

                return array();
            }

            // Get all fields name from db based on sql statement
            $flds = $stmt->result_metadata()->fetch_fields();
            $data = array();

            foreach ($flds as $fld) {

                $dataKey = $fld->name;
                $result[$dataKey] = "";

                for ($i = 1; ++$i < 999;) {

                    if (!array_key_exists($dataKey, $data)) {

                        $data[$dataKey] = &$result[$dataKey];
                        break;
                    }

                    $dataKey = $fld->name . '_' . $i;
                }
            }

            if (!call_user_func_array(array($stmt, 'bind_result'), $data)) {

                trigger_error(sprintf('%s [%s]: [%s]', __METHOD__, $stmt->errno, $stmt->error));
                return FALSE;
            }

            $row = $return = array();

            while ($stmt->fetch()) {

                foreach ($data as $k => $v) {

                    $row[$k] = stripslashes($v);
                }

                $return[] = $row;
            }

            return $return;
        }

        return NULL;
    }

}
