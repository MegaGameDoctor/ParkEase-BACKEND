<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

include "../connect.php";
$status = false;

if (
    !isset($_REQUEST["token"]) ||
    !isset($_REQUEST["park"]) ||
    !isset($_REQUEST["place"]) ||
    !isset($_REQUEST["car"]) ||
    !isset($_REQUEST["carType"])
) {
    $data = ["error" => "Некорректный запрос"];
} else {
    $token = $_REQUEST["token"];
    $park = $_REQUEST["park"];
    $place = $_REQUEST["place"];
    $car = $_REQUEST["car"];
    $carType = $_REQUEST["carType"];

    $stmt = $db->prepare(
        "SELECT * FROM sessions WHERE token = ? AND finish = ?"
    );
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
                        "SELECT * FROM park_places WHERE id = ? AND parkID = ?"
                    );
                    $stmt->bind_param("ss", $place, $park);
                    $stmt->execute() or die("Не удалось обработать запрос");
                    $result = $stmt->get_result();
                    if ($placeData = mysqli_fetch_array($result)) {
                        $ownedBy = $placeData["ownedBy"];

                        $stmt = $db->prepare(
                            "SELECT COUNT(*) FROM park_places WHERE ownedBy = ? AND parkID = ?"
                        );

                        $stmt->bind_param("ss", $userID, $park);
                        $stmt->execute() or die("Не удалось обработать запрос");
                        $result = $stmt->get_result();
                        $usedCount = mysqli_fetch_array($result)["COUNT(*)"];
                        $data = ["ownedBy" => $ownedBy];
                        if ($usedCount == 0 && $ownedBy == 0) {
                            $status = true;
                            $stmt = $db->prepare(
                                "UPDATE park_places SET ownedBy = ?, car = ?, carType = ?, changeDate = ? WHERE id = ? AND parkID = ?"
                            );
                            $stmt->bind_param(
                                "ssssss",
                                $userID,
                                $car,
                                $carType,
                                time(),
                                $place,
                                $park
                            );
                            $stmt->execute() or
                                die("Не удалось обработать запрос");
                            $data = ["ownedBy" => $userID];
                        } elseif ($ownedBy == $userID) {
                            $status = true;
                            $stmt = $db->prepare(
                                "UPDATE park_places SET ownedBy = ?, changeDate = ? WHERE id = ? AND parkID = ?"
                            );
                            $empty = 0;
                            $stmt->bind_param(
                                "ssss",
                                $empty,
                                time(),
                                $place,
                                $park
                            );
                            $stmt->execute() or
                                die("Не удалось обработать запрос");
                            $data = ["ownedBy" => 0];
                        } elseif ($usedCount > 0) {
                            $data = ["error" => "Вы уже заняли место на этой парковке"];
                        }
                    } else {
                        $data = ["error" => "Место не существует или не привязано к указанной парковке"];
                    }
                } else {
                    $data = ["error" => "Не удалось найти парковку компании или Вы в ней не состоите"];
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
