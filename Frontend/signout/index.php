<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeserver | Sign Out</title>

    <!-- global css -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/color.css">
    <link rel="stylesheet" href="../css/font.css">
    
    <!-- global javascript -->
    <script src="../javascript/utils.js"></script>
    <script src="../javascript/script.js"></script>

    <?php
        include '../global.php';

        $client->signOut();
        $utils->changeLocation("/", "_self");
    ?>

</head>
<body>
</body>
</html>