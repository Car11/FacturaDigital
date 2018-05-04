<?php 
require_once("Conexion.php");

class CategoriasXProducto{
    public $idcategoria;
    public $idproducto;

    public static function Create($obj){
        try {
            $created = true;
            foreach ($obj as $catprod) {
                $sql="INSERT INTO categoriasxproducto   (idcategoria, idproducto)
                VALUES (:idcategoria, :idproducto)";
                //
                $param= array(':idcategoria'=>$catprod->idcategoria, ':idproducto'=>$catprod->idproducto);
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
            $updated= self::Delete($obj[0]->idproducto);
            // crea los nuevos objetos
            $updated= self::Create($obj);
            return $updated;
        }     
        catch(Exception $e) {
            return false;
        }
    }

    public static function Delete($_idproducto){
        try {                 
            $sql='DELETE FROM categoriasxproducto  
                WHERE idproducto= :idproducto';
            $param= array(':idproducto'=> $_idproducto);
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