function eID (el) {
    return document.getElementById(el);
}

function ajax(api, callback, post = {}) {
    var fd = new FormData();

    Object.keys(post).forEach(function(key) {
        fd.append(key, post[key]);
    });

    var xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        callback(this.responseText, xhttp);
    };
    xhttp.open("POST", api, true);
    xhttp.send(fd);

    return xhttp;
}

function getDayName(dateStr, locale) {
    var date = new Date(dateStr);
    return date.toLocaleDateString(locale, { weekday: 'short' });
}

var player                      = eID("player");
var playerSource                = eID("playerSource");
var maxVolume                   = 100;
var minVolume                   = 0;
player.volume                   = eID("body").getAttribute("data-volume");
var playerChannel               = eID("body").getAttribute("data-channel");
var playerVolumeClearer         = false;
var playerChannelNumberClearer  = false;
var playerChannelChangeClearer  = false;
var changedChannel              = false;
var changedStatus               = false; // Whether the user changed their status (channel, volume, muted) and they need to be put into the database
var subtractYears               = document.querySelector("body").getAttribute("data-subtract-years");
var teletextPage                = 1;
var teletextMaxPage             = 3;

function playerUpdateVolume (change) {
    changedStatus = true;

    clearTimeout(playerVolumeClearer);
    playerVolumeClearer = setTimeout(function() { eID("volume").classList.remove("show"); }, 3000);

    player.volume                      = parseFloat(player.volume + change).toFixed(2);
    eID("volumeBar").style.width    = Math.floor(player.volume * 100) + "%";
    eID("volumeText").innerText     = Math.floor(player.volume * 100);
    eID("volume").classList.add("show");
    if (player.muted) player.muted = false;
}

function playerMute () {
    changedStatus = true;

    clearTimeout(playerVolumeClearer);
    
    player.muted = true;
    eID("volumeText").innerText = "Mute";
    eID("volume").classList.add("show");
}

function playerUnmute () {
    changedStatus = true;

    clearTimeout(playerVolumeClearer);
    playerVolumeClearer = setTimeout(function() { eID("volume").classList.remove("show"); }, 3000);

    player.muted = false;
    eID("volumeText").innerText = Math.floor(player.volume * 100);
}

function changeChannel (channel, instant = false) {
    if (channel < 0) return false;

    if (!instant) clearTimeout(playerChannelChangeClearer);

    clearTimeout(playerChannelNumberClearer);
    playerChannelNumberClearer = setTimeout(function () {
        eID("channelNumber").classList.remove("show");
    }, 3000);

    if (eID("channelNumber").classList.contains("show") && !changedChannel) {
        var newNumber = "" + parseInt(eID("channelNumber").innerText) + channel;
        if (newNumber.length < 3) eID("channelNumber").innerText = newNumber;
    } else {
        changedChannel = false;
        eID("channelNumber").innerText = channel;
        if (parseInt(eID("channelNumber").innerText) < 10) eID("channelNumber").innerText = "0" + eID("channelNumber").innerText;
        eID("channelNumber").classList.add("show");
    }

    var waitTime;
    if (!instant) waitTime = 1000;
    else          waitTime = 0;

    playerChannelChangeClearer  = setTimeout(function() {
        if (parseInt(eID("channelNumber").innerText) != playerChannel) {
            playerChannel = eID("channelNumber").innerText;
            refreshCurrentChannel();
            changedChannel = true;
        }
    }, waitTime);
}

function updateTeletext() {
    if (teletextPage == 1) {
        ajax("/ajax/teletext.php", function(r) {
            document.getElementById("teletext").innerHTML = r;
        }, { channel: playerChannel, showDuration: player.duration });
    } else if (teletextPage == 2) {
        ajax("/ajax/teletextWeather.php", function(r) {
            document.getElementById("teletext").innerHTML = r;
        }, { channel: playerChannel, showDuration: player.duration });
    } else if (teletextPage == 3) {
        ajax("/ajax/teletextJokes.php", function(r) {
            document.getElementById("teletext").innerHTML = r;
        }, { channel: playerChannel, showDuration: player.duration });
    }

}

