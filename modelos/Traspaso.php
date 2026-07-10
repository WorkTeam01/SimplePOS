<?php
require_once "../config/Conexion.php";

class Traspaso
{
    // Constructor
    public function __construct() {}

    // Método para insertar un nuevo traspaso
    public function insertar($idusuario, $motivo, $observaciones, $idproducto, $cantidad)
    {
        $sql = "INSERT INTO traspaso (idusuario, motivo, observaciones) 
                VALUES ('$idusuario', '$motivo', '$observaciones')";

        // Usamos ejecutarConsulta_retornarID para obtener el ID del traspaso insertado
        $idtraspasonew = ejecutarConsulta_retornarID($sql);

        $num_elementos = 0;
        $sw = true;

        while ($num_elementos < count($idproducto)) {
            $idprod = (int) $idproducto[$num_elementos];
            $cant = (int) $cantidad[$num_elementos];

            $sql_detalle = "INSERT INTO detalletraspaso(idtraspaso, idproducto, cantidad)
                           VALUES ('$idtraspasonew', '$idprod', '$cant')";

            ejecutarConsulta($sql_detalle) or $sw = false;

            $num_elementos++;
        }

        return $sw;
    }

    // Método para completar un traspaso
    public function completar($idtraspaso, $iddetalletraspaso)
    {
        $fecha_completado = date("Y-m-d H:i:s");
        $sql = "UPDATE traspaso SET estado='Completado', fecha_completado='$fecha_completado' 
                WHERE idtraspaso='$idtraspaso'";

        ejecutarConsulta($sql);

        $num_elementos = 0;
        $sw = true;

        while ($num_elementos < count($iddetalletraspaso)) {
            $iddetalle = (int) $iddetalletraspaso[$num_elementos];

            // Obtener el idproducto y cantidad para actualizar el stock
            $sql_detalle = "SELECT idproducto, cantidad FROM detalletraspaso
                           WHERE iddetalletraspaso='$iddetalle'";
            $respuesta = ejecutarConsulta($sql_detalle);
            $reg = $respuesta->fetch_object();
            $idproducto = $reg->idproducto;
            $cantidad = $reg->cantidad;

            // Actualizar el estado del detalle
            $sql_update = "UPDATE detalletraspaso SET estado='Completado'
                          WHERE iddetalletraspaso='$iddetalle'";

            ejecutarConsulta($sql_update) or $sw = false;

            // Actualizar el stock del producto
            $sql_update_stock = "UPDATE producto SET stock = stock - '$cantidad' 
                               WHERE idproducto = '$idproducto'";
            ejecutarConsulta($sql_update_stock) or $sw = false;

            $num_elementos++;
        }

        return $sw;
    }

    // Método para cancelar un traspaso
    public function cancelar($idtraspaso)
    {
        $sql = "UPDATE traspaso SET estado='Cancelado' WHERE idtraspaso='$idtraspaso'";
        $sql_detalle = "UPDATE detalletraspaso SET estado='Cancelado' WHERE idtraspaso='$idtraspaso'";

        ejecutarConsulta($sql);
        ejecutarConsulta($sql_detalle);

        return true;
    }

    // Método para listar todos los traspasos
    public function listar()
    {
        $sql = "SELECT t.idtraspaso, t.fecha_solicitud, t.motivo,
                CONCAT(u.nombre, ' ', u.apellidopaterno) as usuario,
                t.estado
                FROM traspaso t
                INNER JOIN usuarios u ON t.idusuario = u.idusuario
                ORDER BY t.idtraspaso DESC";

        return ejecutarConsulta($sql);
    }

    // Método para mostrar los datos de un traspaso específico
    public function mostrar($idtraspaso)
    {
        $sql = "SELECT t.idtraspaso, t.idusuario,
                t.fecha_solicitud, t.fecha_completado,
                t.motivo, t.observaciones, t.estado,
                CONCAT(u.nombre, ' ', u.apellidopaterno) as usuario
                FROM traspaso t
                INNER JOIN usuarios u ON t.idusuario = u.idusuario
                WHERE t.idtraspaso = '$idtraspaso'";

        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar los detalles de un traspaso
    public function listarDetalle($idtraspaso)
    {
        $sql = "SELECT dt.iddetalletraspaso, dt.idtraspaso, dt.idproducto, 
                p.nombre as producto, p.codigo, p.stock, p.imagen,
                dt.cantidad, dt.estado
                FROM detalletraspaso dt
                INNER JOIN producto p ON dt.idproducto = p.idproducto
                WHERE dt.idtraspaso = '$idtraspaso'";

        return ejecutarConsulta($sql);
    }

    // Método para verificar el stock disponible
    public function verificarStock($idproducto, $cantidad)
    {
        $sql = "SELECT stock FROM producto WHERE idproducto = '$idproducto'";
        $respuesta = ejecutarConsulta($sql);
        $reg = $respuesta->fetch_object();

        return ($reg->stock >= $cantidad);
    }

    // Método para contar los traspasos por estado
    public function contarTraspasosPorEstado()
    {
        $sql = "SELECT estado, COUNT(*) as total FROM traspaso GROUP BY estado";
        return ejecutarConsulta($sql);
    }
}
