class Client {
    constructor() {
        this.username;
        this.userid;
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
    onLoad() {}
}