<?php
if (!isset($_SESSION))
    session_start();
if(isset($_POST["action"])){
    $producto= new Producto();
    switch($_POST["action"]){
        case "ReadAll":
            echo json_encode($producto->ReadAll());
            break;
        case "Read":
            echo json_encode($producto->Read());
            break;
        case "Create":
            $producto->Create();
            break;
        case "Update":
            $producto->Update();
            break;
        case "Delete":
            $producto->Delete();
            break;   
        case "CheckRelatedItems":  
            echo json_encode($producto->CheckRelatedItems());
            break;  
        case "ReadByCode":  
            echo json_encode($producto->ReadByCode());
            break;
    }
}
class Producto{
    public $id=null;
    public $nombre='';
    public $cantidad=0;
    public $scancode='';
    public $precio=0;
    public $codigorapido='';
    public $idcategoria='';
    public $fechaExpiracion=null;
    public $descripcion='';
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
            $this->nombre= $obj["nombre"] ?? '';
            $this->descripcion= $obj["descripcion"] ?? '';
            $this->cantidad= $obj["cantidad"] ?? 0;            
            $this->scancode= $obj["scancode"] ?? '';
            $this->precio= $obj["precio"] ?? 0;
            $this->codigorapido= $obj["codigorapido"] ?? 0;
            $this->idcategoria= $obj["idcategoria"] ?? null;
            $this->fechaExpiracion= $obj["fechaExpiracion"] ?? null;
        }
    }
    function ReadAll(){
        try {
            $sql='SELECT id, nombre, cantidad, scancode, cantidad, precio , codigorapido
                FROM     producto       
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
            $sql='SELECT id, nombre, cantidad, scancode, precio , codigorapido, idcategoria, fechaExpiracion, descripcion
                FROM producto  
                where id=:id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql,$param);
            return $data;
        }     
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar el producto'))
            );
        }
    }
    function ReadByCode(){
        try{     
            $sql="SELECT id, codigorapido, descripcion, precio, cantidad, id FROM producto WHERE codigorapido =:codigorapido OR scancode =:scancode";
            $param= array(':codigorapido'=>$this->codigorapido,':scancode'=>$this->scancode);
            $data= DATA::Ejecutar($sql,$param);
            
            if(count($data))
                return $data;
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
    function Create(){
        try {
            $sql="INSERT INTO producto   (id,nombre, cantidad, scancode, precio ,codigorapido, idcategoria, fechaExpiracion, descripcion)
                VALUES (uuid(),:nombre, :cantidad, :scancode, :precio ,:codigorapido, :idcategoria, :fechaExpiracion, :descripcion)";              
            //
            $param= array(':nombre'=>$this->nombre,':cantidad'=>$this->cantidad,':scancode'=>$this->scancode, ':precio'=>$this->precio, ':codigorapido'=>$this->codigorapido, ':idcategoria'=>$this->idcategoria, ':fechaExpiracion'=>$this->fechaExpiracion, ':descripcion'=>$this->descripcion );
            $data = DATA::Ejecutar($sql,$param,false);
            if($data)
            {
                return true;
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
            $sql="UPDATE producto 
                SET nombre=:nombre, cantidad=:cantidad, scancode=:scancode, precio=:precio, codigorapido=:codigorapido, idcategoria=:idcategoria, fechaExpiracion=:fechaExpiracion, descripcion= :descripcion
                WHERE id=:id";
            $param= array(':id'=>$this->id, ':nombre'=>$this->nombre,':cantidad'=>$this->cantidad,':scancode'=>$this->scancode, ':precio'=>$this->precio , ':codigorapido'=>$this->codigorapido, ':idcategoria'=>$this->idcategoria, ':fechaExpiracion'=>$this->fechaExpiracion, ':descripcion'=>$this->descripcion );
            $data = DATA::Ejecutar($sql,$param,false);
            if($data)
                return true;
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
    function CheckRelatedItems(){
        try{
            // if($this->CheckRelatedItems()){
            //     //$sessiondata array que devuelve si hay relaciones del objeto con otras tablas.
            //     $sessiondata['status']=1; 
            //     $sessiondata['msg']='Registro en uso'; 
            //     return $sessiondata;           
            // }              
            if(count($data))
                return $data;
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
            // if($this->CheckRelatedItems()){
            //     //$sessiondata array que devuelve si hay relaciones del objeto con otras tablas.
            //     $sessiondata['status']=1; 
            //     $sessiondata['msg']='Registro en uso'; 
            //     return $sessiondata;           
            // }                    
            $sql='DELETE FROM producto  
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