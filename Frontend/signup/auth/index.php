<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeserver | Auth</title>
    
    <!-- global css -->
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/color.css">
    <link rel="stylesheet" href="../../css/font.css">
    <link rel="stylesheet" href="../../css/login.css">

    <!-- gloabl javascript -->
    <script src="../../javascript/utils.js"></script>
    <script src="../../javascript/script.js"></script>

    <?php
        include '../../global.php'; 

        // GET CODE
        $query  = "SELECT * FROM ".$TABLENAME1." WHERE EMAIL='".$_COOKIE["EMAIL"]."' AND USERNAME='".$_COOKIE["USERNAME"]."';";
        $return = $CONNECTION->query($query);
        while($row = $return->fetch_assoc()) {
            $CODE = $row["CODE"];
        }

        // LISTEN FOR POST_REQUEST
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            // INIT
            $INPUT  = $_POST["CODE"];
            
            AUTH_EMAIL($INPUT);
        } else {
            SEND_EMAIL("SIGNUP", $_COOKIE["EMAIL"], $CODE);
        }
    ?>
</head>
<body>
    <header>
        <a href="/signup"><- Back</a>
    </header>
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