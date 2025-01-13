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
            $status = true;
            $data = ["error" => "Сессия действительна"];
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
