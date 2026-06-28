<?php
require "../config/Conexion.php";

class Venta
{
    public function __construct() {}

    public function listarMetodosPago()
    {
        $sql = "SELECT * FROM metodos_pago WHERE estado = 1";
        return ejecutarConsulta($sql);
    }

    public function insertar($idcliente, $idusuario, $totalventa, $metodopago, $idproducto, $cantidad, $precioventa, $descuento, $pagos_detalle = array())
    {
        $num_elementos = 0;
        $sw = true;

        // Verificar stock
        while ($num_elementos < count($idproducto)) {
            $idprod = $idproducto[$num_elementos];
            $cant = $cantidad[$num_elementos];

            $sql_stock = "SELECT stock FROM producto WHERE idproducto='$idprod'";
            $stock_actual = ejecutarConsultaSimpleFila($sql_stock);

            if ($stock_actual['stock'] < $cant) return "Agotado";
            $num_elementos++;
        }

        // Insertar venta
        $sql = "INSERT INTO venta (idcliente, idusuario, totalventa, metodopago, fechacreacion, estado) 
                VALUES ('$idcliente', '$idusuario', '$totalventa', '$metodopago', NOW(), '1')";

        $idventanew = ejecutarConsulta_retornarID($sql);
        $num_elementos = 0;
        $sw = true;

        // Insertar detalles
        while ($num_elementos < count($idproducto)) {
            $idprod = $idproducto[$num_elementos];
            $cant = $cantidad[$num_elementos];

            $sql_detalle = "INSERT INTO detalleventa (idventa, idproducto, cantidad, precioventa, descuento, fechacreacion, estado) 
                            VALUES ('$idventanew', '$idprod', '$cant', '$precioventa[$num_elementos]', '$descuento[$num_elementos]', NOW(), '1')";

            ejecutarConsulta($sql_detalle) or $sw = false;

            // Actualizar stock
            $sql_update_stock = "UPDATE producto SET stock = stock - '$cant' WHERE idproducto='$idprod'";
            ejecutarConsulta($sql_update_stock) or $sw = false;

            $num_elementos++;
        }

        // Insertar pagos
        foreach ($pagos_detalle as $pago) {
            $idmetodo = $pago['idmetodo'];
            $monto = $pago['monto'];
            $efectivo_recibido = isset($pago['efectivo_recibido']) ? $pago['efectivo_recibido'] : NULL;
            $efectivo_cambio = isset($pago['efectivo_cambio']) ? $pago['efectivo_cambio'] : NULL;

            $sql_pago = "INSERT INTO pagos_venta (idventa, idmetodo, monto, efectivo_recibido, efectivo_cambio, fecha_pago, estado)
                         VALUES ('$idventanew', '$idmetodo', '$monto', " .
                ($efectivo_recibido !== NULL ? "'$efectivo_recibido'" : "NULL") . ", " .
                ($efectivo_cambio !== NULL ? "'$efectivo_cambio'" : "NULL") . ", NOW(), 'APLICADO')";

            ejecutarConsulta($sql_pago) or $sw = false;
        }

        return $sw ? $idventanew : false;
    }

    public function anular($idventa)
    {
        $sql_detalles = "SELECT idproducto, cantidad FROM detalleventa WHERE idventa='$idventa'";
        $detalles = ejecutarConsulta($sql_detalles);

        while ($detalle = $detalles->fetch_object()) {
            $sql_devolver_stock = "UPDATE producto SET stock = stock + '$detalle->cantidad' WHERE idproducto='$detalle->idproducto'";
            ejecutarConsulta($sql_devolver_stock);
        }

        $sql_anular_pagos = "UPDATE pagos_venta SET estado='ANULADO' WHERE idventa='$idventa'";
        ejecutarConsulta($sql_anular_pagos);

        $sql = "UPDATE venta SET estado='0' WHERE idventa='$idventa'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($idventa)
    {
        $sql = "SELECT v.idventa, DATE(v.fechacreacion) as fecha, v.idcliente, 
                CONCAT(p.nombre,' ',p.apellidopaterno,' ',p.apellidomaterno) as cliente, 
                u.idusuario, CONCAT(u.nombre,' ',u.apellidopaterno,' ',u.apellidomaterno) as usuario, 
                v.totalventa, v.metodopago, v.estado 
                FROM venta v 
                INNER JOIN persona p ON v.idcliente = p.idpersona 
                INNER JOIN usuarios u ON v.idusuario = u.idusuario 
                WHERE v.idventa='$idventa'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarPagosVenta($idventa)
    {
        $sql = "SELECT p.*, m.nombre as metodo_nombre 
                FROM pagos_venta p
                INNER JOIN metodos_pago m ON p.idmetodo = m.id_metodo
                WHERE p.idventa = '$idventa'";
        return ejecutarConsulta($sql);
    }

    public function listarDetalle($idventa)
    {
        $sql = "SELECT dv.idventa, dv.idproducto, p.nombre, dv.cantidad, dv.precioventa, dv.descuento, 
                (dv.cantidad*dv.precioventa-dv.descuento) as subtotal 
                FROM detalleventa dv 
                INNER JOIN producto p ON dv.idproducto = p.idproducto 
                WHERE dv.idventa='$idventa'";
        return ejecutarConsulta($sql);
    }

    public function listar()
    {
        $sql = "SELECT v.idventa, DATE(v.fechacreacion) as fecha, v.idcliente, 
                CONCAT(p.nombre,' ',p.apellidopaterno,' ',p.apellidomaterno) as cliente, 
                u.idusuario, CONCAT(u.nombre,' ',u.apellidopaterno,' ',u.apellidomaterno) as usuario, 
                v.totalventa, v.metodopago, v.estado 
                FROM venta v 
                INNER JOIN persona p ON v.idcliente = p.idpersona 
                INNER JOIN usuarios u ON v.idusuario = u.idusuario 
                ORDER BY v.idventa DESC";
        return ejecutarConsulta($sql);
    }

    public function ventacabecera($idventa)
    {
        $sql = "SELECT v.idventa, v.idcliente, 
                CONCAT(p.nombre,' ',p.apellidopaterno,' ',p.apellidomaterno) AS cliente, 
                p.direccion, p.tipodocumento, p.numdocumento, p.email, p.telefono, 
                v.idusuario, CONCAT(u.nombre,' ',u.apellidopaterno,' ',u.apellidomaterno) AS usuario, 
                DATE(v.fechacreacion) AS fecha, v.totalventa, v.metodopago
                FROM venta v 
                INNER JOIN persona p ON v.idcliente = p.idpersona 
                INNER JOIN usuarios u ON v.idusuario = u.idusuario 
                WHERE v.idventa='$idventa'";
        return ejecutarConsulta($sql);
    }

    public function ventadetalles($idventa)
    {
        $sql = "SELECT p.nombre AS producto, p.codigo, d.cantidad, d.precioventa, d.descuento, 
                (d.cantidad*d.precioventa-d.descuento) AS subtotal 
                FROM detalleventa d 
                INNER JOIN producto p ON d.idproducto = p.idproducto 
                WHERE d.idventa='$idventa'";
        return ejecutarConsulta($sql);
    }

    public function verificarStock($idproducto)
    {
        $sql = "SELECT stock FROM producto WHERE idproducto='$idproducto'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarPagos($idventa)
    {
        $sql = "SELECT pv.id_pago, pv.idventa, pv.idmetodo, mp.nombre as metodo_pago, 
            pv.monto, pv.efectivo_recibido, pv.efectivo_cambio, pv.fecha_pago, pv.estado
            FROM pagos_venta pv
            INNER JOIN metodos_pago mp ON pv.idmetodo = mp.id_metodo
            WHERE pv.idventa = '$idventa' AND pv.estado = 'APLICADO'
            ORDER BY pv.fecha_pago ASC";

        return ejecutarConsulta($sql);
    }
}
