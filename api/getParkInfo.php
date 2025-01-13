<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

include "../connect.php";
$status = false;

if (!isset($_REQUEST["token"]) || !isset($_REQUEST["park"])) {
    $data = ["error" => "Некорректный запрос"];
} else {
    $token = $_REQUEST["token"];
    $park = $_REQUEST["park"];

    $stmt = $db->prepare("SELECT * FROM sessions WHERE token = ? AND finish = ?");
    $finish = 0;
    $stmt->bind_param("ss", $token, $finish);
    $stmt->execute() or die("Не удалось обработать запрос");
    $result = $stmt->get_result();

    if ($sessionData = mysqli_fetch_array($result)) {
        if ($sessionData["ip"] == getIP()) {
            $stmt = $db->prepare("SELECT * FROM accounts WHERE id = ?");
            $stmt->bind_param("s", $sessionData["userID"]);
            $stmt->execute() or die("Не удалось обработать запрос");
            $result = $stmt->get_result();
            if ($accountData = mysqli_fetch_array($result)) {
                $stmt = $db->prepare("SELECT * FROM companies WHERE id = ?");
                $stmt->bind_param("s", $accountData["companyID"]);
                $stmt->execute() or die("Не удалось обработать запрос");
                $result = $stmt->get_result();
                $companyID = mysqli_fetch_array($result)["id"];

                $stmt = $db->prepare(
                    "SELECT * FROM parks WHERE companyID = ? AND id = ?"
                );
                $stmt->bind_param("ss", $companyID, $park);
                $stmt->execute() or die("Не удалось обработать запрос");
                $result = $stmt->get_result();
                if ($parkData = mysqli_fetch_array($result)) {
                    $parkID = $parkData["id"];

                    $stmt = $db->prepare(
                        "SELECT * FROM park_images WHERE parkID = ?"
                    );
                    $stmt->bind_param("s", $parkID);
                    $stmt->execute() or die("Не удалось обработать запрос");
                    $result = $stmt->get_result();
                    if ($imageData = mysqli_fetch_array($result)) {
                        $image = [
                            "url" => $imageData["url"],
                            "width" => $imageData["width"],
                            "height" => $imageData["height"]
                        ];

                        $places = [];

                        $stmt = $db->prepare(
                            "SELECT * FROM park_places WHERE parkID = ?"
                        );
                        $stmt->bind_param("s", $parkID);
                        $stmt->execute() or die("Не удалось обработать запрос");
                        $result = $stmt->get_result();
                        while ($placeData = mysqli_fetch_array($result)) {
                            $placeInfo = [
                                "id" => $placeData["id"],
                                "ownedBy" => $placeData["ownedBy"],
                                "x" => $placeData["x"],
                                "y" => $placeData["y"],
                                "width" => $placeData["width"],
                                "height" => $placeData["height"],
                                "rotate" => $placeData["rotate"],
                                "number" => $placeData["numb"],
                                "changeDate" => $placeData["changeDate"]
                            ];
                            array_push($places, $placeInfo);
                        }
                        $status = true;
                        $data = [
                            "id" => $parkID,
                            "companyID" => $parkData["companyID"],
                            "name" => $parkData["name"],
                            "image" => $image,
                            "places" => $places
                        ];
                    } else {
                        $data = ["error" => "Не удалось получить изображение парковки"];
                    }
                } else {
                    $data = ["error" => "Не удалось найти парковку компании или пользователь в ней не состоит"];
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
