var frame = null;
var deviceWidth = (window.innerWidth > 0) ? window.innerWidth : screen.width;
var deviceHeight = (window.innerHeight > 0) ? window.innerHeight : screen.height;
var frameWidth = (deviceWidth < 420) ? (deviceWidth - 20) : 440;
var frameHeight = (deviceHeight < 830) ? (deviceHeight - 50) : 750;

languageSelectEvent = function (language) {
    //sendGtagEvent('selected-language-' + language);
    language = language.toUpperCase();
    changeLanguageSelected(language);
};

function changeLanguageSelected(e) {
    var languageSelected = e.options ? e.options[e.selectedIndex].value : e;
    localStorage["language"] = languageSelected;
    var div = document.createElement("div");
    document.getElementsByTagName('body')[0].appendChild(div);
    div.outerHTML = "<iframe class='iframeBot' id='iframeBot' width='140px' height='60px' style='position:fixed;bottom: 5px; right: 10px; border-radius: 20px; border: 0; background: unset; z-index: 2000;' src='https://bot.vcsc.courts.ca.gov/chat.html'></iframe>";
    //div.outerHTML = "<iframe class='iframeBot' id='iframeBot' width='140px' height='60px' style='position:fixed;bottom: 5px; right: 10px; border-radius: 20px; border: 0; background: unset; z-index: 2000;' src='chat.html'></iframe>";
    frame.remove();

    var postMessageData = { type: 'state', language: languageSelected, county: '', questionId: '' };
    if (frame == null) {
        frame = document.getElementById('iframeBot');
        //frame = document.getElementById('iframeBot').contentWindow;
    }
    if (frame != null) {
        frame.postMessage(JSON.stringify(postMessageData), window.location.href);
    }
}

document.addEventListener("DOMContentLoaded", function(){
    (function () {
        var div = document.createElement("div");
        document.getElementsByTagName('body')[0].appendChild(div);
        div.outerHTML = "<iframe class='iframeBot' id='iframeBot' width='140px' height='60px' style='position:fixed; bottom: 10px; right: 10px; border-radius: 20px; border: 0; background: unset; z-index: 2000;' src='https://bot.vcsc.courts.ca.gov/chat.html'></iframe>";
        //div.outerHTML = "<iframe class='iframeBot' id='iframeBot' width='140px' height='60px' style='position:fixed; bottom: 10px; right: 10px; border-radius: 20px; border: 0; background: unset; z-index: 2000;' src='chat.html'></iframe>";
        //div.outerHTML = "<iframe class='iframeBot' id='iframeBot' width='140px' height='60px' style='position:fixed; bottom: 2px; right: 10px; border-radius: 20px; border: 0; background: unset; z-index: 2000;' src='https://jccatpchatbotwebprod.azurewebsites.net/chat.html'></iframe>";
    }());

    var eventMethod = window.addEventListener
        ? "addEventListener"
        : "attachEvent";
    var eventer = window[eventMethod];
    var messageEvent = eventMethod === "attachEvent"
        ? "onmessage"
        : "message";
    eventer(messageEvent, function (e) {
        if (e.data === "open" || e.message === "open") {
            document.getElementById('iframeBot').style.height = frameHeight + 'px';
            document.getElementById('iframeBot').style.width = frameWidth + 'px';
            document.getElementById('iframeBot').style.border = '1px #f4ad3b solid';
        }

        if (e.data === "close" || e.message === "close") {
            document.getElementById('iframeBot').style.height = 60 + 'px';
            document.getElementById('iframeBot').style.width = 140 + 'px';
            document.getElementById('iframeBot').style.border = '0px none';
        }
    });
});
