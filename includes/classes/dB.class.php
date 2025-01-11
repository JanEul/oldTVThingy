<?php
class dB {
    public      $RowCount;

    protected   $Connection,
                $Config;

    function __construct() {
        try {
			//$this->Connection = new PDO("mysql:host=127.0.0.1;dbname=tv4;charset=utf8mb4", "root", '',
            $this->Connection = new PDO("mysql:host=127.0.0.1;dbname=oldtvemulator_main;charset=utf8mb4", "oldtvemulator_root", '',
                    [
                        PDO::ATTR_EMULATE_PREPARES      => false,
                        PDO::ATTR_PERSISTENT            => true,
                        PDO::MYSQL_ATTR_INIT_COMMAND    => "SET NAMES utf8mb4"
                    ]);
            $this->Connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $tz = (new DateTime('now', new DateTimeZone('Europe/Berlin')))->format('P');
            $this->Connection->exec("SET time_zone='$tz';");

            return true;
        } catch (PDOException $e) {
            header($_SERVER['SERVER_PROTOCOL'] . ' Database Unavailable', true, 503);
            die($e);
        }
    }
    
    // Executes a SQL query
    public function query(string $SQL, bool $Single = false, array $Execute = []) {
        $Query = $this->Connection->prepare($SQL);
        $Query->execute($Execute);

        $this->RowCount = $Query->rowCount();

        // If it's a SELECT statement it returns an array. Otherwise it returns a boolean of whether or not it was updated successfully
        if (inStr("SELECT", $SQL)) {
            if ($this->RowCount == 0)   return [];
            elseif ($Single)            return @$Query->fetch(PDO::FETCH_ASSOC);
            else                        return @$Query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            if ($this->RowCount > 0)    return true;
            else                        return false;
        }
    }
    
    // Inserts a value into the database
    public function insert(string $table, array $columns, bool $ignore = false) : bool {
        // Get columns
        $Columns        = "";
        $valuesPrepare  = "";
        $Values_Array   = [];
        foreach ($columns as $column => $value) {
            $Columns .= $column.",";
            if ($value !== "MSLF_NOW()" && $value !== "MSLF_NULL()") {
                $valuesPrepare              .= ":".$column.",";
                $Values_Array[":".$column]   = $value;
            } elseif ($value === "MSLF_NOW()") {
                $valuesPrepare              .= "NOW(),";
            } elseif ($value === "MSLF_NULL()") {
                $valuesPrepare              .= "NULL,";
            }
        }
        $Columns            = rtrim($Columns, ",");
        $valuesPrepare      = rtrim($valuesPrepare, ",");

		if (!$ignore) 	$ingoreSQL = "";
		else 		 	$ingoreSQL = "IGNORE";

        // Execute query
        if ($this->query("INSERT $ingoreSQL INTO $table ($Columns) VALUES ($valuesPrepare)", false, $Values_Array))  	return true;
        else                                                                                                        return false;
    }
    
    // Updates columns in the set table
    public function update(string $table, array $columns, array $where = []) : bool {
        // Get columns
        $valuesPrepare  = "";
        $Values_Array   = [];
        foreach ($columns as $column => $value) {
            if ($value !== null) {
                if ($value !== "MSLF_NOW()" && $value !== "MSLF_NULL()") {
                    $valuesPrepare              .= "$column = :".$column.",";
                    $Values_Array[":".$column]   = $value;
                } elseif ($value === "MSLF_NOW()") {
                    $valuesPrepare              .= "$column = NOW(),";
                } elseif ($value === "MSLF_NULL()") {
                    $valuesPrepare              .= "$column = NULL,";
                }
            }
        }
        $valuesPrepare      = rtrim($valuesPrepare, ",");
        
        // Generate where conditions
        $whereSQL = "";
        if ($where) {
            $whereSQL .= "WHERE";
            foreach ($where as $column => $condition) {
                $whereSQL                   .= " $column = :$column AND";
                $Values_Array[":".$column]   = $condition;
            }
            $whereSQL = rtrim($whereSQL, "AND");
        }
        
        // Execute query
        if ($this->query("UPDATE $table SET $valuesPrepare $whereSQL", false, $Values_Array))   return true;
        else                                                                                              return false;
    }
    
    // Creates a random string, checks if it's used in $column somewhere and if it is, generate another one and repeat.
    public function createUniqueString(int $length, string $column, string $table) : string {
    	$found = true;
    	while ($found) {
            if (!$this->exists($string = randomString($length), $column, $table)) $found = false;
    	}
    	return $string;
    }
    
    // Check if string exists or not
    public function exists(string $string, string $column, string $table) : bool {
        $query = $this->query("SELECT EXISTS(SELECT * FROM $table WHERE $column = :STRING) as a", true, [":STRING" => $string]);

        if ($query["a"])    return true;
        else                return false;
    }
    
    public function count(string $column, string $table, array $where) : int {
        // Generate where conditions
        $Values_Array   = [];
        $whereSQL       = "";
        if ($where) {
            $whereSQL .= "WHERE";
            foreach ($where as $column => $condition) {
                $whereSQL                   .= " $column = :$column AND";
                $Values_Array[":".$column]   = $condition;
            }
            $whereSQL = rtrim($whereSQL, "AND");
        }

        //echo "SELECT count($column) as a FROM $table $whereSQL";
        return $this->query("SELECT count($column) as a FROM $table $whereSQL", true, $Values_Array)["a"];
    }

    public function lastID() : int {
        return $this->Connection->lastInsertId();
    }
}