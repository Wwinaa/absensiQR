<?php
/**
 * Database Wrapper (PDO)
 * Handles connection and common queries using prepared statements
 */
class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;

    private $dbh; // Database Handler
    private $stmt;
    private $error;

    public function __construct() {
        $this->host = DB_HOST;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
        $this->dbname = DB_NAME;

        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_PERSISTENT => false, // Do NOT keep persistent connections on Windows/Apache
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // Fetch objects by default
            PDO::ATTR_TIMEOUT => 5 // 5 second timeout to prevent indefinite hangs
        ];

        // Create PDO instance
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // Don't die in production ideally, but for development it's critical to show error
            // so we don't get "bindValue() on null"
            die("Database Connection Failed: " . $this->error);
        }
    }

    /**
     * Prepare statement with query
     */
    public function query($sql) {
        if ($this->dbh) {
            $this->stmt = $this->dbh->prepare($sql);
            if (!$this->stmt) {
                die("Failed to prepare statement. SQL: " . htmlspecialchars($sql));
            }
        }
    }

    /**
     * Bind values to parameters
     */
    public function bind($param, $value, $type = null) {
        if (!$this->stmt) {
            die("Cannot bind parameter '$param': statement is null. Check if the query was successfully prepared.");
        }
        
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Execute the prepared statement
     */
    public function execute() {
        return $this->stmt->execute();
    }

    /**
     * Get multiple records as an array of objects
     */
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    /**
     * Get a single record as an object
     */
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }

    /**
     * Get row count
     */
    public function rowCount() {
        return $this->stmt->rowCount();
    }
    
    /**
     * Get last inserted ID
     */
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }
    
    /**
     * Check if connection is successful (Useful for testing)
     */
    public function isConnected() {
        return $this->dbh !== null;
    }
}
