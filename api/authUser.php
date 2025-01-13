<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

include "../connect.php";
$status = false;

if (!isset($_REQUEST["mailOrPhone"]) || !isset($_REQUEST["password"])) {
    $data = ["error" => "Некорректный запрос"];
} else {
    $mailOrPhone = $_REQUEST["mailOrPhone"];
    $password = $_REQUEST["password"];

    if (strpos($mailOrPhone, "@")) {
        $email = $mailOrPhone;
        $phone = "";
    } else {
        $phone = $mailOrPhone;
        $email = "";
    }

    $stmt = $db->prepare("SELECT * FROM accounts WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute() or die("Не удалось обработать запрос");
    $result = $stmt->get_result();

    if ($accData = mysqli_fetch_array($result)) {
        if (md5($password) == $accData["password"]) {
            $token = gen_token();
            $userID = $accData["id"];
            
            $stmt = $db->prepare(
                "UPDATE sessions SET finish = ? WHERE finish = ? AND ip = ?"
            );
            $finish = 0;
            $stmt->bind_param("sss", time(), $finish, getIP());
            $stmt->execute() or die("Не удалось обработать запрос");


            $stmt = $db->prepare(
                "INSERT INTO sessions (`token`, `start`, `userID`, `ip`) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $token, time(), $userID, getIP());
            $stmt->execute() or die("Не удалось обработать запрос");
            $status = true;
            $data = [
                "token" => $token,
                "f_name" => $accData["f_name"],
                "l_name" => $accData["l_name"]
            ];
            $status = true;
        } else {
            $status = false;
            $data = ["error" => "Неверно указан пароль"];
        }
    } else {
        $status = false;
        $data = ["error" => "Вы указали неверные данные"];
    }
}

$answer = ["status" => $status, "data" => $data];

echo json_encode($answer, JSON_UNESCAPED_UNICODE);
?>
