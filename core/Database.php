<?php
class Database
{
    private static $instance = null;
    private $connection;
    private static $connectionPool = [];
    private static $maxConnections = 10;
    private $statement;
    private $errorController;
    private $debugMode = false;

    private function __construct()
    {
        $this->errorController = new ErrorController();

        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            $this->handleError('connection_error', $e);
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function isConnected()
    {
        try {
            $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function reconnect()
    {
        $this->connection = null;
        self::$instance = null;
        return self::getInstance();
    }

    public static function getConnection($name = 'default')
    {
        if (!isset(self::$connectionPool[$name])) {
            if (count(self::$connectionPool) >= self::$maxConnections) {
                throw new RuntimeException('Maximum connection limit reached');
            }
            self::$connectionPool[$name] = new self();
        }
        return self::$connectionPool[$name];
    }

    public function closeConnection()
    {
        $this->connection = null;
        foreach (self::$connectionPool as $key => $connection) {
            if ($connection === $this) {
                unset(self::$connectionPool[$key]);
                break;
            }
        }
    }

    public function query($sql, $params = [])
    {
        try {
            $this->statement = $this->connection->prepare($sql);
            $this->statement->execute($params);
            return $this;
        } catch (PDOException $e) {
            $this->handleError('query_error', $e, ['sql' => $sql, 'params' => $params]);
        }
    }

    public function fetchAll()
    {
        try {
            return $this->statement->fetchAll();
        } catch (PDOException $e) {
            $this->handleError('fetch_error', $e);
        }
    }

    public function fetch()
    {
        try {
            return $this->statement->fetch();
        } catch (PDOException $e) {
            $this->handleError('fetch_error', $e);
        }
    }

    public function rowCount()
    {
        try {
            return $this->statement->rowCount();
        } catch (PDOException $e) {
            $this->handleError('count_error', $e);
        }
    }

    public function lastInsertId()
    {
        try {
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->handleError('last_id_error', $e);
        }
    }

    public function beginTransaction()
    {
        try {
            return $this->connection->beginTransaction();
        } catch (PDOException $e) {
            $this->handleError('transaction_error', $e);
        }
    }

    public function commit()
    {
        try {
            return $this->connection->commit();
        } catch (PDOException $e) {
            $this->handleError('transaction_error', $e);
        }
    }

    public function rollback()
    {
        try {
            return $this->connection->rollBack();
        } catch (PDOException $e) {
            $this->handleError('transaction_error', $e);
        }
    }

    public function inTransaction()
    {
        return $this->connection->inTransaction();
    }

    private function handleError($type, $exception, $context = [])
    {
        $errorData = [
            'type' => 'database',
            'code' => $exception->getCode(),
            'message' => $this->getErrorMessage($type, $exception),
            'details' => [
                'error_type' => $type,
                'sql_state' => $exception->errorInfo[0] ?? null,
                'driver_code' => $exception->errorInfo[1] ?? null,
                'driver_message' => $exception->errorInfo[2] ?? null,
                'context' => $context
            ]
        ];

        // ເພີ່ມຂໍ້ມູນການ debug ຖ້າຢູ່ໃນ development mode
        if (ENVIRONMENT === 'development' || $this->debugMode) {
            $errorData['details']['trace'] = $exception->getTraceAsString();
            $errorData['details']['file'] = $exception->getFile();
            $errorData['details']['line'] = $exception->getLine();

            // Log error
            $this->logError($errorData);
        }

        // ຖ້າຢູ່ໃນ transaction, ໃຫ້ rollback
        if ($this->inTransaction()) {
            $this->rollback();
        }

        $this->errorController->handle('500', $errorData);
        exit;
    }

    public function enableDebug()
    {
        $this->debugMode = true;
    }

    public function disableDebug()
    {
        $this->debugMode = false;
    }

    private function logError($errorData)
    {
        $log = date('Y-m-d H:i:s') . " - Database Error\n";
        $log .= json_encode($errorData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        error_log($log, 3, LOG_PATH . '/database_error.log');
    }

    private function getErrorMessage($type, $exception): string
    {
        switch ($type) {
            case 'connection_error':
                return 'ບໍ່ສາມາດເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນໄດ້';
            case 'query_error':
                return 'ເກີດຂໍ້ຜິດພາດໃນການ Query ຖານຂໍ້ມູນ';
            case 'fetch_error':
                return 'ເກີດຂໍ້ຜິດພາດໃນການດຶງຂໍ້ມູນ';
            case 'count_error':
                return 'ເກີດຂໍ້ຜິດພາດໃນການນັບຈຳນວນແຖວ';
            case 'last_id_error':
                return 'ເກີດຂໍ້ຜິດພາດໃນການດຶງ ID ຫຼ້າສຸດ';
            default:
                return 'ເກີດຂໍ້ຜິດພາດທີ່ບໍ່ຮູ້ຈັກໃນຖານຂໍ້ມູນ';
        }
    }
}
