class Utils {
    constructor() {

    }
    
    setFavicon() {
        let HEAD = document.getElementsByTagName("head")[0];
        if(window.matchMedia("(prefers-color-scheme: light)").matches) {
            HEAD.innerHTML += '<link rel="shortcut icon" href="/assets/img/lightFavicon.ico" type="image/x-icon"></link>';
        } else {
            HEAD.innerHTML += '<link rel="shortcut icon" href="/assets/img/darkFavicon.ico" type="image/x-icon"></link>';
        }
    }
}