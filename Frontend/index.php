<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeserver</title>

    <!-- global css -->
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/color.css">
    <link rel="stylesheet" href="./css/font.css">

    <!-- gloabl javascript -->
    <script src="./javascript/utils.js" async></script>
    <script src="./javascript/script.js" async></script>

    <?php include './global.php'; ?>
</head>
<body>
    <?php include './templates/header.php'; ?>
    <?php if($client->isLogedIn()) { ?>
        <section>
            <!-- logged in -->
        </section>
    <?php } else { ?>
        <section>
            <aside>
                <h1>
                    The virtual
                    <br>
                    living room
                </h1>
                <p>
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Perspiciatis excepturi, nobis consequatur a numquam corporis maxime omnis quasi blanditiis ex, quae doloremque sapiente! Laudantium, repellat officiis! Dolorum consequatur officia voluptate.
                </p>
            </aside>
            <aside>
                <!-- calander -->
            </aside>
        </section>
    <?php } ?>
    <footer>
    </footer>
</body>
</html>