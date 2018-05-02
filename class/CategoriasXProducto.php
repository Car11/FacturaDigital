<?php 
require_once("Conexion.php");

class CategoriasXProducto{
    public $idcategoria;
    public $idproducto;

    public static function Create($obj){
        try {
            foreach ($obj as $catprod) {
                $sql="INSERT INTO categoriasxproducto   (idcategoria, idproducto)
                VALUES (:idcategoria, :idproducto)";
                //
                $param= array(':idcategoria'=>$catprod->idcategoria, ':idproducto'=>$catprod->idproducto);
                $data = DATA::Ejecutar($sql,$param,false);
                if($data)
                {
                    return true;
                }
                else throw new Exception('Error al guardar.', 02);
            }
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