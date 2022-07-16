<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeserver | Auth Sign Up</title>

    <!-- global css -->
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <link rel="stylesheet" href="../../../assets/css/color.css">
    <link rel="stylesheet" href="../../../assets/css/font.css">
    <link rel="stylesheet" href="../../../assets/css/login.css">

    <!-- gloabl javascript -->
    <script src="../../../assets/javascript/utils.js"></script>
    <script src="../../../assets/javascript/script.js"></script>

    <?php
        include '../../global.php'; 

        // LISTEN FOR POST_REQUEST
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            // INIT
            $CODE = utf8_encode($_POST["CODE"]);
            // CALL
            $client->signUpEmailAuth($CODE);
        }

        function send() {
            $utils    = $GLOBALS["utils"];
            $client   = $GLOBALS["client"];
            $database = $GLOBALS["database"];
            // INIT
            $TYPE     = "SIGNUP";
            $TABLE    = "TEMP";
            $RECEIVER = $utils->getCookie("EMAIL");
            $COLUMNS  = ["*"];
            $LIKE     = [
                "EMAIL" => $RECEIVER
            ];

            $queryAns = $database->select($TABLE, $COLUMNS, $LIKE);
            // CALL
            $client->sendEmail($TYPE, $RECEIVER, $CODE);
        }
        send();
    ?>
</head>
<body>
    <section>
        <img src="../../assets/svg/hub24dp.svg">
        <h1>Autheticate your E-Mail Account</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input class="bg-black lightgrey" type="code" name="CODE" placeholder="6-digit Code">
            <input class="submit bg-positive white" type="submit" value="Submit">
        </form>
        <div>
            <p>E-Mail hasn't arrived? <a href="/signup/auth">Resend Code.</a></p>
        </div>
    </section>
</body>
</html>