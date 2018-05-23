<?php 
require_once("Conexion.php");

class CategoriasXProducto{
    public $idfactura=0;
    public $idproducto=0;
    public $cantidad=0;
    public $precio=0;

    public static function Create($obj){
        try {
            $created = true;
            foreach ($obj as $prodfact) {
                $sql="INSERT INTO productosxfactura   (idfactura, idproducto, cantidad, precio)
                VALUES (:idfactura, :idproducto, :cantidad, :precio)";
                //
                $param= array(':idfactura'=>$prodfact->idfactura, ':idproducto'=>$prodfact->idproducto, ':cantidad'=>$prodfact->cantidad, ':precio'=>$prodfact->precio);
                $data = DATA::Ejecutar($sql,$param,false);
                if(!$data)
                    $created= false;
            }
            return $created;
        }     
        catch(Exception $e) {
            return false;
        }
    }

    public static function Update($obj){
        try {
            $updated = true;
            // elimina todos los objetos relacionados
            $updated= self::Delete($obj[0]->idfactura);
            // crea los nuevos objetos
            $updated= self::Create($obj);
            return $updated;
        }     
        catch(Exception $e) {
            return false;
        }
    }

    public static function Delete($_idfactura){
        try {                 
            $sql='DELETE FROM categoriasxproducto  
                WHERE idfactura= :idfactura';
            $param= array(':idfactura'=> $_idfactura);
            $data= DATA::Ejecutar($sql, $param, false);
            if($data)
                return true;
            else false;
        }
        catch(Exception $e) {
            return false;
        }
    }
}
?>