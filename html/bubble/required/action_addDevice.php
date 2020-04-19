<?php
    //////////////////////////////////////////////////////////// ADD DEVICE ACTION///////////////////////////////////////////////////
    /**
     * THIS FILE DOES A MAJORITY OF THE HEAVY LIFTING TO DO WITH QR CODES
     * QR CODES MUST TAKE THIS FORMAT
     * FIRST 3 CHARACTERS ARE DEVICE TYPE, E.G. A HUB IS 000
     * REMAINING CHARACTERS ARE AUTHCODE, THE UNIQUE IDENTIFIER IN THE DEVICE DATABASE
     */
    //LOAD QR CODE READING LIBRARY
    require dirname(__DIR__) . "/vendor/autoload.php";
    include_once dirname(__DIR__).'/required/config.php';
    use Zxing\QrReader;

    //GET PHOTO FROM POST BASE64 DATA
    $imageURI = filter_input(INPUT_POST, "photo", FILTER_SANITIZE_STRING);

    if($imageURI == FALSE){
        echo("{\"error\":\"Invalid request\"}");
        exit(0);
    }
    
    $imageURI = str_replace(' ', '+', $imageURI);
	$imageURI = str_replace('data:image/png;base64,', '', $imageURI);

    //DECODE BASE 64, BASE64_DECODE HAS TROUBLE DEALING WITH BASE64>5000 CHARACTERS, SO DO IT IN CLUMPS
    $decoded = "";
    for ($i=0; $i < ceil(strlen($imageURI)/256); $i++)
        $decoded = $decoded . base64_decode(substr($imageURI,$i*256,256));    
    //BEGIN SESSION
    session_start();
    //GRAB USER_ID
    $user_id = $_SESSION['user_id'];
    //END SESSION
    session_write_close();
    //PUT CONTENTS INTO FILE, THIS IS REQUIRED FOR QRCODE READER
    file_put_contents(dirname(__DIR__).'/upload/'.$user_id.".png",$decoded);
    //LOAD FILE AGAIN
    $image = dirname(__DIR__).'/upload/'.$user_id.".png";


    //SCAN IMAGE FOR QR CODE
    $qrcode = new QrReader($image);

    //GET TEXT FROM QR CODE IF IT EXISTS
    $qrText = $qrcode->text();
    if(!empty($qrText)){
        //GET DATA FROM QR CODE TEXT
        $deviceType = (int)substr($qrText, 0, 3);
        
        //SANITIZE DEVICE TYPE
        if(!is_numeric($deviceType)){
            echo("{\"error\":\"Invalid device type\"}");
            exit(0);
        }

        //GET AUTH_KEY, SANITIZE THIS VARIABLE!
        $auth_key = filter_var(substr($qrText, 3), FILTER_SANITIZE_STRING);

        if($auth_key == FALSE){
            echo("{\"error\":\"Auth key is invalid, please try again\"}");
            exit(0);
        }
        
        //SWITCH FOR DEVICE TYPE
        switch($deviceType){
            case 0: //HUB
                //FIND HUB IN TABLE WITH SPESIFIED AUTH_KEY
                $stmt = $db->prepare("SELECT * FROM hub_info WHERE auth_key = ?");
                $stmt->bind_param("s", $auth_key);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    //INSERT LINK IN HUB_USERS BETWEEN NEWLY ADDED HUB AND USER_ID FROM LOGGED IN SESSION
                    $row = $result->fetch_assoc();
                    $hub_id = $row['hub_id'];

                    //CHECK IF THE HUB ALREADY HAS AN OWNER
                    $stmtOwner = $db->prepare("SELECT * FROM hub_owners WHERE hub_id = ?");
                    $stmtOwner->bind_param("i", $hub_id);
                    $stmtOwner->execute();
                    $resultOwner = $stmtOwner->get_result();

                    //IF THE HUB DOESNT HAVE AN OWNER
                    if($resultOwner->num_rows == 0){

                        //ADD OWNER IN OWNERS TABLE
                        $stmtAddOwner = $db->prepare("INSERT INTO hub_owners (hub_id,hub_owner_id) VALUES (?, ?)");
                        $stmtAddOwner->bind_param("ii", $hub_id, $user_id);
                        if(!$stmtAddOwner->execute()){
                            echo "Hmm, something went wrong, please contact customer support!";
                        }
                        $stmtAddOwner->close();

                        //INSERT JOIN BETWEEN USER AND ACCOUNT
                        $stmt1 = $db->prepare("INSERT INTO hub_users (user_id, hub_id) VALUES (?, ?)");
                        $stmt1->bind_param("ii", $user_id, $hub_id);
                        if ($stmt1->execute()) {
                            echo "Sucessfully registered your new hub!\nNavigate home to view your newly added hub!";
                        }else{
                            echo "Hmm, something went wrong, please refresh the page and try again";
                        }
                        $stmt1->close(); 
                    } else {//THE HUB HAS AN OWNER, REQUEST ACCESS FROM OWNER
                        
                        
                        echo "An access request to the owner of this hub has been sent! Please ask them to check the email associated with their account, and confirm your access.";

                    }
                    $stmtOwner->close();
                } else {
                    echo "This Hub doesnt appear registered with us yet. Ensure the device has a green flashing LED on the top. For more troubleshooting, see <a href=\"index.php?action=troubleshooting\"Our Troubleshooting Tips</a>";
                }
                $stmt->close();

            break;
            default: //OTHER DEVICE
                //BEGIN SESSION
                session_start();
                //GRAB USER_ID
                $hub_id = $_SESSION['hub_id'];
                //END SESSION
                session_write_close();

                //SANITIZE DEVICE TYPE, CHECK THE DEVICE IS A KNOWN DEVICE TYPE
                $stmtCheckType = $db->prepare("SELECT * FROM device_types WHERE type_id = ?");
                $stmtCheckType->bind_param("i", $deviceType);
                if(!$stmtCheckType->execute()){
                    echo("{\"error\":\"Invalid device type\"}");
                    $stmtCheckType->close();
                    exit(0);
                }
                $result = $stmtCheckType->get_result();
                if($result->num_rows != 1){
                    echo("{\"error\":\"Invalid device type\"}");
                    $stmtCheckType->close();
                    exit(0);
                }
                //GET THE NAME OF THE DEVICE FOR DEFAULT NAME
                $device_name = $result->fetch_assoc()['type_name'];
                $stmtCheckType->close();

                //PREPARE NEW DEVICE INSERTION
                $stmtNewDevice = $db->prepare("INSERT INTO device_info (hub_id, device_auth_code, device_name, device_type, device_status) VALUES (?,?,?,?,0);");
                $stmtNewDevice->bind_param("issi", $hub_id, $auth_key, $device_name, $deviceType);
                if(!$stmtNewDevice->execute()){
                    echo("{\"error\":\"Unknown error, please try again\"}");
                    $stmtNewDevice->close();
                    exit(0);
                }
                echo("{\"success\":\"Device successfully added\"}");
                $stmtNewDevice->close();
                exit(0);
            break;
        }

    }


?>