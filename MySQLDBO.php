<?php

namespace ECT\Common;

/**
 * Class MySQLDBO
 * 
 * STL PDO interface for accessing MySQL a little easier than putting PDO calls everywhere in your code.
 */
class MySQLDBO {

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var MySQLDBO
     */
    private static $instance;

    /**
     * @var null|resource callable function
     */
    private $customLoggingFunction = null;

    /**
     * @return MySQLDBO
     */
    public static function Get()
    {
        if( ! self::$instance instanceof MySQLDBO )
        {
            self::$instance = new MySQLDBO();
        }
        return self::$instance;
    }

    /**
     * MySQL Database interface.  Simplifies using the STL PDO functionality for MySQL.
     * Exposes easy to implement public interface with the database to allow for easily
     * calling and pulling records.
     * 
     * MySQLDBO constructor.
     * @throws \Exception
     */
    public function __construct($host, $database, $username, $password, $port = 3306)
    {
        try {
            $dsn = "mysql:host=". $host .";dbname=". $database .";port=". $port;
            $this->pdo = new \PDO($dsn, $username, $password);
            $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->pdo->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL);
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch(\PDOException $e)
        {
            throw new \Exception("MySQLDBO Error Occurred");
        }
    }

    /**
     * Call's the custom error function if it is defined
     * 
     * @param string $errorMessage
     */
    private function logError($errorMessage){
        if( $this->customLoggingFunction !== null){
            \call_user_func($this->customLoggingFunction, $errorMessage);
        }
    }

    /**
     * Prepares a statement and then exectues the statement with the required parameters.  This
     * function is not publicly exposed and is at the heart of all public manipulation functions.
     * This function handles everything PDO needs to properly execute a safe (sanitized with 
     * prepare) query and inject the parameters for you.
     * 
     * @param string $sql
     * @param array $params
     * @return \PDOStatement
     * @throws \Exception
     */
    private function PrepareAndExecute($sql, $params)
    {
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($params);
            return $statement;
        } catch(\PDOException $e){
            $this->logError($e->getMessage());
            throw new \Exception("Server Error Occurred");
        }
    }

    /**
     * This function is designed to handle quick inserts, deletes or updates that a response
     * isn't wanted for.  E.g updating a counter, deleting a temp record.
     * 
     * @param $sql
     * @return bool
     * @throws \Exception
     */
    public function ExecuteAndClose($sql)
    {
        $statement = $this->PrepareAndExecute($sql, array());
        $statement->closeCursor();
        return true;
    }

    /**
     * Select N number of records from the database.
     * 
     * @param $sql
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function Select($sql, $params)
    {
        $results = array();
        $statement = $this->PrepareAndExecute($sql, $params);
        while($row = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            $results[] = $row;
        }
        $statement->closeCursor();
        return $results;
    }

    /**
     * Insert a record into the database and return the ID of the record.
     * NOTE: The ID is returned as a string due to the PDO lastInsertId method
     * returning a string, cast the return as you see fit.
     * 
     * @param $sql
     * @param $params
     * @return string
     * @throws \Exception
     */
    public function Insert($sql, $params)
    {
        $statement = $this->PrepareAndExecute($sql, $params);
        $id = $this->pdo->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

    /**
     * Update a database table.  Returns # of manipulated records as an integer.
     * 
     * @param $sql
     * @param $params
     * @return int
     * @throws \Exception
     */
    public function Update($sql, $params)
    {
        $statement = $this->PrepareAndExecute($sql, $params);
        $rowCount = $statement->rowCount();
        $statement->closeCursor();
        return $rowCount;
    }

    /**
     * Select and return a single row as an associative array.  This should be used when
     * you're only looking to retrieve a single row, e.g SELECT * FROM users WHERE Username = "unique_name"
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function SelectSingleRow($sql, $params)
    {
        $statement = $this->PrepareAndExecute($sql, $params);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $row;
    }

    /**
     * Select and return a single field.  This should be used when you're only looking
     * to retrieve a single record from a query, e.g SELECT UserId FROM users WHERE Username = "unique_name"
     * 
     * @param $sql
     * @param $params
     * @return string|null
     * @throws ServerErrorException
     */
    public function SelectSingleField($sql, $params)
    {
        $statement = $this->PrepareAndExecute($sql, $params);
        $row = $statement->fetch(\PDO::FETCH_NUM);
        $statement->closeCursor();
        //in case we get an empty result back
        if( ! isset($row[0])){
            return null;
        }
        return $row[0];
    }

    /**
     * Put the database into a transaction state
     */
    public function StartTransaction()
    {
        if( ! $this->pdo->inTransaction())
        {
            $this->pdo->beginTransaction();
        }
    }

    /**
     * Commit any transation calls made
     * Note: This will take the database out of the transaction state
     */
    public function CommitTransaction()
    {
        if( $this->pdo->inTransaction())
        {
            $this->pdo->commit();
        }
    }

    /**
     * Rollback any transaction calls made
     */
    public function RollbackTransaction()
    {
        if( $this->pdo->inTransaction())
        {
            $this->pdo->rollBack();
        }
    }

}