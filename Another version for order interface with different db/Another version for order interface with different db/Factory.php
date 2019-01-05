<?php
require("dbconfig.php");
require("orderModel.php");
require("playerModel.php");
global $db;
$pid=1;
$tname=1;
?>
<!DOCTYPE html>
<html>

<head>
	<title>工廠</title>
</head>
<body>
    <H1>1.Factory</H1>
	<?php include("formTemplate.php"); ?>
	<hr>
    <table style="width: 100%">
	    <tbody><tr>
			<td>週次</td>
			<td>庫存</td>
		    <td>前期下訂</td>
		    <td>前期訂貨正在路上的訂單</td>
			<td>準備進貨</td>
			<td>下游訂單</td>
			<td>累計成本</td>
           <td>實際出貨</td>

	    </tr>
		<?php
		$result =getOrderList($tname,$pid);
		while (	$rs = mysqli_fetch_assoc($result)) {
			echo 
			"<tr>",
			"<td>" , $rs['week'] , "</td>",
			"<td>" , $rs['stock'], "</td>",
			"<td>" , $rs['demand'], "</td>",
			"<td>" , $rs['ontheway'], "</td>",
			"<td>" , $rs['readyin'], "</td>",
			"<td>" , $rs['ordered'], "</td>",
			"<td>" , $rs['cost'], "</td>",
			"<td>" , $rs['actual_shipment'], "</td>",
			"</tr>";
		
		}
			?>
    </table>
</body>
</html>