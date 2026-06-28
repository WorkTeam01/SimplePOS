<?php
//incluir la conexion de base de datos
require "../config/Conexion.php";
class Consultas
{

    //implementamos nuestro constructor
    public function __construct() {}

    //listar compras por fecha
    public function comprasfecha($fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT DATE(c.fechacreacion) as fecha, u.nombre as usuario, 
                CONCAT(p.nombre, ' ', p.apellidopaterno, ' ', p.apellidomaterno) as proveedor, 
                c.total as total_compra, c.estado 
                FROM compra c 
                INNER JOIN persona p ON c.idproveedor = p.idpersona 
                INNER JOIN usuarios u ON c.idusuario = u.idusuario 
                WHERE DATE(c.fechacreacion) >= '$fecha_inicio' 
                AND DATE(c.fechacreacion) <= '$fecha_fin'";
        return ejecutarConsulta($sql);
    }

    //listar ventas por fecha y cliente
    // Implementar un método para listar las ventas por fecha y cliente
    public function ventasfechacliente($fecha_inicio, $fecha_fin, $idcliente)
    {
        $sql = "SELECT v.idventa, DATE(v.fechacreacion) as fecha, u.nombre as usuario, p.nombre as cliente, 
                        v.totalventa, v.estado, v.metodopago 
                FROM venta v 
                INNER JOIN usuarios u ON v.idusuario=u.idusuario 
                INNER JOIN persona p ON v.idcliente=p.idpersona 
                WHERE DATE(v.fechacreacion) >= '$fecha_inicio' AND DATE(v.fechacreacion) <= '$fecha_fin'";

        if ($idcliente != "") {
            $sql .= " AND v.idcliente = '$idcliente'";
        }

        return ejecutarConsulta($sql);
    }

    //total de compras hoy
    public function totalcomprahoy()
    {
        $sql = "SELECT IFNULL(SUM(total), 0) as total_compra 
                FROM compra 
                WHERE DATE(fechacreacion) = CURDATE()
                AND estado = 1";
        return ejecutarConsulta($sql);
    }

    //total de ventas hoy
    public function totalventahoy()
    {
        $sql = "SELECT IFNULL(SUM(totalventa), 0) as total_venta 
                FROM venta 
                WHERE DATE(fechacreacion) = CURDATE()
                AND estado = 1";
        return ejecutarConsulta($sql);
    }

    //productos más vendidos
    public function productos_mas_vendidos()
    {
        $sql = "SELECT 
        p.idproducto,
        p.nombre, 
        p.descripcion,
        p.imagen,
        p.codigo,
        p.precioventa as precio_actual,
        SUM(d.cantidad) AS total_unidades,
        SUM(d.cantidad * d.precioventa) AS venta_bruta,
        COUNT(DISTINCT v.idventa) AS num_ventas
        FROM detalleventa d 
        INNER JOIN producto p ON d.idproducto = p.idproducto 
        INNER JOIN venta v ON d.idventa = v.idventa
        WHERE v.estado = 1
        AND p.estado = 1
        GROUP BY p.idproducto 
        ORDER BY total_unidades DESC
        LIMIT 5";
        return ejecutarConsulta($sql);
    }

    //productos por agotar
    public function productos_por_agotar()
    {
        $sql = "SELECT 
            p.idproducto,
            p.nombre, 
            p.codigo, 
            p.descripcion, 
            p.stock,
            p.imagen,
            p.preciocompra,
            p.precioventa,
            c.nombre as categoria,
            (SELECT COUNT(*) FROM detalleventa dv 
             INNER JOIN venta v ON dv.idventa = v.idventa
             WHERE dv.idproducto = p.idproducto AND v.estado = 1) as num_ventas
            FROM producto p 
            LEFT JOIN categoria c ON p.idcategoria = c.idcategoria
            WHERE p.stock <= 10 AND p.estado = 1
            ORDER BY p.stock ASC, num_ventas DESC
            LIMIT 7";
        return ejecutarConsulta($sql);
    }

