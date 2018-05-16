<?php
if(isset($_POST["action"])){
    $opt= $_POST["action"];
    unset($_POST['action']);
    // Classes
    require_once("Conexion.php");
    // Session
    if (!isset($_SESSION))
        session_start();
    // Instance
    $factura= new Factura();
    switch($opt){
        case "ReadAll":
            echo json_encode($factura->ReadAll());
            break;
        case "Read":
            echo json_encode($factura->Read());
            break;
        case "Create":
            $factura->Create();
            break;
        case "Update":
            $factura->Update();
            break;
        case "Delete":
            echo json_encode($factura->Delete());
            break;   
    }    
}

class Factura{
    public $id=null;
    public $idusuario=null;
    public $fecha=null;
    //
    public $subtotal=0;    
    public $iva=0;
    public $porcentajeiva=0;
    public $descuento=0;
    public $porcentajeDescuento=0;
    public $total=0;
    //
    public $tipopago='';
    public $pagacon=0;
    public $vuelto=0;
    //
    public $listaproducto= array();
    
    function __construct(){
        // identificador único
        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            require_once("UUID.php");
            $this->id= $obj["id"] ?? UUID::v4();
            $this->idusuario= $obj["idusuario"] ?? '';
            $this->fecha= $obj["fecha"] ?? '';
            $this->subtotal= $obj["subtotal"] ?? '';
            $this->iva= $obj["iva"] ?? 0;            
            $this->porcentajeiva= $obj["porcentajeiva"] ?? 0;
            $this->descuento= $obj["descuento"] ?? '';            
            $this->porcentajeDescuento= $obj["porcentajeDescuento"] ?? 0;            
            $this->total= $obj["total"] ?? null;
            //Categorias del factura.
            if(isset($obj["listaproducto"] )){
                require_once("ProductosXFactura.php");
                //
                foreach ($obj["listaproducto"] as $idprod) {
                    $prodfact= new ProductosXFactura();
                    $prodfact->idcategoria= $idprod;
                    $prodfact->idproducto= $this->id;
                    array_push ($this->listaproducto, $prodfact);
                }
            }
        }
    }

    function ReadAll(){
        try {
            $sql='SELECT id, idusuario, fecha, descuento, iva, porcentajeiva , porcentajeDescuento
                FROM     factura       
                ORDER BY idusuario asc';
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
            $sql='SELECT p.id, p.idusuario, p.fecha, p.subtotal, iva, porcentajeiva, descuento, porcentajeDescuento, total, c.id as idcategoria,c.idusuario as nombrecategoria
                FROM factura  p LEFT JOIN categoriasxproducto cp on cp.idproducto = p.id
                    LEFT join categoria c on c.id = cp.idcategoria
                where p.id=:id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql,$param);     
            foreach ($data as $key => $value){
                require_once("Categoria.php");
                $cat= new Categoria(); // categorias del factura
                if($key==0){
                    $this->id = $value['id'];
                    $this->idusuario = $value['idusuario'];
                    $this->fecha = $value['fecha'];
                    $this->subtotal = $value['subtotal'];
                    $this->iva = $value['iva'];
                    $this->porcentajeiva = $value['porcentajeiva'];
                    $this->descuento = $value['descuento'];
                    $this->porcentajeDescuento = $value['porcentajeDescuento'];
                    $this->total = $value['total'];
                    //categoria
                    if($value['idcategoria']!=null){
                        $cat->id = $value['idcategoria'];
                        $cat->idusuario = $value['nombrecategoria'];
                        array_push ($this->listaproducto, $cat);
                    }
                }
                else {
                    $cat->id = $value['idcategoria'];
                    $cat->idusuario = $value['nombrecategoria'];
                    array_push ($this->listaproducto, $cat);
                }
            }
            return $this;
        }     
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar el factura'))
            );
        }
    }

    function Create(){
        try {
            $sql="INSERT INTO factura   (id, idusuario, fecha, subtotal, iva, porcentajeiva, descuento, porcentajeDescuento, total)
                VALUES (:uuid, :idusuario, :fecha, :subtotal, :iva, :porcentajeiva, :descuento, :porcentajeDescuento, :total)";
            //
            $param= array(':uuid'=>$this->id, ':idusuario'=>$this->idusuario, ':fecha'=>$this->fecha, ':subtotal'=>$this->subtotal, ':iva'=>$this->iva, ':porcentajeiva'=>$this->porcentajeiva,
                ':descuento'=>$this->descuento, ':porcentajeDescuento'=>$this->porcentajeDescuento, ':total'=>$this->total);
            $data = DATA::Ejecutar($sql,$param, false);
            if($data)
            {
                //save array obj
                if(ProductosXFactura::Create($this->listaproducto))
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
            $sql="UPDATE factura 
                SET idusuario=:idusuario, fecha=:fecha, subtotal= :subtotal, iva=:iva, porcentajeiva=:porcentajeiva, descuento=:descuento, porcentajeDescuento=:porcentajeDescuento, total=:total
                WHERE id=:id";
            $param= array(':id'=>$this->id, ':idusuario'=>$this->idusuario, ':fecha'=>$this->fecha, ':subtotal'=>$this->subtotal, ':iva'=>$this->iva, ':porcentajeiva'=>$this->porcentajeiva , 
                ':descuento'=>$this->descuento, ':porcentajeDescuento'=>$this->porcentajeDescuento, ':total'=>$this->total);
            $data = DATA::Ejecutar($sql,$param,false);
            if($data){
                //update array obj
                if($this->listaproducto!=null)
                    if(ProductosXFactura::Update($this->listaproducto))
                        return true;            
                    else throw new Exception('Error al guardar las categorias.', 03);
                else {
                    // no tiene categorias
                    if(ProductosXFactura::Delete($this->id))
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
                FROM categoriasxproducto x
                WHERE x.idproducto= :id";
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
            $sql='DELETE FROM factura  
                WHERE id= :id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql, $param, false);
            if($data){
                $sessiondata['status']=0; 
                return $sessiondata;
            }
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