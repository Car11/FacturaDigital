<?php 
require_once("Conexion.php");

class EventosXRol{
    public $idevento;
    public $idrol;

    public static function Create($obj){
        try {
            $created = true;
            foreach ($obj as $everol) {
                $sql="INSERT INTO eventosxrol   (idevento, idrol)
                    VALUES (:idevento, :idrol)";
                //
                $param= array(':idevento'=>$everol->idevento, ':idrol'=>$everol->idrol);
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
            $updated= self::Delete($obj[0]->idrol);
            // crea los nuevos objetos
            $updated= self::Create($obj);
            return $updated;
        }     
        catch(Exception $e) {
            return false;
        }
    }

    public static function Delete($_idrol){
        try {                 
            $sql='DELETE FROM eventosxrol  
                WHERE idrol= :idrol';
            $param= array(':idrol'=> $_idrol);
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