    // Total sales this week
    public function totalventasemana()
    {
        $sql = "SELECT IFNULL(SUM(totalventa), 0) as total_venta 
            FROM venta 
            WHERE YEARWEEK(fechacreacion, 1) = YEARWEEK(CURDATE(), 1)
            AND estado = 1";
        return ejecutarConsulta($sql);
    }

    // Total sales this month
    public function totalventames()
    {
        $sql = "SELECT IFNULL(SUM(totalventa), 0) as total_venta 
            FROM venta 
            WHERE MONTH(fechacreacion) = MONTH(CURDATE()) 
            AND YEAR(fechacreacion) = YEAR(CURDATE())
            AND estado = 1";
        return ejecutarConsulta($sql);
    }

    // Total clients count
    public function totalclientes()
    {
        $sql = "SELECT COUNT(*) as total 
            FROM persona 
            WHERE tipopersona = 'Cliente' AND estado = 1";
        return ejecutarConsulta($sql);
    }

    // Total suppliers count
    public function totalproveedores()
    {
        $sql = "SELECT COUNT(*) as total 
            FROM persona 
            WHERE tipopersona = 'Proveedor' AND estado = 1";
        return ejecutarConsulta($sql);
    }
    public function top_cliente_hoy()
    {
        $sql = "SELECT p.nombre, SUM(v.totalventa) as total_comprado 
            FROM venta v 
            JOIN persona p ON v.idcliente = p.idpersona 
            WHERE DATE(v.fechacreacion) = CURDATE() AND v.estado = 1 AND p.estado = 1
            GROUP BY v.idcliente 
            ORDER BY total_comprado DESC 
            LIMIT 1";
        return ejecutarConsulta($sql);
    }

    public function top_cliente_semana()
    {
        $sql = "SELECT p.nombre, SUM(v.totalventa) as total_comprado 
            FROM venta v 
            JOIN persona p ON v.idcliente = p.idpersona 
            WHERE YEARWEEK(v.fechacreacion) = YEARWEEK(CURDATE())
            AND v.estado = 1
            AND p.estado = 1
            GROUP BY v.idcliente 
            ORDER BY total_comprado DESC 
            LIMIT 1";
        return ejecutarConsulta($sql);
    }

    public function top_cliente_mes()
    {
        $sql = "SELECT p.nombre, SUM(v.totalventa) as total_comprado 
            FROM venta v 
            JOIN persona p ON v.idcliente = p.idpersona 
            WHERE MONTH(v.fechacreacion) = MONTH(CURDATE()) 
            AND YEAR(v.fechacreacion) = YEAR(CURDATE()) 
            AND v.estado = 1
            AND p.estado = 1
            GROUP BY v.idcliente 
            ORDER BY total_comprado DESC 
            LIMIT 1";
        return ejecutarConsulta($sql);
    }

    public function top_cliente_anio()
    {
        $sql = "SELECT p.nombre, SUM(v.totalventa) as total_comprado 
            FROM venta v 
            JOIN persona p ON v.idcliente = p.idpersona 
            WHERE YEAR(v.fechacreacion) = YEAR(CURDATE())
            AND v.estado = 1
            AND p.estado = 1
            GROUP BY v.idcliente 
            ORDER BY total_comprado DESC 
            LIMIT 1";
        return ejecutarConsulta($sql);
    }

    // Ventas del día anterior
    public function totalventaayer()
    {
        $sql = "SELECT IFNULL(SUM(totalventa), 0) as total_venta 
            FROM venta 
            WHERE DATE(fechacreacion) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
            AND estado = 1";
        return ejecutarConsulta($sql);
    }

    // Ventas de la semana anterior
    public function totalventasemaneanterior()
    {
        $sql = "SELECT IFNULL(SUM(totalventa), 0) as total_venta 
            FROM venta 
            WHERE YEARWEEK(fechacreacion, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK), 1)
            AND estado = 1";
        return ejecutarConsulta($sql);
    }

    // Ventas del mes anterior
    public function totalventamesanterior()
    {
        $sql = "SELECT IFNULL(SUM(totalventa), 0) as total_venta 
            FROM venta 
            WHERE YEAR(fechacreacion) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
            AND MONTH(fechacreacion) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
            AND estado = 1";
        return ejecutarConsulta($sql);
    }
}
