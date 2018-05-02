<?php
require_once("Conexion.php");
//require_once("Log.php");
//require_once('Globals.php');

if (!isset($_SESSION))
    session_start();

if(isset($_POST["action"])){
    $categoria= new Categoria();
    switch($_POST["action"]){
        case "ReadAll":
            echo json_encode($categoria->ReadAll());
            break;
        case "Read":
            echo json_encode($categoria->Read());
            break;
        case "Create":
            $categoria->Create();
            break;
        case "Update":
            $categoria->Update();
            break;
        case "Delete":
            echo json_encode($categoria->Delete());
            break;   
    }
}

class Categoria{
    public $id=null;
    public $nombre='';
    public $descripcion;

    function __construct(){
        // identificador único
        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        if(isset($_POST["categoria"])){
            $obj= json_decode($_POST["categoria"],true);
            $this->id= $obj["id"] ?? null;
            $this->nombre= $obj["nombre"] ?? '';
            $this->descripcion= $obj["descripcion"] ?? '';
        }
    }

    function ReadAll(){
        try {
            $sql='SELECT id, nombre, descripcion
                FROM     categoria       
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
            $sql='SELECT id, nombre, descripcion
                FROM categoria  
                where id=:id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql,$param);
            return $data;
        }     
        catch(Exception $e) {            
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar el categoria'))
            );
        }
    }

    function Create(){
        try {
            $sql="INSERT INTO categoria   (id, nombre)
                VALUES (uuid(), :nombre)";              
            //
            $param= array(':nombre'=>$this->nombre);
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
            $sql="UPDATE categoria 
                SET nombre=:nombre 
                WHERE id=:id";
            $param= array(':id'=>$this->id, ':nombre'=>$this->nombre );
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
            $sql='DELETE FROM categoria  
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