<?php
require_once("Conexion.php");
//require_once("Log.php");
//require_once('Globals.php');
//
if (!isset($_SESSION))
    session_start();

if(isset($_POST["action"])){
    $opt= $_POST["action"];
    unset($_POST['action']);
    //
    $producto= new Producto();
    switch($opt){
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
            echo json_encode($producto->Delete());
            break;   
    }    
}

class Producto{
    public $id=null;
    public $nombre='';
    public $descripcion='';
    public $listaevento= array();
    

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
            $this->descripcion= $obj["descripcion"] ?? '';
            //Categorias del producto.
            if(isset($obj["listaevento"] )){
                require_once("CategoriasxProducto.php");
                //
                foreach ($obj["listaevento"] as $idcat) {
                    $catprod= new CategoriasXProducto();
                    $catprod->idcategoria= $idcat;
                    $catprod->idproducto= $this->id;
                    array_push ($this->listaevento, $catprod);
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
            $sql='SELECT p.id, p.nombre, p.nombreAbreviado, p.descripcion, cantidad, precio, scancode, codigoRapido, fechaExpiracion, c.id as idcategoria,c.nombre as nombrecategoria
                FROM producto  p LEFT JOIN categoriasxproducto cp on cp.idproducto = p.id
                    LEFT join categoria c on c.id = cp.idcategoria
                where p.id=:id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql,$param);     
            foreach ($data as $key => $value){
                require_once("Categoria.php");
                $cat= new categoria(); // categorias del producto
                if($key==0){
                    $this->id = $value['id'];
                    $this->nombre = $value['nombre'];
                    $this->nombreAbreviado = $value['nombreAbreviado'];
                    $this->descripcion = $value['descripcion'];
                    $this->cantidad = $value['cantidad'];
                    $this->precio = $value['precio'];
                    $this->scancode = $value['scancode'];
                    $this->codigoRapido = $value['codigoRapido'];
                    $this->fechaExpiracion = $value['fechaExpiracion'];
                    //categoria
                    if($value['idcategoria']!=null){
                        $cat->id = $value['idcategoria'];
                        $cat->nombre = $value['nombrecategoria'];
                        array_push ($this->listaevento, $cat);
                    }
                }
                else {
                    $cat->id = $value['idcategoria'];
                    $cat->nombre = $value['nombrecategoria'];
                    array_push ($this->listaevento, $cat);
                }
            }
            return $this;
        }     
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar el producto'))
            );
        }
    }

    function Create(){
        try {
            $sql="INSERT INTO producto   (id, nombre, nombreAbreviado, descripcion, cantidad, precio, scancode, codigoRapido, fechaExpiracion)
                VALUES (:uuid, :nombre, :nombreAbreviado, :descripcion, :cantidad, :precio, :scancode, :codigoRapido, :fechaExpiracion)";
            //
            $param= array(':uuid'=>$this->id, ':nombre'=>$this->nombre, ':nombreAbreviado'=>$this->nombreAbreviado, ':descripcion'=>$this->descripcion, ':cantidad'=>$this->cantidad, ':precio'=>$this->precio,
                ':scancode'=>$this->scancode, ':codigoRapido'=>$this->codigoRapido, ':fechaExpiracion'=>$this->fechaExpiracion);
            $data = DATA::Ejecutar($sql,$param, false);
            if($data)
            {
                //save array obj
                if(CategoriasxProducto::Create($this->listaevento))
                    return true;
                else throw new Exception('Error al guardar las categorias.', 03);
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
            if($data){
                //update array obj
                if($this->listaevento!=null)
                    if(CategoriasxProducto::Update($this->listaevento))
                        return true;            
                    else throw new Exception('Error al guardar las categorias.', 03);
                else {
                    // no tiene categorias
                    if(CategoriasXProducto::Delete($this->id))
                        return true;
                    else throw new Exception('Error al guardar las categorias.', 04);
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

    private function CheckRelatedItems(){
        try{
            $sql="SELECT idproducto
                FROM categoriasxproducto R
                WHERE R.idproducto= :id";
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