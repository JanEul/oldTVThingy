<?php
    require "../includes/init.php";
    $channel = $_DB->query("SELECT * FROM channels WHERE channel = :CHANNEL", true, [":CHANNEL" => $_POST["channel"]]);
    $Queue = $_DB->query("SELECT shows.show_name, queue.playTime FROM queue INNER JOIN shows ON queue.show_id = shows.show_id WHERE queue.channel_id = :CHANNEL AND queue.playTime > NOW() AND shows.isAd = 0 ORDER BY queue.playTime ASC LIMIT 65", false, [":CHANNEL" => $_POST["channel"]]);
    $currentlyPlaying = $_DB->query("SELECT shows.show_name, shows.isAd, queue.playTime FROM queue INNER JOIN shows ON shows.show_id = queue.show_id WHERE queue.playTime <= NOW() AND channel_id = :CHANNEL ORDER BY queue.playTime DESC LIMIT 1", true, [":CHANNEL" => $_POST["channel"]]);

    if ($channel["adsAmount"])  $adsAddTime = $channel["adsAmount"] * 15;
    else                        $adsAddTime = 0;
    ?>
<?php if ($channel) : ?>
<div id="teletext-top">
    <div>
        Kanal <?= $_POST["channel"] ?>
    </div>
    <div>
        <div id="teletext-date"></div>
    </div>
</div>
<div id="teletext-middle">
    <?= $channel["channelName"] ?>
</div>
<div id="teletext-bottom">
    <div style="font-size:35px"><strong>STUNDENPLAN</strong>:</div>
    <div id="teletext-plan">
        <table border="0" style="width:100%" cellspacing="0" cellpadding="4">
            <?php if ($currentlyPlaying) : ?>
                <tr>
                    <td valign="center" width="78%" style="border-color:orange"><div class="teletext-title" style="color:orange"><?php if (!$currentlyPlaying["isAd"]) : ?><?= mb_strtoupper(str_replace("_", "", str_replace(".mp4", "", $currentlyPlaying["show_name"]))) ?><?php else : ?>Werbung<?php endif ?></div></td>
                    <td align="right" style="font-size: 29px;color:orange;border-color:orange" valign="center" width="22%"><?php if (!$currentlyPlaying["isAd"]) : ?><?= date("d.m H:i", strtotime($currentlyPlaying["playTime"])) ?><?php endif ?></td>
                </tr>
            <?php endif ?>
            <?php if ($Queue) : ?>
            <?php foreach ($Queue as $q) : ?>
                <tr>
                    <td valign="center" width="78%"><div class="teletext-title"><?= mb_strtoupper(str_replace("_", "", str_replace(".mp4", "", $q["show_name"]))) ?></div></td>
                    <td align="right" style="font-size: 29px;" valign="center" width="22%"><?= date("d.m H:i", strtotime($q["playTime"])) ?></td>
                </tr>
            <?php endforeach ?>
            <?php endif ?>
        </table>
    </div>
</div>
<?php else : ?>
    <div id="teletext-top">
        <div>
            Kanal <?= $_POST["channel"] ?>
        </div>
        <div>
            <div id="teletext-date"></div>
        </div>
    </div>
    <div id="teletext-middle">
        Leerer Kanal
    </div>
    <div id="teletext-bottom">
        <div><strong>STUNDENPLAN</strong>:</div>
        <div id="teletext-plan">
        </div>
    </div>
<?php endif ?>
