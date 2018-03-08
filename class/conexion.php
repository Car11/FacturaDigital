<?php 

class DATA {
    
	public static $conn;
    private static $connSql;
    private static $config="";

	private static function ConfiguracionIni(){
        require_once('Globals.php');
        if (file_exists('../../ini/config.ini')) {
            self::$config = parse_ini_file('../../ini/config.ini',true); 
        } 
        else if (file_exists('../ini/config.ini')) {
            self::$config = parse_ini_file('../ini/config.ini',true); 
        }    
        else if (file_exists('../../../ini/config.ini')) {
            self::$config = parse_ini_file('../../../ini/config.ini',true); 
        }         
    }  

    private static function Conectar(){
        try {          
            self::ConfiguracionIni();
            if(!isset(self::$conn)) {                                
                self::$conn = new PDO('mysql:host='. self::$config[Globals::app]['host'] .';dbname=' . self::$config[Globals::app]['dbname'].';charset=utf8', self::$config[Globals::app]['username'],   self::$config[Globals::app]['password']); 
                return self::$conn;
            }
        } catch (PDOException $e) {
            require_once("Log.php");  
            log::AddD('FATAL', 'Ha ocurrido al Conectar con la base de datos MySQL[01]', $e->getMessage());
            $_SESSION['errmsg']= 'Problemas de Conexión';
            header('Location: ../Error.php');
            exit;
        }
    }
    
    public static function ConectarSQL(){
        try {           
            if(!isset(self::$connSql)) {
                $config = parse_ini_file('../ini/config.ini'); 
                self::$connSql = new PDO("odbc:sqlserver", 'dbaadmin', 'dbaadmin'); 
                return self::$connSql;
            }
        } catch (PDOException $e) {
            require_once("Log.php");  
            log::AddD('FATAL', 'Ha ocurrido al Conectar con la base de datos SQL[01]', $e->getMessage());
            //$_SESSION['errmsg']= $e->getMessage();
            header('Location: ../Error.php');
            exit;
        }
    }    

    // Ejecuta consulta SQL, $op = true envía los datos en 'crudo', $op=false envía los datos en arreglo (fetch).
    public static function Ejecutar($sql, $param=NULL, $op=false) {
        try{
            //conecta a BD
            self::Conectar();
            $st=self::$conn->prepare($sql);
            self::$conn->beginTransaction(); 
            if($st->execute($param))
            {
                self::$conn->commit(); 
                if(!$op)
                    return  $st->fetchAll();
                else return $st;    
            } else {
                self::$conn->rollback(); 
                require_once("Log.php");  
                log::AddD('ERROR', 'Ha ocurrido al Ejecutar la sentencia SQL[02]', 'code: ' . $st->errorInfo()[1] . ' msg: ' . $st->errorInfo()[2] );
                return false;
            }
            
        } catch (Exception $e) {
            self::$conn->rollback(); 
            require_once("Log.php");  
            log::AddD('ERROR', 'Ha ocurrido al Ejecutar la sentencia SQL', $e->getMessage());
            //$_SESSION['errmsg']= $e->getMessage();
            header('Location: ../Error.php');
            exit;
        }
    }
    
    public static function EjecutarSQL($sql, $param=NULL, $op=false) {
        try{
            //conecta a BD
            self::ConectarSQL();    
            $st=self::$connSql->prepare($sql);
            self::$conn->beginTransaction(); 
            if($st->execute($param)){
                self::$conn->commit(); 
                if(!$op)
                    return  $st->fetchAll();
                else return $st;    
            } else {
                self::$conn->rollback(); 
                require_once("Log.php");  
                log::Add('ERROR', 'Ha ocurrido al Ejecutar la sentencia SQL[02]');
                return false;
            }
        } catch (Exception $e) {
            self::$conn->rollback(); 
            require_once("Log.php");  
            log::AddD('ERROR', 'Ha ocurrido al Ejecutar la sentencia SQL', $e->getMessage());
            //$_SESSION['errmsg']= $e->getMessage();
            header('Location: ../Error.php');
            exit;
        }
    }
    
	private static function Close(){
		mysqli_close(self::$conn);			
	}

    public static function getLastID(){
        return self::$conn->lastInsertId();
    }
}
?>
