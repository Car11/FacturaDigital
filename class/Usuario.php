<?php
if (!isset($_SESSION))
    session_start();

if(isset($_POST["action"])){
    $usuario= new Usuario();
    switch($_POST["action"]){
        case "ReadAll":
            echo json_encode($usuario->ReadAll());
            break;
        case "Read":
            echo json_encode($usuario->Read());
            break;
        case "Create":
            $usuario->Create();
            break;
        case "Update":
            $usuario->Update();
            break;
        case "Delete":
            $usuario->Delete();
            break;   
        case "Login":
            $usuario->username= $_POST["username"];
            $usuario->password= $_POST["password"];
            echo json_encode($usuario->Login());
            break;   
        case "CheckSession":
            $_SESSION['url']= $_POST["url"];
            echo json_encode($usuario->CheckSession());
            break;   
    }
}

class Usuario{
  
    public $id;
    public $username;
    public $password;
    public $rol;
    public $nombre;
    public $email;
    public $is_active;
    //private $sessiondata = array(); // devuelve el estado del login.

    function __construct(){
        require_once("Conexion.php");
        //require_once("Log.php");
        //require_once('Globals.php');
        //
        // identificador único
        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            $this->id= $obj["id"] ?? null;
            $this->id= $obj["username"] ?? '';
            $this->nombre= $obj["nombre"] ?? '';
            
        }
    }

    function CheckSession(){
        if(isset($_SESSION["username"])){    
            // ****** VALIDA SI TIENE CREDENCIALES PARA LA URL CONSULTADA ******
            // if(CheckRol($_SESSION["url"]))...
            $sessiondata['id']= $_SESSION["id"];
            $sessiondata['username']= $_SESSION["username"];
            $sessiondata['rol']= $_SESSION["rol"];
            $sessiondata['nombre']= $_SESSION["nombre"];
            $sessiondata['url']= $_SESSION["url"];
            $sessiondata['status']= 'login';
            return $sessiondata;
        }
        else {            
            $sessiondata['url']= $_SESSION["url"];
            $sessiondata['status']='invalido';
            return $sessiondata;
        }
    }

    function EndSession(){
        unset($_SESSION['id']);
        unset($_SESSION['username']);
        unset($_SESSION['rol']);
        unset($_SESSION['nombre']);        
        unset($_SESSION["url"]);
        $sessiondata['status']='invalido';
        return $sessiondata;
    }

    function Login(){
        try {            
            $sql='SELECT id, nombre /*,rol*/ FROM usuario where password=:password  AND username=:username';
            $param= array(':username'=>$this->username, ':password'=>$this->password);   
            $data= DATA::Ejecutar($sql, $param);
            if($data){
                // Session
                $_SESSION["id"]= '011';
                $_SESSION["username"]= $this->username;                
                $_SESSION["rol"]= 'rol-prueba';
                $_SESSION["nombre"]= 'nombre prueba';
                //          
                //if(isset($_SESSION['url']))
                $sessiondata['url']=  isset($_SESSION['url'])? $_SESSION['url'] : 'Dashboard.html';    
                $sessiondata['status']='login'; 
                return $sessiondata;
            }
            else {
                $sessiondata['url']=  isset($_SESSION['url'])? $_SESSION['url'] : 'Dashboard.html';
                $sessiondata['status']='invalido'; 
                return $sessiondata;
            }
        }     
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        } 
    }
}


?>