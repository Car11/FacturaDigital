<?php
if (!isset($_SESSION))
    session_start();

if(isset($_POST["action"])){
    $producto= new Producto();
    switch($_POST["action"]){
        case "LoadAll":
            echo json_encode($producto->LoadAll());
            break;
        case "Load":
            echo json_encode($producto->Load());
            break;
        case "Insert":
            $producto->Insert();
            break;
        case "Update":
            $producto->Update();
            break;
        case "Delete":
            echo json_encode($producto->Delete());
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

    function __construct(){
        require_once("Conexion.php");
        //require_once("Log.php");
        //require_once('Globals.php');
        //
        if(isset($_POST["producto"])){
            $obj= json_decode($_POST["producto"],true);
            $this->id= $obj["id"] ?? null;
            $this->nombre= $obj["nombre"] ?? '';
            $this->cantidad= $obj["cantidad"] ?? 0;            
            $this->scancode= $obj["scancode"] ?? '';
            $this->precio= $obj["precio"] ?? 0;
            $this->codigorapido= $obj["codigorapido"] ?? 0;
            $this->idcategoria= $obj["idcategoria"] ?? null;
            $this->fecha= $obj["fecha"] ?? null;
        }
    }

    function LoadAll(){
        try {
            $sql='SELECT id, nombre, cantidad, scancode, precio , codigorapido
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

    function Load(){
        try {
            $sql='SELECT id, nombre, cantidad, scancode, precio , codigorapido, idcategoria, fecha
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

    function Insert(){
        try {
            $sql="INSERT INTO producto   (id,nombre, cantidad, scancode, precio ,codigorapido, idcategoria, fecha)
                VALUES (uuid(),:nombre, :cantidad, :scancode, :precio ,:codigorapido, :idcategoria, :fecha)";              
            //
            $param= array(':nombre'=>$this->nombre,':cantidad'=>$this->cantidad,':scancode'=>$this->scancode, ':precio'=>$this->precio, ':codigorapido'=>$this->codigorapido, ':idcategoria'=>$this->idcategoria, ':fecha'=>$this->fecha );
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
                SET nombre=:nombre, cantidad=:cantidad, scancode=:scancode, precio=:precio, codigorapido=:codigorapido, idcategoria=:idcategoria, fecha=:fecha
                WHERE id=:id";
            $param= array(':id'=>$this->id, ':nombre'=>$this->nombre,':cantidad'=>$this->cantidad,':scancode'=>$this->scancode, ':precio'=>$this->precio , ':codigorapido'=>$this->codigorapido, ':idcategoria'=>$this->idcategoria, ':fecha'=>$this->fecha );
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
            $sql="SELECT id
                FROM /*  definir relacion */ R
                WHERE R./*definir campo relacion*/= :id";                
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