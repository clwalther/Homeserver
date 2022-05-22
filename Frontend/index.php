<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeserver</title>
    <link rel="shortcut icon" href="./assets/icons/favicon.ico" type="image/x-icon">

    <!-- global css -->
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/color.css">
    <link rel="stylesheet" href="./css/font.css">

    <!-- gloabl javascript -->
    <script src="./javascript/utils.js"></script>
    <script src="./javascript/script.js"></script>

    <?php include './global.php'; ?>
</head>
<body>
    <header>
        <a href="/" class="nomark">
            <img src="./assets/svg/hub24dp.svg">
        </a>    
        <div>
            <label>
                <form action="" method="">
                    <input type="text" name="SEARCH" id="SEARCH_BAR"
                        placeholder="Search or jump to...">
                    <div>
                        <input type="submit" value="Lorem ipsum.txt">
                        <input type="submit" value="Lorem ipsum.docx">
                    </div>
                </form>
            </label>
            <nav>
                <a href="./share">Share</a>
                <a href="./printer">Printer</a>
                <a href="./lorem">Lorem</a>
            </nav>
        </div>
        <nav>
            <a href="./signin">Sign in</a>
            <a href="./signup">Sign up</a>
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M7 10l5 5 5-5H7z"/></svg>
            </div>
            <div>
                <img src="/assets/DATABASE/USERIMG/<?php echo $_COOKIE["USERID"];?>.svg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M7 10l5 5 5-5H7z"/></svg>
                <div>
                    <button onclick="window.open('/status',  '_self')">Set Status</button>
                    <button onclick="window.open('/profile', '_self')">Your Profile</button>
                    <button onclick="window.open('/premium', '_self')">Upgrade now</button>
                    <button onclick="window.open('/signout', '_self')">Sign out</button>
                </div>
            </div>
        </nav>
    </header>
    <section>
    </section>
    <footer>
    </footer>
</body>
</html>