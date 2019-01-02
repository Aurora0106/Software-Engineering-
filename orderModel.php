<?php
require("dbconfig.php");
function insertOrder($serno,$pid,$order,$currWeek){
    global $db;
    $acc_cost = getAccCost($pid);
    $original_stock = "15";
    $expected_arrival = "0";
    $actual_arrival = "0";
    if($currWeek > 1){
        $original_stock = "SELECT (abc.original_stock - abc.demand) FROM (SELECT original_stock,demand FROM `player_record` WHERE pid = $pid AND week = $currWeek - 1) as abc ";
    } else if ($currWeek > 2) {
        if($pid == 1){
            $expected_arrival = "SELECT orders FROM player_record WHERE pid = $pid AND week = $currWeek - 2";
            $actual_arrival = "SELECT actual_shipment FROM player_record WHERE pid = $pid AND week = $currWeek - 2";
        }
        $expected_arrival = "SELECT orders FROM player_record WHERE pid = ($pid-1) AND week = $currWeek - 2";
        $actual_arrival = "SELECT actual_shipment FROM player_record WHERE pid = ($pid-1) AND week = $currWeek - 2";
    }
    $demand = getDemand($pid,$currWeek);
    if($original_stock > 0){
        $cost = $original_stock;
    } else {
        $cost = 0;
    }
    if($original_stock >= $demand) {    //有足夠的貨
        $actual_shipment = $demand;
    } else if ($original_stock > 0) {    //有多少給多少
        $actual_shipment= $original_stock;    
    } else {    //沒有貨
        $actual_shipment = 0;
    }
    if($currWeek == 1){
        $sql = "UPDATE 
                    `player_record`
                SET 
                    serno = ($serno),
                    original_stock = ($original_stock),
                    expected_arrival = ($expected_arrival),
                    actual_arrival = ($actual_arrival),
                    orders = ($order),
                    cost = ($original_stock),
                    acc_cost = ($acc_cost),
                    demand = ($demand),
                    actual_shipment = ($actual_shipment),
                    week = ($currWeek)
                WHERE 
                    pid = '$pid'";
    } else {
        $sql = "INSERT INTO 
                    `player_record`
                (serno,pid,week,original_stock,expected_arrival,actual_arrival,orders,cost,acc_cost,demand,actual_shipment)
                VALUES
                ($serno,$pid,$currWeek,($original_stock),($expected_arrival),0,$order,$cost,($acc_cost),($demand),$actual_shipment)";
    }
    echo $sql;
	$stmt = mysqli_prepare($db, $sql);
	mysqli_stmt_bind_param($stmt, "iii",$serno,$pid,$order);
    mysqli_stmt_execute($stmt); 
}
function getAccCost($pid)
{
    global $db;
    $sql = "SELECT SUM(cost) AS result FROM player_record WHERE pid= $pid ";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt); 
    $rs = mysqli_fetch_assoc($result);
    return $rs['result'];
}
function getDemand($pid,$currWeek)
{
    global $db;
    switch ($pid){
        case '4':
            // $dynamicSql = "SELECT demand FROM gamecycle WHERE week=(SELECT MAX(week) FROM period) ";
            $sql = "SELECT demand AS result FROM gamecycle WHERE week= $currWeek ";
            break;
        case '3':
            $sql = "SELECT orders AS result FROM player_record WHERE pid=4 AND week = $currWeek";
            break;
    }
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt); 
    $rs = mysqli_fetch_assoc($result);
    return $rs['result'];
}
function getOrderList($serno,$pid) 
{
    global $db;
    $sql = "select * from player_record where pid=? and serno =?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $pid, $serno);
    mysqli_stmt_execute($stmt); //執行SQL
    $result = mysqli_stmt_get_result($stmt); 
    return $result;
}
function r_playerrecord($serno,$pid){//清除playerrecord資料庫
    global $db;
    $sql = "TRUNCATE TABLE player_record";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_execute($stmt);  
    $sql = "INSERT INTO player_record 
    (serno,pid,week,original_stock,expected_arrival,actual_arrival,orders,cost,acc_cost,demand,actual_shipment)
    values
    ($serno,$pid,0,15,0,0,0,15,15,0,0)";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_execute($stmt);  
	// return;
}
function getOriginalStock($week,$pid){
    global $db;
    $sql = "SELECT original_stock AS result FROM player_record WHERE week = ($week) AND pid = $pid";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt); 
    $rs = mysqli_fetch_assoc($result);
    return $rs['result'];
}

function getActualArrival($week,$pid){
    global $db;
    $sql = "SELECT actual_arrival AS result FROM player_record WHERE week = ($week) AND pid = $pid";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt); 
    $rs = mysqli_fetch_assoc($result);
    return $rs['result'];
}

function countactual_shipment($serno,$pid){//計算實際出貨
    global $db;
    $hand = $original_stok + $actual_arrival;    
    if($hand >= $demand) {    //有足夠的貨
        $actual_shipment = $demand;
    } else if ($hand > 0) {    //有多少給多少
        $actual_shipment= $hand;    
    } else {    //沒有貨
        $actual_shipment = 0;
    } 
    updateOrder($serno,$cid,);
    $sql = "UPDATE `player_record` SET `actual_shipment` = original_stock";
    $stmt = mysqli_prepare($db, $sql);
	//mysqli_stmt_bind_param($stmt, "i",$order);
    mysqli_stmt_execute($stmt); 
}
