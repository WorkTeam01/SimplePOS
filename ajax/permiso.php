 <?php
require_once "../config/auth.php";
requireAuth();
require_once "../modelos/Permiso.php";

$categoria=new Permiso();


switch ($_GET["op"]) {
	
    case 'listar':
		$rspta=$categoria->listar();
		$data=Array();

		while ($reg=$rspta->fetch_object()) {
			$data[]=array(
            
            "0"=>htmlspecialchars($reg->nombre)
            
              );
		}
		$results=array(
             "sEcho"=>1,//info para datatables
             "iTotalRecords"=>count($data),//enviamos el total de registros al datatable
             "iTotalDisplayRecords"=>count($data),//enviamos el total de registros a visualizar
             "aaData"=>$data); 
		echo json_encode($results);
		break;
}
 ?>