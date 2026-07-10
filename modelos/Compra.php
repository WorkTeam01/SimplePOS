<?php 
require "../config/Conexion.php";

class Compra {
    public function __construct() {}

    public function insertar($idproveedor, $idusuario, $total, $idproducto, $cantidad, $preciocompra) {
        // Recalcular total en servidor (no confiar en el total enviado por el cliente)
        $total = 0;
        for ($i = 0; $i < count($idproducto); $i++) {
            $total += (int) $cantidad[$i] * (float) $preciocompra[$i];
        }

        $sql = "INSERT INTO compra (idproveedor, idusuario, total, fechacreacion, estado)
                VALUES ('$idproveedor', '$idusuario', '$total', NOW(), 1)";

        $idcompranew = ejecutarConsulta_retornarID($sql);
        $num_elementos = 0;
        $sw = true;

        while ($num_elementos < count($idproducto)) {
            $idprod = (int) $idproducto[$num_elementos];
            $cant = (int) $cantidad[$num_elementos];
            $precio = (float) $preciocompra[$num_elementos];

            // Insertar detalle de compra
            $sql_detalle = "INSERT INTO detallecompra (idcompra, idproducto, cantidad, preciocompra, fechacreacion, estado)
                          VALUES('$idcompranew', '$idprod', '$cant',
                          '$precio', NOW(), 1)";

            ejecutarConsulta($sql_detalle) or $sw = false;

            // Actualizar stock del producto (AQUÍ ESTÁ LA ACTUALIZACIÓN)
            $sql_update_stock = "UPDATE producto SET stock = stock + ".$cant."
                               WHERE idproducto = ".$idprod;

            ejecutarConsulta($sql_update_stock) or $sw = false;

            $num_elementos++;
        }
        return $sw;
    }

    public function anular($idcompra) {
        $compra = $this->mostrar($idcompra);
        if (!$compra || $compra['estado'] !== 'Aceptado') return false;

        // Primero obtenemos los detalles para revertir el stock
        $detalles = $this->listarDetalle($idcompra);
        $sw = true;

        if($detalles) {
            while ($reg = $detalles->fetch_object()) {
                // Revertir el stock (restamos lo añadido)
                $sql_revertir = "UPDATE producto SET stock = stock - ".$reg->cantidad." 
                                WHERE idproducto = ".$reg->idproducto;
                ejecutarConsulta($sql_revertir) or $sw = false;
            }
        }
        
        // Luego anulamos la compra
        if($sw) {
            $sql = "UPDATE compra SET estado=0 WHERE idcompra='$idcompra'";
            return ejecutarConsulta($sql);
        }
        return false;
    }

    public function activar($idcompra) {
        $compra = $this->mostrar($idcompra);
        if (!$compra || $compra['estado'] !== 'Anulado') return false;

        // Primero obtenemos los detalles para actualizar stock
        $detalles = $this->listarDetalle($idcompra);
        $sw = true;

        if($detalles) {
            while ($reg = $detalles->fetch_object()) {
                // Sumar el stock nuevamente
                $sql_actualizar = "UPDATE producto SET stock = stock + ".$reg->cantidad." 
                                 WHERE idproducto = ".$reg->idproducto;
                ejecutarConsulta($sql_actualizar) or $sw = false;
            }
        }
        
        // Luego activamos la compra
        if($sw) {
            $sql = "UPDATE compra SET estado=1 WHERE idcompra='$idcompra'";
            return ejecutarConsulta($sql);
        }
        return false;
    }

    public function mostrar($idcompra) {
        $sql = "SELECT c.idcompra, DATE(c.fechacreacion) as fecha, c.idproveedor, 
                CONCAT(p.nombre, ' ', p.apellidopaterno) as proveedor, 
                u.idusuario, CONCAT(u.nombre, ' ', u.apellidopaterno) as usuario, 
                c.total, 
                CASE c.estado 
                    WHEN 1 THEN 'Aceptado' 
                    WHEN 0 THEN 'Anulado' 
                END as estado 
                FROM compra c 
                INNER JOIN persona p ON c.idproveedor = p.idpersona 
                INNER JOIN usuarios u ON c.idusuario = u.idusuario 
                WHERE idcompra='$idcompra'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarDetalle($idcompra) {
        $sql = "SELECT dc.idcompra, dc.idproducto, p.nombre, dc.cantidad, dc.preciocompra 
                FROM detallecompra dc 
                INNER JOIN producto p ON dc.idproducto = p.idproducto 
                WHERE dc.idcompra='$idcompra'";
        return ejecutarConsulta($sql);
    }

    public function listar() {
        $sql = "SELECT c.idcompra, DATE(c.fechacreacion) as fecha, c.idproveedor, 
                CONCAT(p.nombre, ' ', p.apellidopaterno) as proveedor, 
                u.idusuario, CONCAT(u.nombre, ' ', u.apellidopaterno) as usuario, 
                c.total, 
                CASE c.estado 
                    WHEN 1 THEN 'Aceptado' 
                    WHEN 0 THEN 'Anulado' 
                END as estado 
                FROM compra c 
                INNER JOIN persona p ON c.idproveedor = p.idpersona 
                INNER JOIN usuarios u ON c.idusuario = u.idusuario 
                ORDER BY c.idcompra DESC";
        return ejecutarConsulta($sql);
    }
}
?>