function refreshCurrentChannel () {
    ajax("/ajax/getPlayingShow.php", function(r) {
        var data = JSON.parse(r);

        if (data["fileName"] != "none") {
            playerSource.setAttribute("src", "http://oldtvemulator.xyz/static/videos/"+data["fileName"]+".mp4");
            player.load();
            player.currentTime = parseInt(data["playTime"]);
        } else {
            playerSource.setAttribute("src", "http://oldtvemulator.xyz/static/videos/static.mp4");
            player.load();
            player.currentTime = 0;
        }
        player.play();
        changedStatus = true;
    }, { channel: playerChannel });
}

player.onended = function() {
    setTimeout(function() {
        refreshCurrentChannel();
    }, 1000)
}

refreshCurrentChannel();

setInterval(function(r) {
    if (changedStatus) {
        changedStatus = false;
        ajax("/ajax/updateStatus.php", function(r) {}, { volume: player.volume * 100, channel: playerChannel, muted: player.muted });
    }
}, 1000);

setInterval(function() {
    if (document.getElementById("teletext").classList.contains("show")) {
        var date = new Date();
        date.setFullYear(date.getFullYear() - subtractYears);

        document.getElementById("teletext-date").innerHTML = getDayName(new Date(), "de-DE") + " " + date.toLocaleString("de-DE").replaceAll(".", ". ").replace(",", "");
    }
}, 50)

document.onkeypress = function (e) {
    e = e || window.event;

    switch (e.key) {
        case "m" :
            // Mute button [M]
            if (player.muted)   playerUnmute();
            else                playerMute();
        break;
        case "1" :
            changeChannel(1);
        break;
        case "2" :
            changeChannel(2);
        break;
        case "3" :
            changeChannel(3);
        break;
        case "4" :
            changeChannel(4);
        break;
        case "5" :
            changeChannel(5);
        break;
        case "6" :
            changeChannel(6);
        break;
        case "7" :
            changeChannel(7);
        break;
        case "8" :
            changeChannel(8);
        break;
        case "9" :
            changeChannel(9);
        break;
        case "0" :
            changeChannel(0);
            break;
    }
};

document.addEventListener ('keydown', function (e) {
    e = e || window.event;

    switch (e.which) {
        case 87 :
            // Volume Up [W]
            playerUpdateVolume(0.010);
            break;
        case 83 :
            // Volume Down [S]
            playerUpdateVolume(-0.010);
            break;
        case 38 :
            // Arrow Up
            if (document.getElementById("teletext").classList.contains("show")) {
                document.getElementById("teletext-bottom").scrollBy(0, -30);
            }
            break;
        case 40 :
            // Arrow Down
            if (document.getElementById("teletext").classList.contains("show")) {
                document.getElementById("teletext-bottom").scrollBy(0, 30);
            }
            break;
    }
});

document.addEventListener ('keyup', function (e) {
    e = e || window.event;

    switch (e.which) {
        case 37 :
            // Arrow Left (Next Channel)
            if (!document.getElementById("teletext").classList.contains("show")) {
                changeChannel(parseInt(playerChannel) - 1, true);
            } else if ((teletextPage - 1) > 0) {
                teletextPage--;
                updateTeletext();
            }
            break;
        case 39 :
            // Arrow Right (Previous Channel)
            if (!document.getElementById("teletext").classList.contains("show")) {
                changeChannel(parseInt(playerChannel) + 1, true);
            } else if ((teletextPage + 1) <= teletextMaxPage) {
                teletextPage++;
                updateTeletext();
            }
            break;
        case 13 :
            // Show/hide teletext [T]
            if (document.getElementById("teletext").classList.contains("show"))    document.getElementById("teletext").classList.remove("show");
            else                                                                            document.getElementById("teletext").classList.add("show");
            updateTeletext();
            break;
    }
});