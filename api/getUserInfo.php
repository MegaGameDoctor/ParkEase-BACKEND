<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

include "../connect.php";
$status = false;

if (!isset($_REQUEST["token"])) {
    $data = ["error" => "Некорректный запрос"];
} else {
    $token = $_REQUEST["token"];

    $stmt = $db->prepare("SELECT * FROM sessions WHERE token = ? AND finish = ?");
    $finish = 0;
    $stmt->bind_param("ss", $token, $finish);
    $stmt->execute() or die("Не удалось обработать запрос");
    $result = $stmt->get_result();

    if ($sessionData = mysqli_fetch_array($result)) {
        if ($sessionData["ip"] == getIP()) {
            $userID = $sessionData["userID"];
            $stmt = $db->prepare("SELECT * FROM accounts WHERE id = ?");
            $stmt->bind_param("s", $userID);
            $stmt->execute() or die("Не удалось обработать запрос");
            $result = $stmt->get_result();
            if ($accountData = mysqli_fetch_array($result)) {
                $companyID = $accountData["companyID"];
                $stmt = $db->prepare("SELECT * FROM companies WHERE id = ?");
                $stmt->bind_param("s", $companyID);
                $stmt->execute() or die("Не удалось обработать запрос");
                $result = $stmt->get_result();
                if ($companyData = mysqli_fetch_array($result)) {
                    $availableParks = [];

                    $stmt = $db->prepare(
                        "SELECT * FROM parks WHERE companyID = ?"
                    );
                    $stmt->bind_param("s", $companyID);
                    $stmt->execute() or die("Не удалось обработать запрос");
                    $result = $stmt->get_result();
                    while ($parksData = mysqli_fetch_array($result)) {
                        $parkData = [
                            "id" => $parksData["id"],
                            "name" => $parksData["name"]
                        ];
                        
                        array_push($availableParks, $parkData);
                    }
                    
                    
                    $usedPlaces = [];
                    $stmt = $db->prepare(
                        "SELECT * FROM park_places WHERE ownedBy = ?"
                    );
                    $stmt->bind_param("s", $userID);
                    $stmt->execute() or die("Не удалось обработать запрос");
                    $result = $stmt->get_result();
                    while ($placesData = mysqli_fetch_array($result)) {
                     $stmtIn = $db->prepare(
                        "SELECT * FROM parks WHERE id = ?"
                    );
                    
                    $stmtIn->bind_param("s", $placesData["parkID"]);
                    $stmtIn->execute() or die("Не удалось обработать запрос");
                    $resultIn = $stmtIn->get_result();
                    $parkName = mysqli_fetch_array($resultIn)['name'];
                    
                    $parkDat = [
                        "id" => $placesData["parkID"],
                        "name" => $parkName
                        ];
                        
                        $placeData = [
                            "id" => $placesData["id"],
                            "park" => $parkDat,
                            "number" => $placesData['numb'],
                            "car" => $placesData["car"],
                            "carType" => $placesData["carType"]
                        ];
                        
                        array_push($usedPlaces, $placeData);
                    }
                    
                    $companyInfo = [
                        "id" => $companyData["id"],
                        "name" => $companyData["name"],
                        "descr" => $companyData["descr"]
                    ];
                    $status = true;
                    $data = [
                        "id" => $accountData["id"],
                        "f_name" => $accountData["f_name"],
                        "l_name" => $accountData["l_name"],
                        "company" => $companyInfo,
                        "phone" => $accountData["phone"],
                        "email" => $accountData["email"],
                        "availableParks" => $availableParks,
                        "usedPlaces" => $usedPlaces
                        //"regDate" => $accountData["regDate"],
                    ];
                } else {
                    $data = ["error" => "Не удалось найти компанию"];
                }
            } else {
                $data = ["error" => "Не удалось найти пользователя"];
            }
        } else {
            $data = ["error" => "Сессия недействительна"];
        }
    } else {
        $data = ["error" => "Сессия не найдена"];
    }
}

$answer = [
    "status" => $status, 
    "data" => $data
    ];

echo json_encode($answer, JSON_UNESCAPED_UNICODE);
?>
