<?php

error_reporting(-1);
ini_set('display_errors', 'On');

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');

$item = $_GET['item'];

include('dbConnection.php');

switch ($item) {
    case "zone":
        $data = GetZones($conn);
        break;
    case "pie":
        $data = GetPies($conn);
        break;
    case "area":
        $data = GetAreas($conn);
        break;
	case "linea":
        $data = GetLineas($conn);
        break;
    default:
        die("Requested item '" . $item . " not recognized.");
}

echo json_encode($data); 


$conn->close();

function GetZones($conn) {
	$sql = "SELECT * FROM ZoneStatus WHERE ZoneType = \"Aisle\"";
	$result = $conn->query($sql);
	$aisleData = array();
	if ($result->num_rows > 0)
		while($row = $result->fetch_assoc())
			$aisleData[] = (object) ['id' => $row["ZoneId"], 'status' => $row["Status"] == 0 ? false : true];
		
	$sql = "SELECT * FROM ZoneStatus WHERE ZoneType = \"Storage\"";
	$result = $conn->query($sql);
	$storageData = array();
	if ($result->num_rows > 0)
		while($row = $result->fetch_assoc())
			$storageData[] = (object) ['id' => $row["ZoneId"], 'status' => $row["Status"] == 0 ? false : true];

	$data = array();
	$data['aisle'] = $aisleData;
	$data['storage'] = $storageData;
	
	return $data;
}

function GetPies($conn) {
	$sql = "SELECT SUM(IF(TYPE=0, 1, 0)) Entries, SUM(IF(TYPE=1, 1, 0)) Exits, \"Total\" StorageId FROM StorageMovements" . 
		" UNION " .
		"SELECT SUM(IF(TYPE=0, 1, 0)) Entries, SUM(IF(TYPE=1, 1, 0)) Exits, StorageId FROM StorageMovements GROUP BY StorageId";

	$result = $conn->query($sql);
	
	$data = array();

	if ($result->num_rows > 0)
		while($row = $result->fetch_assoc())
			$data[$row["StorageId"]] = array((int) $row["Entries"], (int) $row["Exits"]);
	
	return $data;
}

function GetAreas($conn) {
	$sql = "SELECT LoadsInSystem, CONCAT(LPAD(HOUR(Time), 2, \"0\"), \":\", LPAD(MINUTE(Time), 2, \"0\")) Time FROM LoadsInSystemAvg";
	$result = $conn->query($sql);
	
	$values = array();
	$times = array();
	
	while($row = $result->fetch_assoc()) {
		$values[] = (int) $row["LoadsInSystem"];
		$times[] = $row["Time"];
	}
	
	$data = array();
	$data["data"] = (object) ['data' => $values, 'label' => "Loads In System"];
	$data["labels"] = $times;
		
	return $data;
}

/*
function GetArea($conn, $type) {
	$sql = "SELECT COUNT(*) Total FROM LoadsInSystem";
	$result = $conn->query($sql);
	while($row = $result->fetch_assoc())
		$total = (int) $row["Total"];
	
	$sql = "SELECT CONCAT(LPAD(HOUR(Time), 2, \"0\"), \":\", LPAD(MINUTE(Time), 2, \"0\")) Time, COUNT(*) Entries FROM StorageMovements WHERE TYPE = " . $type . " GROUP BY HOUR(Time), MINUTE(Time)";
	$result = $conn->query($sql);
	
	$values = array();
	$times = array();

	if ($result->num_rows > 0)
		while($row = $result->fetch_assoc()) {
			$values[] = (int) $row["Entries"];
			$times[] = $row["Time"];
		}
	
	$data = array();
	$data["data"] = (object) ['data' => $values, 'label' => $type == 0 ? "Entries" : $type == 1 ? "Exits" : "Total"];
	$data["labels"] = $times;
		
	return $data;
}

function GetAreas($conn) {
	$sql = "SELECT COUNT(*) Total FROM LoadsInSystem";
	$result = $conn->query($sql);
	while($row = $result->fetch_assoc())
		$total = (int) $row["Total"];
	
	$sql = "SELECT CONCAT(LPAD(HOUR(Time), 2, \"0\"), \":\", LPAD(MINUTE(Time), 2, \"0\")) Time, COUNT(*) Entries FROM StorageMovements WHERE TYPE = 0 GROUP BY HOUR(Time), MINUTE(Time)";
	$result = $conn->query($sql);
	
	$values = array();
	$times = array();

	if ($result->num_rows > 0)
		while($row = $result->fetch_assoc()) {
			$values[] = (int) $row["Entries"];
			$times[] = $row["Time"];
		}
	
	$data = array();
	$data["entries"] = GetArea($conn, 0);
	$data["exits"] = GetArea($conn, 1);
	$data["total"] = GetArea($conn, 2);
		
	return $data;
}
*/

function GetLinea($conn, $type) {
	$sql = "SELECT CONCAT(LPAD(HOUR(Time), 2, \"0\"), \":\", LPAD(MINUTE(Time), 2, \"0\")) Time, LoadsCount, AisleId FROM AisleMovementsAvg WHERE TYPE = " . $type . " ORDER BY HOUR(Time), MINUTE(Time), AisleId ASC";
	$result = $conn->query($sql);
	
	$values = array();
	$values[0] = array();
	$values[1] = array();
	$values[2] = array();
	$values[3] = array();
	
	$times = array();

	if ($result->num_rows > 0)
		while($row = $result->fetch_assoc()) {
			$values[(int) $row["AisleId"]][] = (int) $row["LoadsCount"];
			if (((int) $row["AisleId"]) == 0) $times[] = $row["Time"];
		}
	
	$data = array();
	$data["data"] = (object) ['data' => $values, 'label' => $type == 0 ? "Entries" : $type == 1 ? "Exits" : "Current"];
	$data["labels"] = $times;
		
	return $data;
}

function GetLineas($conn) {
	$data = array();
	$data["entries"] = GetLinea($conn, 0);
	$data["exits"] = GetLinea($conn, 1);
	$data["current"] = GetLinea($conn, 2);
		
	return $data;
}
