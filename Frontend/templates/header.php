<header>
    <a href="/" class="nomark">
        <img src="/assets/svg/hub24dp.svg">
    </a>    
    <div>
        <label>
            <form action="" method="">
                <input type="text" name="SEARCH" id="SEARCH_BAR"
                    placeholder="Search or jump to...">
                <div id="header-propositions">
                    <input type="submit" value="Lorem ipsum.txt">
                    <input type="submit" value="Lorem ipsum.docx">
                </div>
            </form>
        </label>
        <nav>
            <?php if($client->isLogedIn()) { ?>
                <!-- logged in -->
                <a href="/share">Share</a>
                <a href="/calender">Calender</a>
                <a href="/notes">Notes</a>
            <?php
                    echo $server->getHTMLExtension();
                } else {
            ?>
                <!-- logged out -->
                <a href="/explor">Explor</a>
                <a href="/automate">Automate</a>
                <a href="/security">Security</a>
                <a href="/community">Community</a>
            <?php } if($client->isAdmin()) { ?>
                <!-- type: admin -->
                <a href="/admin">Admin</a>
            <?php } ?>
        </nav>
    </div>
    <nav>
        <?php if($client->isLogedIn()) { ?>
            <!-- logged in -->
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M7 10l5 5 5-5H7z"/></svg>
                <div>
                    <button onclick="window.open('', '_self');">Notes</button>
                    <button onclick="window.open('', '_self');">Appointment</button>
                    <button onclick="window.open('', '_self');">Folder</button>
                    <button onclick="window.open('', '_self');">Document</button>
                    <button onclick="window.open('', '_self');">Textfile</button>
                </div>
            </div>
            <div>
                <img src="/assets/database/user-img/<?php echo $utils->getCookie("USERID");?>.svg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M7 10l5 5 5-5H7z"/></svg>
                <div>
                    <button onclick="window.open('/status',  '_self');">Set Status</button>
                    <button onclick="window.open('/profile', '_self');">Your Profile</button>
                    <button onclick="window.open('/upgrade', '_self');">Upgrade now</button>
                    <button onclick="window.open('/utils/signout', '_self');">Sign out</button>
                </div>
            </div>
        <?php } else { ?>
            <!-- logged out -->
            <a href="/utils/signin">Sign in</a>
            <a href="/utils/signup">Sign up</a>
        <?php } ?>
    </nav>
</header>