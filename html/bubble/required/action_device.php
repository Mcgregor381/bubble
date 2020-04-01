<?php
////////////////////////////////////// DEVICE CONTROL SCRIPT/////////////////////////////////////////////
include_once 'config.php';

//GRAB TYPE FROM POST
$type = "device";
if(isset($_POST['type'])) $status = $_POST['type'];

//GRAB RELEVANT FLAGS
$status = 0;
if(isset($_POST['stat'])) $status = $_POST['stat'];
$hub_id = 0;
if(isset($_POST['hubID'])) $hub_id = $_POST['hubID'];
$device_id = 0;
if(isset($_POST['id'])) $device_id = $_POST['id'];

switch($type){
    case "device":       
        $stmt = $db->prepare("UPDATE device_info SET device_status = (device_status ^ 1) WHERE device_id = ?");
        $stmt->bind_param("i", $device_id);
        $stmt->execute();
        $stmt->close();
        $stmt = $db->prepare("SELECT device_status FROM device_info WHERE device_id = ?");
        $stmt->bind_param("i", $device_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result>fetch_assoc();
        $new_status = $row['device_status'];

        echo("{"status":$new_status}");
    break;
    case "room":
        $set_device = 0;
        
        if ($status == 0){
            $set_device = 1;
        }
        
        $stmt = $db->prepare("UPDATE device_info SET device_status = ? WHERE hub_id = ? AND room_id = ?");
        $stmt->bind_param("iii", $set_device, $hub_id, $room_id);
        $stmt->execute();
        $stmt->close();
    break;
}

?>
