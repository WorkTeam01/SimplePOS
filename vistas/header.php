<?php
require_once "../config/auth.php";
iniciarSesionSegura();

// Obtener el nombre del archivo actual
$current_file = basename($_SERVER['PHP_SELF']);
$usuarioImagen = !empty($_SESSION['imagen']) ? '../files/usuarios/' . rawurlencode((string) $_SESSION['imagen']) : '../public/img/avatar.png';
$usuarioNombre = htmlspecialchars((string) ($_SESSION['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
$usuarioApellidoPaterno = htmlspecialchars((string) ($_SESSION['apellidopaterno'] ?? ''), ENT_QUOTES, 'UTF-8');
$usuarioApellidoMaterno = htmlspecialchars((string) ($_SESSION['apellidomaterno'] ?? ''), ENT_QUOTES, 'UTF-8');
$usuarioCargo = htmlspecialchars((string) ($_SESSION['cargo'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es-bo">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SimplePOS | Sistema de Ventas</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="../public/css/bootstrap.min.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="../public/css/font-awesome.min.css">

  <!-- AdminLTE -->
  <link rel="stylesheet" href="../public/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../public/css/_all-skins.min.css">

  <!-- CSS específico para escritorio.php -->
  <?php if ($current_file == 'escritorio.php'): ?>
    <link rel="stylesheet" href="../public/css/escritorio.css">
  <?php endif; ?>

  <!-- Icons -->
  <link rel="apple-touch-icon" href="../public/img/icono.png">
  <link rel="shortcut icon" href="../public/img/icono.png">

  <!-- DATATABLES -->
  <link rel="stylesheet" href="../public/datatables/jquery.dataTables.min.css">
  <link rel="stylesheet" href="../public/datatables/buttons.dataTables.min.css">
  <link rel="stylesheet" href="../public/datatables/responsive.dataTables.min.css">

  <!-- Bootstrap Select -->
  <link rel="stylesheet" href="../public/css/bootstrap-select.min.css">
</head>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">
    <header class="main-header">
      <!-- Logo -->
      <a href="escritorio.php" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">
          <img src="../public/img/logo.png" class="img-circle" alt="Logo" style="height: 35px;">
        </span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
          <img src="../public/img/logo.png" class="img-circle" alt="SimplePOS" style="height: 35px; margin-right: 5px;">
          <b>Simple</b>POS
        </span>
      </a>
      <!-- Header Navbar: style can be found in header.less -->
      <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
          <span class="sr-only">NAVEGACIÓN</span>
        </a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="<?php echo $usuarioImagen; ?>" class="user-image" alt="User Image">
                <span class="hidden-xs"><?php echo $usuarioNombre; ?></span>
              </a>
              <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header">
                  <img src="<?php echo $usuarioImagen; ?>" class="img-circle" alt="User Image">

                  <p>
                    <?php echo trim($usuarioNombre . ' ' . $usuarioApellidoPaterno . ' ' . $usuarioApellidoMaterno); ?>
                    <small><?php echo $usuarioCargo; ?></small>
                  </p>
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="perfil.php" class="btn btn-default btn-flat">Perfil</a>
                  </div>
                  <div class="pull-right">
                    <a href="../ajax/usuario.php?op=salir" class="btn btn-default btn-flat">Salir</a>
                  </div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        <!-- Sidebar user panel -->

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
          <br>
          <?php
          if ($_SESSION['escritorio'] == 1) {
            echo ' <li><a href="escritorio.php"><i class="fa  fa-dashboard (alias)"></i> <span>Escritorio</span></a>
        </li>';
          }
          ?>
          <?php
          if ($_SESSION['almacen'] == 1) {
            echo ' <li class="treeview">
          <a href="#">
            <i class="fa fa-laptop"></i> <span>Almacen</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="articulo.php"><i class="fa fa-circle-o"></i> Articulos</a></li>
            <li><a href="categoria.php"><i class="fa fa-circle-o"></i> Categorias</a></li>
            <li><a href="traspaso.php"><i class="fa fa-circle-o"></i> Traspaso</a></li>
          </ul>
        </li>';
          }
          ?>
          <?php
          if ($_SESSION['compras'] == 1) {
            echo ' <li class="treeview">
          <a href="#">
            <i class="fa fa-th"></i> <span>Compras</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="ingreso.php"><i class="fa fa-circle-o"></i> Ingresos</a></li>
            <li><a href="proveedor.php"><i class="fa fa-circle-o"></i> Proveedores</a></li>
          </ul>
        </li>';
          }
          ?>

          <?php
          if ($_SESSION['ventas'] == 1) {
            echo '<li class="treeview">
          <a href="#">
            <i class="fa fa-shopping-cart"></i> <span>Ventas</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="venta.php"><i class="fa fa-circle-o"></i> ventas</a></li>
            <li><a href="cliente.php"><i class="fa fa-circle-o"></i> clientes</a></li>
          </ul>
        </li>';
          }
          ?>

          <?php
          if ($_SESSION['acceso'] == 1) {
            echo '  <li class="treeview">
          <a href="#">
            <i class="fa fa-folder"></i> <span>Acceso</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="usuario.php"><i class="fa fa-circle-o"></i> Usuarios</a></li>
            <li><a href="permiso.php"><i class="fa fa-circle-o"></i> Permisos</a></li>
          </ul>
        </li>';
          }
          ?>

          <?php
          if ($_SESSION['configuracion'] == 1) {  // Asumiendo que agregarás este permiso
            echo '<li class="treeview">
          <a href="#">
            <i class="fa fa-cogs"></i> <span>Configuración</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="empresa.php"><i class="fa fa-circle-o"></i> Datos Empresa</a></li>
            <li><a href="sucursal.php"><i class="fa fa-circle-o"></i> Sucursales</a></li>
          </ul>
        </li>';
          }
          ?>
          <?php
          if ($_SESSION['consultac'] == 1) {
            echo '     <li class="treeview">
          <a href="#">
            <i class="fa fa-bar-chart"></i> <span>Consulta Compras</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="comprasfecha.php"><i class="fa fa-circle-o"></i>Compras por fechas</a></li>
          </ul>
        </li>';
          }
          ?>

          <?php
          if ($_SESSION['consultav'] == 1) {
            echo '<li class="treeview">
          <a href="#">
            <i class="fa fa-bar-chart"></i> <span>Consulta Ventas</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="ventasfechacliente.php"><i class="fa fa-circle-o"></i> Consulta Ventas</a></li>

          </ul>
        </li>';
          }
          ?>
        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>
