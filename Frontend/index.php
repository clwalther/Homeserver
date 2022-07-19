<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeserver</title>

    <!-- global css -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/color.css">
    <link rel="stylesheet" href="../assets/css/font.css">

    <!-- gloabl javascript -->
    <script src="../assets/javascript/utils.js" async></script>
    <script src="../assets/javascript/client.js" async></script>
    <script src="../assets/javascript/search.js" async></script>
    <script src="../assets/javascript/script.js" async></script>

    <?php include './global.php'; ?>
</head>
<body>
    <?php 
        include './templates/header.php';

        if($client->isLogedIn()) {
    ?>
        <!-- logged in -->
        <section>
            <article>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Pariatur ab sed tempora temporibus animi debitis repellat similique odit, magnam fuga. Tenetur, ipsum! Quo, odio? Sunt non ipsam aliquid sapiente vero.
            </article>
        </section>
    <?php } else { ?>
        <!-- logged out -->
        <section>
            <article id="home">
                <h1 class="segoe-UI">
                    The virtual
                    <br>
                    living room
                </h1>
                <p>
                    Lorem ipsum dolor, sit amet consectetur adipisicing elit. Perspiciatis dolore unde soluta facere, consequuntur sapiente officiis odio velit nostrum accusantium ratione laudantium doloribus
                </p>
                <button class="segoe-UI" onclick="window.open('/utils/signup', '_self');">
                    Get started
                </button>
            </article>
        </section>
    <?php 
        }

        include './templates/footer.html';
    ?>
</body>
</html>