<?php
require_once("Conexion.php");
require_once("CategoriasxProducto.php");
//require_once("Log.php");
//require_once('Globals.php');
//
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
    }
}

class Producto{
    public $id=null;
    public $nombre='';
    public $nombreAbreviado='';
    public $descripcion='';    
    public $cantidad=0;
    public $precio=0;
    public $scancode='';
    public $codigoRapido='';
    public $fechaExpiracion=null;
    public $categoria= array();
    

    function __construct(){
        // identificador único
        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            $this->id= $obj["id"] ?? null;
            $this->nombre= $obj["nombre"] ?? '';
            $this->nombreAbreviado= $obj["nombreAbreviado"] ?? '';
            $this->descripcion= $obj["descripcion"] ?? '';
            $this->cantidad= $obj["cantidad"] ?? 0;            
            $this->precio= $obj["precio"] ?? 0;
            $this->scancode= $obj["scancode"] ?? '';            
            $this->codigoRapido= $obj["codigoRapido"] ?? 0;            
            $this->fechaExpiracion= $obj["fechaExpiracion"] ?? null;
            //Categorias del producto.
            if(isset($obj["categoria"] )){
                foreach ($obj["categoria"] as $idcat) {
                    $catprod= new CategoriasXProducto();
                    $catprod->id= $idcat;
                    array_push ($this->categoria, $catprod);
                }
            }
        }
    }

    function ReadAll(){
        try {
            $sql='SELECT id, nombre, cantidad, scancode, cantidad, precio , codigoRapido
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
            $sql='SELECT id, nombre, nombreAbreviado, descripcion, cantidad, precio, scancode, codigoRapido, fechaExpiracion
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
    d41d8cd9 8f00 b204 e980 0998ecf8427e
    function Create(){
        try {
            $sql="INSERT INTO producto   (id, nombre, nombreAbreviado, descripcion, cantidad, precio, scancode, codigoRapido, fechaExpiracion)
                VALUES (uuid(),:nombre, :nombreAbreviado, :descripcion, :cantidad, :precio, :scancode, :codigoRapido, :fechaExpiracion);
                
                ";
            //
            $param= array(':nombre'=>$this->nombre, ':nombreAbreviado'=>$this->nombreAbreviado, ':descripcion'=>$this->descripcion, ':cantidad'=>$this->cantidad, ':precio'=>$this->precio,
                ':scancode'=>$this->scancode, ':codigoRapido'=>$this->codigoRapido, ':fechaExpiracion'=>$this->fechaExpiracion);
            $data = DATA::Ejecutar($sql,$param);
            if($data)
            {
                //get id.
                //save array obj
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
                SET nombre=:nombre, nombreAbreviado=:nombreAbreviado, descripcion= :descripcion, cantidad=:cantidad, precio=:precio, scancode=:scancode, codigoRapido=:codigoRapido, fechaExpiracion=:fechaExpiracion
                WHERE id=:id";
            $param= array(':id'=>$this->id, ':nombre'=>$this->nombre, ':nombreAbreviado'=>$this->nombreAbreviado, ':descripcion'=>$this->descripcion, ':cantidad'=>$this->cantidad, ':precio'=>$this->precio , 
                ':scancode'=>$this->scancode, ':codigoRapido'=>$this->codigoRapido, ':fechaExpiracion'=>$this->fechaExpiracion);
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

    private function CheckRelatedItems(){
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