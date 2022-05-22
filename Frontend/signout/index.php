<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeserver | Sign Out</title>
    <link rel="shortcut icon" href="../assets/icons/favicon.ico" type="image/x-icon">

    <?php
        include '../global.php';

        $client->signOut();
        $utils->changeLocation("/", "_self");
        ?>
</head>
<body>
</body>
</html>