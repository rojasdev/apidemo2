<?php
class Users{
 
    // database connection and table name
    private $conn;
    private $table_name = "users";
 
    // object properties
    public $id;
    public $username;
    public $password;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // create user
    function create() {
        // Query to check if the username already exists
        $check_query = "SELECT id FROM " . $this->table_name . " WHERE username = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(1, $this->username);
        $check_stmt->execute();
        
        // If the username already exists, return false
        if ($check_stmt->rowCount() > 0) {
            return false; // Username already exists
        }
        
        // Query to insert record
        $query = "INSERT INTO
                    " . $this->table_name . "
                SET
                    username = :username, password = :password";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        
        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);
        
        // Execute query
        if ($stmt->execute()) {
            return true; // Record inserted successfully
        }
        
        return false; // Error in execution
    }

    // validate
    function validate() {
        // Query to select user with matching username and password
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = ? AND password = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        
        // Bind parameters
        $stmt->bindParam(1, $this->username);
        $stmt->bindParam(2, $this->password);
        
        // Execute query
        if ($stmt->execute()) {
            // Check if exactly one row is returned
            if ($stmt->rowCount() == 1) {
                return true; // Valid credentials
            }
        }
        
        return false; // Invalid credentials or error
    }

    // read products
    function read(){
 
        // select all query
        $query = "SELECT * FROM
        " . $this->table_name . "  ORDER BY
                    id DESC";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }
}
?>