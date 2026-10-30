<footer class="main-footer">
  <div class="pull-right hidden-xs">
    <b>Versión</b> 1.0
  </div>
  <div class="footer-content">
    <strong>SimplePOS</strong> - Open Source POS
  </div>
</footer>

<style>
  .footer-content {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
  }
</style>

<!-- jQuery 3 - DEBE CARGARSE PRIMERO -->
<script src="../public/js/jquery.min.js"></script>

<!-- Token CSRF: se adjunta automáticamente a todas las peticiones AJAX -->
<script>
  window.CSRF_TOKEN = "<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES); ?>";
  if (window.jQuery) {
    $.ajaxSetup({
      headers: { "X-CSRF-TOKEN": window.CSRF_TOKEN }
    });
  }
</script>

<!-- Bootstrap 3.3.7 -->
<script src="../public/js/bootstrap.min.js"></script>

<!-- AdminLTE App -->
<script src="../public/js/adminlte.min.js"></script>

<!-- DataTables Core - DESPUÉS DE JQUERY -->
<script src="../public/datatables/jquery.dataTables.min.js"></script>

<!-- DataTables Dependencies -->
<script src="../public/datatables/jszip.min.js"></script>
<script src="../public/datatables/pdfmake.min.js"></script>
<script src="../public/datatables/vfs_fonts.js"></script>

<!-- DataTables Buttons and Extensions -->
<script src="../public/datatables/dataTables.buttons.min.js"></script>
<script src="../public/datatables/buttons.html5.min.js"></script>
<script src="../public/datatables/buttons.colVis.min.js"></script>

<!-- Other Libraries -->
<script src="../public/js/bootbox.min.js"></script>
<script src="../public/js/bootstrap-select.min.js"></script>
</body>

</html>