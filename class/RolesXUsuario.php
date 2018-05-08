<?php 
require_once("Conexion.php");

class RolesXUsuario{
    public $idrol;
    public $idusuario;

    public static function Create($obj){
        try {
            $created = true;
            foreach ($obj as $rolusr) {
                $sql="INSERT INTO rolesxusuario   (idrol, idusuario)
                VALUES (:idrol, :idusuario)";
                //
                $param= array(':idrol'=>$rolusr->idrol, ':idusuario'=>$rolusr->idusuario);
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
            $updated= self::Delete($obj[0]->idusuario);
            // crea los nuevos objetos
            $updated= self::Create($obj);
            return $updated;
        }     
        catch(Exception $e) {
            return false;
        }
    }

    public static function Delete($_idusuario){
        try {                 
            $sql='DELETE FROM rolesxusuario  
                WHERE idusuario= :idusuario';
            $param= array(':idusuario'=> $_idusuario);
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