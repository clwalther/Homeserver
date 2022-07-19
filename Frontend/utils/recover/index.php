<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeserver | Recover</title>

    <!-- global css -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/color.css">
    <link rel="stylesheet" href="../../assets/css/font.css">

    <!-- gloabl javascript -->
    <script src="../../assets/javascript/utils.js" async></script>
    <script src="../../assets/javascript/client.js" async></script>
    <script src="../../assets/javascript/search.js" async></script>
    <script src="../../assets/javascript/script.js" async></script>
    <!-- specific css -->
    <link rel="stylesheet" href="../../assets/css/login.css">

    <?php
        include '../../global.php';

        // LISTEN FOR POST_REQUEST
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            // INIT
            $EMAIL = utf8_encode($_POST["EMAIL"]);
            // CALL
            $client->recover($EMAIL);
        }
    ?>
</head>
<body>
    <section>
        <img src="../../assets/svg/hub24dp.svg">
        <h1>Recover account</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input class="bg-black lightgrey" name="EMAIL" type="email" placeholder="Email">
            <input class="submit bg-positive white" type="submit" value="Recover">
        </form>
        <div>
            <p>Remember you password? <a href="/utils/signin">Sign in.</a></p>
        </div>
    </section></body>
</html>