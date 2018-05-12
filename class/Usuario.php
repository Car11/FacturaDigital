<?php
if(isset($_POST["action"])){
    $opt= $_POST["action"];
    unset($_POST['action']);
    // Classes
    require_once("Conexion.php");
    require_once('Evento.php');
    // Session
    if (!isset($_SESSION))
        session_start();
    // Instance
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
        case "CheckUsername":
            $usuario->username= $_POST["username"];
            echo json_encode($usuario->CheckUsername());
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
    public $status = userSessionStatus::invalido; // estado de la sesion de usuario.
    public $listarol= array(); // array de roles del usuario.
    public $eventos= array(); // array de eventos asignados a la sesion de usuario.
    public $url;    

    function __construct(){      
        // identificador único
        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            require_once("UUID.php");
            $this->id= $obj["id"] ?? UUID::v4();
            $this->nombre= $obj["nombre"] ?? '';  
            $this->username= $obj["username"] ?? '';
            $this->password= $obj["password"] ?? '';  
            $this->email= $obj["email"] ?? '';  
            $this->activo= $obj["activo"] ?? '';  
            //roles del usuario.
            if(isset($obj["listarol"] )){
                require_once("RolesXUsuario.php");
                //
                foreach ($obj["listarol"] as $idrol) {
                    $rolUsr= new RolesXUsuario();
                    $rolUsr->idrol= $idrol;
                    $rolUsr->idusuario= $this->id;
                    array_push ($this->listarol, $rolUsr);
                }
            }
        }
    }

    // login and user session

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

    // usuario CRUD

    function ReadAll(){
        try {
            $sql='SELECT id, nombre, username, email, activo
                FROM     usuario       
                ORDER BY nombre asc';
            $data= DATA::Ejecutar($sql);
            return $data;
        }     
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }

    function Read(){
        try {
            $sql='SELECT u.id, u.nombre, u.username, u.password, email, activo, r.id as idrol, r.nombre as nombrerol
                FROM usuario  u LEFT JOIN rolesxusuario ru on ru.idusuario = u.id
                    LEFT JOIN rol r on r.id = ru.idrol
                where u.id=:id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql,$param);     
            foreach ($data as $key => $value){
                require_once("Rol.php");
                $rol= new Rol(); // crol del producto
                if($key==0){
                    $this->id = $value['id'];
                    $this->nombre = $value['nombre'];
                    $this->username = $value['username'];
                    $this->password = $value['password'];
                    $this->email = $value['email'];
                    $this->activo = $value['activo'];
                    //rol
                    if($value['idrol']!=null){
                        $rol->id = $value['idrol'];
                        $rol->nombre = $value['nombrerol'];
                        array_push ($this->listarol, $rol);
                    }
                }
                else {
                    $rol->id = $value['idrol'];
                    $rol->nombre = $value['nombrerol'];
                    array_push ($this->listarol, $rol);
                }
            }
            return $this;
        }     
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar el usuario'))
            );
        }
    }

    function Create(){
        try {
            $sql="INSERT INTO usuario   (id, nombre, username, password, email, activo)
                VALUES (:uuid, :nombre, :username, :password, :email, :activo)";
            //
            $param= array(':uuid'=>$this->id, ':nombre'=>$this->nombre, ':username'=>$this->username, ':password'=> password_hash($this->password, PASSWORD_DEFAULT), 
                ':email'=>$this->email, ':activo'=>$this->activo);
            $data = DATA::Ejecutar($sql,$param, false);
            if($data)
            {
                //save array obj
                if(RolesXUsuario::Create($this->listarol))
                    return true;
                else throw new Exception('Error al guardar los roles.', 03);
            }
            else throw new Exception('Error al guardar.', 02);
        }     
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }

    function Update(){
        try {
            if($this->password=='NOCHANGED'){
                $sql="UPDATE usuario 
                    SET nombre=:nombre, username=:username, email=:email, activo=:activo 
                    WHERE id=:id";
                $param= array(':id'=>$this->id, ':nombre'=>$this->nombre, ':username'=>$this->username, ':email'=>$this->email, ':activo'=>$this->activo);
            }
            else {
                $sql="UPDATE usuario 
                    SET nombre=:nombre, username=:username, password= :password, email=:email, activo=:activo 
                    WHERE id=:id";
                $param= array(':id'=>$this->id, ':nombre'=>$this->nombre, ':username'=>$this->username, ':password'=> password_hash($this->password, PASSWORD_DEFAULT), ':email'=>$this->email, ':activo'=>$this->activo);
            }
            $data = DATA::Ejecutar($sql,$param,false);
            if($data){
                //update array obj
                if($this->listarol!=null)
                    if(RolesXUsuario::Update($this->listarol))
                        return true;            
                    else throw new Exception('Error al guardar los roles.', 03);
                else {
                    // no tiene roles
                    if(RolesXUsuario::Delete($this->id))
                        return true;
                    else throw new Exception('Error al guardar los roles.', 04);
                }
            }
            else throw new Exception('Error al guardar.', 123);
        }     
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }   

    function CheckUsername(){
        try{
            $sql="SELECT id
                FROM usuario 
                WHERE username= :username";
            $param= array(':username'=>$this->username);
            $data= DATA::Ejecutar($sql, $param);
            if(count($data))
                $sessiondata['status']=1; // usuario duplicado
            else $sessiondata['status']=0; // usuario unico
            return $sessiondata;
        }
        catch(Exception $e){
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }

    private function CheckRelatedItems(){
        try{
            $sql="SELECT xx
                FROM xx R
                WHERE R.xx= :id";
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql, $param);
            if(count($data))
                return true;
            else return false;
        }
        catch(Exception $e){
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }

    function Delete(){
        try {
            if($this->CheckRelatedItems()){
                //$sessiondata array que devuelve si hay relaciones del objeto con otras tablas.
                $sessiondata['status']=1; 
                $sessiondata['msg']='Registro en uso'; 
                return $sessiondata;           
            }                    
            $sql='DELETE FROM usuario  
                WHERE id= :id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql, $param, false);
            if($data)
                return $sessiondata['status']=0; 
            else throw new Exception('Error al eliminar.', 978);
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