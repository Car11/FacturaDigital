<?php
require_once("Conexion.php");
//require_once("Log.php");
//require_once('Globals.php');
// Eventos del usuario.
require_once('Evento.php');

if (!isset($_SESSION))
    session_start();

if(isset($_POST["action"])){
    $opt= $_POST["action"];
    unset($_POST['action']);
    $usuario= new Usuario();
    switch($opt){
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
            $usuario->Login();
            echo json_encode($_SESSION['usersession']);
            break;   
        case "CheckSession":            
            $usuario->CheckSession();
            echo json_encode($_SESSION['usersession']);
            break;
        case "EndSession":
            $usuario->EndSession();
            break;        
    }
}

abstract class userSessionStatus
{
    const invalido = 'invalido'; // login invalido
    const login = 'login'; // login ok; credencial ok
    const nocredencial= 'nocredencial'; // login ok; sin credenciales
    const inactivo= 'inactivo';
    const noexiste= 'noexiste';
}

class Usuario{
    public $id;
    public $username;
    public $password;
    public $nombre;
    public $email;
    public $activo = 0;
    public $status = userSessionStatus::invalido;
    public $eventos= array(); // array de eventos del usuario.
    public $url;    

    function __construct(){
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
        if(isset($_SESSION["usersession"]->id)){
            // VALIDA SI TIENE CREDENCIALES PARA LA URL CONSULTADA
            $_SESSION['usersession']->status= userSessionStatus::nocredencial;
            $_SESSION['usersession']->url = $_POST["url"];
            $urlarr = explode('/', $_SESSION['usersession']->url);
            $myUrl = end($urlarr);
            foreach ($_SESSION['usersession']->eventos as $evento) {
                if(strtolower($myUrl) == strtolower($evento->url)){
                    $_SESSION['usersession']->status= userSessionStatus::login;
                    break;
                }
            }
        }
        else {
            $this->status= userSessionStatus::invalido;
            $this->url = $_POST["url"];
            $_SESSION["usersession"]= $this;
        }
    }

    function EndSession(){
        unset($_SESSION['usersession']);
        //return true;
    }

    function CreateHash(){
        return password_hash($this->password, PASSWORD_DEFAULT);
    }

    function Login(){
        try { 
            // demo; borrar...........<<<<
            //$this->password= $this->CreateHash();
            //..................>>>>
            //Check activo & password.
            $sql= 'SELECT u.id, u.username, u.nombre, activo, password, idevento, e.nombre as nombreUrl, e.url
            FROM usuario u inner join rolesxusuario ru on ru.idusuario = u.id
                inner join eventosxrol er on er.idrol = ru.idrol
                inner join evento e on e.id = er.idevento
                where username=:username';
            $param= array(':username'=>$this->username);
            $data= DATA::Ejecutar($sql, $param);
            if($data){
                if($data[0]['activo']==0){
                    unset($_SESSION["usersession"]);
                    $this->status= userSessionStatus::inactivo;
                }
                else {
                    // usuario activo; check password
                    if(password_verify($this->password, $data[0]['password'])){
                        foreach ($data as $key => $value){
                            // Session
                            $evento= new Evento(); // evento con credencial del usuario.
                            if($key==0){
                                $this->id = $value['id'];
                                $this->username = $value['username'];
                                $this->nombre = $value['nombre'];
                                $this->activo = $value['activo'];
                                $this->status = userSessionStatus::login;
                                $this->url = isset($_SESSION['usersession']->url)? $_SESSION['usersession']->url : 'Dashboard.html'; // Url consultada
                                //
                                $evento->id= $value['idevento'];
                                $evento->nombre= $value['nombreUrl'];
                                $evento->url= $value['url'];
                                $this->eventos = array($evento);
                            }
                            else {
                                $evento->id= $value['idevento'];
                                $evento->nombre= $value['nombreUrl'];
                                $evento->url= $value['url'];
                                array_push ($this->eventos, $evento);
                            }                    
                        }
                    }
                    else { // password invalido
                        unset($_SESSION["usersession"]);
                        $this->status= userSessionStatus::invalido;
                    }
                }
            }
            else {
                unset($_SESSION["usersession"]);
                $this->status= userSessionStatus::noexiste;
            }
            // set user session.
            $_SESSION["usersession"]= $this;
        }     
        catch(Exception $e) {
            unset($_SESSION["usersession"]);
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        } 
    }

    function setUnsafeCookie($name, $cookieData, $key)
    {
        
        $iv = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
        return setcookie(
            $name, 
            base64_encode(
                $iv.
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_128,
                    $key,
                    json_encode($cookieData),
                    MCRYPT_MODE_CBC,
                    $iv
                )
            )
        );
    }

}




?>