class Client {
    constructor() {
        this.username  = this.getCookie("USERNAME");
        this.userid    = this.getCookie("USERID");
        this.email     = this.getCookie("EMAIL");
        this.auth      = this.getCookie("AUTH");
        this.isLogedIn = this.logedIn();
    }
    setFavicon() {
        let HEAD = document.getElementsByTagName("head")[0];
        if(window.matchMedia("(prefers-color-scheme: light)").matches) {
            HEAD.innerHTML += '<link rel="shortcut icon" href="/assets/icons/lightFavicon.ico" type="image/x-icon"></link>';
        } else {
            HEAD.innerHTML += '<link rel="shortcut icon" href="/assets/icons/darkFavicon.ico" type="image/x-icon"></link>';
        }
    }

    setCookie(key, value, expire="session") {
        if(!expire=="session") {
            const DATE = new Date();
            DATE.setTime(DATE.getTime() + (expire*24*60*60*1000));
            expire = DATE.toUTCString();
        }
        document.cookie = `${key}=${value}; expires=${expire}; path=/;`;
        return 0;
    }
    
    getCookie(key) {
        const COOKIES = decodeURIComponent(document.cookie);
        for(let COOKIE of COOKIES.split("; ")) {
            let HEAD  = COOKIE.split("=")[0];
            let VALUE = COOKIE.split("=")[1];
            if(key === HEAD) {
                return VALUE;
            }
        }
        return -1
    }
    
    logedIn() {
        if (isNaN(this.username) && isNaN(this.email) && this.userid != -1 && this.auth != -1) {
            return true;
        } else {
            return false;
        }
    }
}