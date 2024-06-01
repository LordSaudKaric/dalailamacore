<?php
namespace Dalailama\Database;

use PDO;
use PDOException;
use Dalailama\File\File;
use Dalailama\Exception\MissingInputException;
use Dalailama\Exception\DatabaseConnectionException;

class Database
{
    /**
     * @var $instance;
     */
    protected static $instance;
    /**
     * @var $connection
     */
    protected static $connection;
    /**
     * @var string $table
     */
    private static string $table = '';
    /**
     * @var string $select
     */
    private static string $select = '';
    /**
     * @var string $join
     */
    private static string $join = '';
    /**
     * @var string $where
     */
    private static string $where = '';
    /**
     * @var string $group_by
     */
    private static string $group_by = '';
    /**
     * @var string $having
     */
    private static string $having = '';
    /**
     * @var string $order_by
     */
    private static string $order_by = '';
    /**
     * @var string $offset
     */
    private static string $offset = '';
    /**
     * @var string $limit
     */
    private static string $limit = '';
    /**
     * @var mixed $query
     */
    private static mixed $query = '';
    /**
     * @var array $binding
     */
    private static array $binding = [];
    /**
     * @var array $where_binding
     */
    private static array $where_binding = [];
    /**
     * @var array $having_binding
     */
    private static array $having_binding = [];
    private static string $setter = '';

    /**
     * Database Constructor
     */
    private function __construct($table){
        static::$table = $table;
    }

    private static function connect()
    {
        if (! static::$connection) {
            $config = File::require('config/database.php');
            extract($config);
            $dsn = "mysql:dbname={$db_name};host={$db_host}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => " SET NAMES {$charset} COLLATE {$collation}"
            ];
            try {
                static::$connection = new PDO($dsn, $db_user, $db_pass, $options);
            } catch (PDOException $e) {
                throw new DatabaseConnectionException(
                    sprintf('Error occurred connection to database: %s',
                        $e->getMessage())
                );
            }
        }
    }

    private static function instance()
    {
        static::connect();
        $table = static::$table;
        if (! static::$instance) {
            static::$instance = new Database($table);
        }
        return static::$instance;
    }

    public static function query($query = null)
    {
        static::instance();

        if ($query == null) {

            if (! static::$table) {
                throw new MissingInputException('Table name is required');
            }

            $query = 'SELECT ';
            $query .= static::$select ?: '*';
            $query .= ' FROM ' . static::$table;
            $query .= static::$join;
            $query .= static::$where;
            $query .= static::$group_by;
            $query .= static::$having;
            $query .= static::$order_by;
            $query .= static::$limit;
            $query .= static::$offset;
        }

        static::$query = $query;
        static::$binding = array_merge(static::$where_binding, static::$having_binding);

        return static::instance();
    }

    public static function select()
    {
        static::$select = implode(', ', func_get_args());
        return static::instance();
    }

    public static function table($table)
    {
        static::$table = $table;
        return static::instance();
    }

    public static function join($table, $first, $second, $operator = '=', $type = 'INNER')
    {
        static::$join .= ' ' . $type . ' JOIN ' . $table . ' ON ' . $first . ' ' . $operator . ' ' . $second;
        return static::instance();
    }

    public static function right_join($table, $first, $second, $operator = '=')
    {
        static::join($table, $first, $second, $operator, 'RIGHT');
        return static::instance();
    }

    public static function left_join($table, $first, $second, $operator = '=')
    {
        static::join($table, $first, $second, $operator, 'LEFT');
        return static::instance();
    }

    public static function where($column, $value, $operator = '=', $type = null)
    {
        $where = '`' . $column . '` ' . $operator . ' ? ';
        if (! static::$where) {
            $stmt = ' WHERE ' . $where;
        } else {
            if ($type == null) {
                $stmt = ' AND ' . $where;
            } else {
                $stmt = ' ' . strtoupper($type) . ' ' . $where;
            }
        }

        static::$where .= $stmt;
        static::$where_binding[] = htmlspecialchars($value ?? '');

        return static::instance();
    }

    public static function or_where($column, $value, $operator = '=')
    {
        static::where($column, $value, $operator, 'OR');
        return static::instance();
    }

    public static function group_by()
    {
        static::$group_by = ' GROUP BY ' . implode(', ', func_get_args());
        return static::instance();
    }

    public static function having($column, $value, $operator = '=')
    {
        $having = '`' . $column . '` ' . $operator . ' ? ';
        if (! static::$having) {
            $stmt = ' HAVING ' . $having;
        } else {
            $stmt = ' AND ' . $having;
        }

        static::$having .= $stmt;
        static::$having_binding[] = htmlspecialchars($value);

        return static::instance();
    }

    public static function order_by($column, $type = null)
    {
        $sep = static::$order_by ? ', ' : ' ORDER BY ';
        $type = ($type != null && in_array(strtoupper($type), ['ASC', 'DESC'])) ? strtoupper($type) : 'ASC';

        static::$order_by .= $sep . $column . ' ' . $type;
        return static::instance();
    }

    public static function limit($limit)
    {
        static::$limit = ' LIMIT ' . $limit;
        return static::instance();
    }

    public static function offset($offset)
    {
        static::$offset = ' OFFSET ' . $offset;
        return static::instance();
    }

    private static function fetchExecute()
    {
        static::query(static::$query);
        $query = trim(static::$query, ' ');

        //die($query);

        $data = static::$connection->prepare($query);
        $data->execute(static::$binding);

        static::clear();

        return $data;
    }

    public static function getAll()
    {
        return static::fetchExecute()->fetchAll();
    }

    public static function get()
    {
        return static::fetchExecute()->fetch();
    }

    public static function execute($data, $query, $where = null)
    {
        static::instance();
        if (! static::$table) {
            throw new MissingInputException('Table name is required');
        }

        foreach ($data as $key => $value) {
            static::$setter .= '`' . $key . '` = ?, ';
            static::$binding[] = filter_var($value, FILTER_DEFAULT);
        }

        static::$setter = trim(static::$setter, ', ');
        $query .= static::$setter;
        $query .= $where != null ? static::$where . ' ' : '';

        static::$binding = $where != null ? array_merge(static::$binding, static::$where_binding) : static::$binding;

        $data = static::$connection->prepare($query);
        $data->execute(static::$binding);

        static::clear();
    }

    public static function insert($data)
    {
        $query = 'INSERT INTO ' . static::$table . ' SET ';

        static::execute($data, $query);

        return static::$connection->lastInsertId();
    }

    public static function update($data)
    {
        $query = 'UPDATE ' . static::$table . ' SET ';

        static::execute($data, $query, true);

        return true;
    }

    public static function delete()
    {
        $query = 'DELETE FROM ' . static::$table;

        static::execute([], $query, true);

        return true;
    }

    private static function clear()
    {
        static::$select     = '';
        static::$table      = '';
        static::$join       = '';
        static::$where      = '';
        static::$having     = '';
        static::$order_by   = '';
        static::$limit      = '';
        static::$offset     = '';
        static::$query      = '';
        static::$instance   = '';
        static::$setter     = '';
        static::$binding        = [];
        static::$where_binding  = [];
        static::$having_binding = [];
    }
}