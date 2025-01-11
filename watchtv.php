<?php
    require "includes/init.php";

    $lastState = $_DB->query("SELECT * FROM laststates", true);
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>TV</title>
        <link rel="stylesheet" href="/static/css/main.css?223">
        <meta charset="utf-8">
    </head>
    <body oncontextmenu="return false" id="body" data-subtract-years="<?= SUBTRACT_YEARS ?>" data-channel="<?= $lastState["lastChannel"] ?>" data-volume="<?= $lastState["lastVolume"] / 100 ?>" data-muted="<?= $lastState["lastMuted"] ?>">
        <div id="teletext">
            <div id="teletext-top">
                <div>
                    Kanal 01
                </div>
                <div>
                    <div id="teletext-date"></div>
                </div>
            </div>
            <div id="teletext-middle">
                Channel Name
            </div>
            <div id="teletext-bottom">
                <div><strong>STUNDENPLAN</strong>:</div>
                <div id="teletext-plan">
                    <table border="0" style="width:100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>Show</td>
                            <td align="right">12.12 12:00</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <video id="player" autoplay <?php if ($lastState["lastMuted"]) : ?>muted<?php endif ?>>
            <source id="playerSource" type="video/mp4">
        </video>
        <div id="volume" <?php if ($lastState["lastMuted"]) : ?>class="show"<?php endif ?>>
            <div>
                <div>VOLUME</div>
                <div id="volumeText"><?php if ($lastState["lastMuted"]) : ?>Mute<?php else : ?><?= $lastState["lastVolume"] ?><?php endif ?></div>
            </div>
            <div>
                <div id="volumeBar" style="width:<?= $lastState["lastVolume"] ?>%"></div>
            </div>
        </div>
        <div id="channelNumber">0<?= $lastState["lastChannel"] ?></div>
        <script src="/static/js/main.js?4598"></script>
    </body>
</html>