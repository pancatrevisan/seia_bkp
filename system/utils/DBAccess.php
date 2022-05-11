<?php
    class DBAccess{
        
        private  $DB_HOST = "localhost";
        private $DB_USER = "root";
        private $DB_PASS = "";
        private $DB   = "seiadatabase";
        
        public $con;

        public function __construct()
        {
            $this->con = mysqli_connect($this->DB_HOST, $this->DB_USER, $this->DB_PASS) or die("Sem conexão com o servidor");
            if (mysqli_connect_errno()) {
                printf("Connect failed: %s\n", mysqli_connect_error());
                exit();
            }
            $select = mysqli_select_db($this->con, $this->DB) or die("Sem acesso ao DB, Entre em contato com o Administrador, ");
            
        }

        public function __destruct()
        {
            mysqli_close($this->con);
        }
        
        public function query($query){
            return mysqli_query($this->con, $query);
        }
    }
    
    
?>