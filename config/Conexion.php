<?php
require_once "global.php";

// Configurable timezone (default UTC) — set PRO_TIMEZONE in global.php
date_default_timezone_set(defined('PRO_TIMEZONE') ? PRO_TIMEZONE : 'UTC');

$conexion = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

mysqli_query($conexion, 'SET NAMES "' . DB_ENCODE . '"');

// No exponer detalles de la conexión al cliente; se registra solo en el log del servidor.
if (mysqli_connect_errno()) {
	error_log('SimplePOS: fallo de conexión a BD: ' . mysqli_connect_error());
	http_response_code(500);
	exit('Error interno de base de datos');
}

if (!function_exists('ejecutarConsulta')) {
	function ejecutarConsulta($sql)
	{
		global $conexion;
		$query = $conexion->query($sql);
		return $query;
	}

	function ejecutarConsultaSimpleFila($sql)
	{
		global $conexion;
		$query = $conexion->query($sql);
		$row = $query->fetch_assoc();
		return $row;
	}
	function ejecutarConsulta_retornarID($sql)
	{
		global $conexion;
		$query = $conexion->query($sql);
		if ($query === false) return false;
		return $conexion->insert_id;
	}

	function limpiarCadena($str)
	{
		global $conexion;
		return mysqli_real_escape_string($conexion, trim($str));
	}

	/**
	 * Ejecuta una consulta preparada. $params es un array de valores;
	 * $types es la cadena de tipos mysqli (ej. 'isss'). Si se omite se infiere.
	 * Devuelve el resultado (SELECT) o true/false (INSERT/UPDATE/DELETE).
	 */
	function ejecutarPreparada($sql, $params = [], $types = '')
	{
		global $conexion;
		$stmt = $conexion->prepare($sql);
		if ($stmt === false) {
			error_log('SimplePOS: prepare falló: ' . $conexion->error);
			return false;
		}
		if (!empty($params)) {
			if ($types === '') {
				$types = '';
				foreach ($params as $p) {
					$types .= is_int($p) ? 'i' : (is_float($p) ? 'd' : 's');
				}
			}
			$stmt->bind_param($types, ...$params);
		}
		if (!$stmt->execute()) {
			error_log('SimplePOS: execute falló: ' . $stmt->error);
			$stmt->close();
			return false;
		}
		$resultado = $stmt->get_result();
		$stmt->close();
		return $resultado === false ? true : $resultado;
	}

	/**
	 * Igual que ejecutarPreparada pero devolviendo solo la primera fila (array)
	 * o null si no hay resultados.
	 */
	function ejecutarPreparadaFila($sql, $params = [], $types = '')
	{
		$resultado = ejecutarPreparada($sql, $params, $types);
		if ($resultado === false || $resultado === true) return null;
		$fila = $resultado->fetch_assoc();
		return $fila === null ? null : $fila;
	}
}
