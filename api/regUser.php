<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

include "../connect.php";
$status = false;
if (
    !isset($_REQUEST["f_name"]) ||
    !isset($_REQUEST["l_name"]) ||
    !isset($_REQUEST["company"]) ||
    !isset($_REQUEST["birthdate"]) ||
    !isset($_REQUEST["mailOrPhone"]) ||
    !isset($_REQUEST["password"])
) {
    $data = ["error" => "Некорретный запрос"];
} else {
    $f_name = $_REQUEST["f_name"];
    $l_name = $_REQUEST["l_name"];
    $company = $_REQUEST["company"];
    $birthdate = $_REQUEST["birthdate"];
    $mailOrPhone = $_REQUEST["mailOrPhone"];
    $password = $_REQUEST["password"];

    $f_name = ucfirst(strtolower($f_name));
    $l_name = ucfirst(strtolower($l_name));

    if (strpos($mailOrPhone, "@")) {
        $email = $mailOrPhone;
        $phone = "--";
    } else {
        $phone = $mailOrPhone;
        $email = "--";
    }
    $stmt = $db->prepare("SELECT * FROM accounts WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute() or die("Не удалось обработать запрос");
    $result = $stmt->get_result();
    if ($phone == "--") {
        $phone = "-";
    } elseif ($email == "--") {
        $email = "-";
    }
    if ($unused = mysqli_fetch_array($result)) {
        $status = false;
        $data = ["error" => "Такой пользователь уже существует"];
    } else {
        if (strlen($f_name) > 1 && strlen($f_name) < 100) {
            if (strlen($l_name) > 1 && strlen($l_name) < 100) {
                if (
                    strlen($password) > 5 &&
                    strlen($password) < 30 &&
                    !strpos($password, " ")
                ) {
                    $stmt = $db->prepare(
                        "SELECT * FROM companies WHERE code = ?"
                    );
                    $stmt->bind_param("s", $company);
                    $stmt->execute() or die("Не удалось обработать запрос");
                    $result = $stmt->get_result();

                    if ($companyData = mysqli_fetch_array($result)) {
                        $company = $companyData["id"];
                        $password = md5($password);
                        
                        
                        $stmt = $db->prepare(
                            "INSERT INTO accounts (`f_name`, `l_name`, `companyID`, `phone`, `email`, `password`, `regDate`) VALUES (?, ?, ?, ?, ?, ?, ?)"
                        );

                        $stmt->bind_param(
                            "sssssss",
                            $f_name,
                            $l_name,
                            $company,
                            $phone,
                            $email,
                            $password,
                            time()
                        );

                        $stmt->execute() or die("Не удалось обработать запрос");
                        
                        
                        $stmt = $db->prepare(
                            "SELECT * FROM accounts WHERE phone = ? AND email = ?"
                        );
                        $stmt->bind_param("ss", $phone, $email);
                        $stmt->execute() or die("Не удалось обработать запрос");
                        $result = $stmt->get_result();
                        $userID = mysqli_fetch_array($result)["id"];


                        $stmt = $db->prepare(
                            "UPDATE sessions SET finish = ? WHERE ip = ? AND finish = ?"
                        );
                        $finish = 0;
                        $stmt->bind_param("sss", time(), getIP(), $finish);
                        $stmt->execute() or die("Не удалось обработать запрос");

                        $token = gen_token();
                        
                        
                        $stmt = $db->prepare(
                            "INSERT INTO sessions (`token`, `start`, `userID`, `ip`, `finish`) VALUES (?, ?, ?, ?, ?)"
                        );

                        $finish = 0;

                        $stmt->bind_param(
                            "sssss",
                            $token,
                            time(),
                            $userID,
                            getIP(),
                            $finish
                        );

                        $stmt->execute() or die("Не удалось обработать запрос");
                        $status = true;
                        $data = [
                            "token" => $token,
                            "f_name" => $f_name,
                            "l_name" => $l_name,
                        ];
                    } else {
                        $status = false;
                        $data = ["error" => "Указанная компания не существует"];
                    }
                } else {
                    $status = false;
                    $data = ["error" => "Пароль должен быть длиннее 5-ти символов и короче 30-ти"];
                }
            } else {
                $status = false;
                $data = ["error" => "Фамилия не может быть длиннее 100 символов и короче 1-го"];
            }
        } else {
            $status = false;
            $data = ["error" => "Имя не может быть длиннее 100 символов и короче 1-го"];
        }
    }
}

$answer = [
    "status" => $status,
    "data" => $data,
];

echo json_encode($answer, JSON_UNESCAPED_UNICODE);
?>
