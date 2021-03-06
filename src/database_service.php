<?php
class DatabaseService{
    private $database;
    
    function __construct(){
        $opts = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
        
        $this->database = new PDO(CONFIG_DB_DRIVER .
                                  ':host=' . CONFIG_DB_HOST .
                                  ';dbname=' . CONFIG_DB_NAME .
                                  ';charset=' . CONFIG_DB_CHARSET,
                                  CONFIG_DB_USERNAME,
                                  CONFIG_DB_PASSWORD,
                                  $opts);
    }

    function create($table, $values){
        $sql = "INSERT INTO " . $table . "(";
        
        foreach($values as $key => $value){
            $sql .= $key . ",";
        }
        $sql = rtrim($sql, ","); // Remove ending comma
        
        $sql .= ") VALUES (";
        foreach($values as $value){
            $sql .= "?,";
        }
        $sql = rtrim($sql, ","); // Remove ending comma
        
        $sql .= ")";
        $stmt = $this->database->prepare($sql);
        
        $i = 1;
        foreach($values as &$value){
            $stmt->bindParam($i, $value);
            $i++;
        }

        if($stmt->execute()){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    public function all($class, $table){
        $sql = 'SELECT * FROM ' . $table;
        $stmt = $this->database->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, $class);
        return $result;
    }

    public function find($class, $table, $key, $id){
        $sql = 'SELECT * FROM ' . $table .
             ' WHERE ' . $key . ' = \'' . $id . '\'';
        $stmt = $this->database->prepare($sql);
        if($stmt->execute()){
            $stmt->setFetchMode(PDO::FETCH_CLASS, $class); 
            $result = $stmt->fetch();
            return $result;
        }
        else{
            return NULL;
        }
    }

    public function query($class, $sql){
        $stmt = $this->database->prepare($sql);
        if($stmt->execute()){
            $stmt->setFetchMode(PDO::FETCH_CLASS, $class);
            $result = $stmt->fetch();
            return $result;
        }
        else{
            return NULL;
        }
    }

    function update($table, $values, $primkey, $id){
        $sql = "UPDATE " . $table . " SET ";
        
        foreach($values as $key => $value){
            $sql .= $key . " = ?,";
        }
        $sql = rtrim($sql, ","); // Remove ending comma
        
        
        
        $sql .= " WHERE " . $primkey . " = ?";
        $stmt = $this->database->prepare($sql);

        $i = 1;
        foreach($values as &$value){
            $stmt->bindParam($i, $value);
            $i++;
        }
        $stmt->bindParam($i, $id);

        if($stmt->execute()){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
}
?>