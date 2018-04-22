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
            $sessiondata['id']= $_SESSION["id"];
            $sessiondata['username']= $_SESSION["username"];
            $sessiondata['rol']= $_SESSION["rol"];
            $sessiondata['nombre']= $_SESSION["nombre"];
            $sessiondata['status']= 'login';
            return $sessiondata;
        }
        else {            
            $sessiondata['status']='invalido';
            return $sessiondata;
        }
    }

    function EndSession(){
        unset($_SESSION['id']);
        unset($_SESSION['username']);
        unset($_SESSION['rol']);
        unset($_SESSION['nombre']);        
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
                $sessiondata['status']='login'; 
                return $sessiondata;
            }
            else {
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