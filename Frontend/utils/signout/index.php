<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeserver | Sign Out</title>

    <!-- global css -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/color.css">
    <link rel="stylesheet" href="../../assets/css/font.css">
    <!-- specific css -->
    <link rel="stylesheet" href="../../assets/css/login.css">
    
    <?php 
        include '../../global.php';

        $client->signOut();
        $utils->changeLocation("/", "_self");
    ?>

</head>
<body>
</body>
</html>