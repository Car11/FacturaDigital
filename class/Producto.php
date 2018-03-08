<?php
if (!isset($_SESSION))
    session_start();
require_once('Globals.php');
require_once("Conexion.php");
require_once("Log.php");

//Globals::ConfiguracionIni();

if(isset($_POST["action"])){
    $producto= new Producto();
    switch($_POST["action"]){       
        case "LoadAll":
            echo json_encode($producto->LoadAll());
            break;
        case "Load":
            $producto->id=$_POST["id"];
            echo json_encode($producto->Load());
            break;
        case "Insert":
            $producto->Nombre= $_POST["Nombre"];
            // $producto->Cantidad= $_POST["Cantidad"];
            // $producto->Scancode= $_POST["Scancode"];
            // $producto->Precio= $_POST["Precio"];
            // $producto->CodigoRapido= $_POST["CodigoRapido"];
            $producto->Insert();
            break;
        case "Update":
            $producto->id= $_POST["id"];
            $producto->Nombre= $_POST["Nombre"];
            // $producto->Cantidad= $_POST["Cantidad"];            
            // $producto->Scancode= $_POST["Scancode"];
            // $producto->Precio= $_POST["Precio"];
            // $producto->CodigoRapido= $_POST["CodigoRapido"];
            $producto->Update();
            break;
        case "Delete":
            $producto->id= $_POST["id"];            
            $producto->Delete();
            break;   
    }
}

class Producto{
    public $id='';
    public $Nombre='';
    public $Cantidad='';
    public $Scancode='';
    public $Precio='';
    public $CodigoRapido='';

    function __construct(){
        require_once('Globals.php');
        require_once("Conexion.php");
        require_once("Log.php");
    }

    function LoadAll(){
        try {
            $sql='SELECT id, Nombre, Cantidad, Scancode, Precio , CodigoRapido 
                FROM     producto       
                ORDER BY Nombre asc';
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
            $sql='SELECT id, Nombre, Cantidad, Scancode, Precio , CodigoRapido 
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
            $sql="INSERT INTO producto   (id,Nombre, Cantidad, Scancode, Precio ,CodigoRapido)
                VALUES (uuid(),:Nombre, :Cantidad, :Scancode, :Precio ,:CodigoRapido)";              
            //
            $param= array(':Nombre'=>$this->Nombre,':Cantidad'=>$this->Cantidad,':Scancode'=>$this->Scancode, ':Precio'=>$this->Precio, ':CodigoRapido'=>$this->CodigoRapido);
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
                SET Nombre=:Nombre, Cantidad=:Cantidad, Scancode=:Scancode, Precio=:Precio CodigoRapido=:CodigoRapido
                WHERE id=:id";
            $param= array(':id'=>$this->id, ':Nombre'=>$this->Nombre,':Cantidad'=>$this->Cantidad,':Scancode'=>$this->Scancode, 'Precio'=>$this->Precio , 'CodigoRapido'=>$this->CodigoRapido );
            $data = DATA::Ejecutar($sql,$param,true);
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
            //     echo "Registro en uso";
            //     return false;
            // }                
            $sql='DELETE FROM producto  
            WHERE id= :id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql, $param, true);
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

}



?>