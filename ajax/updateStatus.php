<?php
    require "../includes/init.php";

    if ($_POST["muted"] == "true") $_POST["muted"] = 1;
    else                           $_POST["muted"] = 0;

    $_DB->update("laststates", ["lastMuted" => (int)$_POST["muted"], "lastVolume" => $_POST["volume"], "lastChannel" => $_POST["channel"]], ["id" => 1]);