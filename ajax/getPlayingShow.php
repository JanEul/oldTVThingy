<?php
    require "../includes/init.php";

    header('Content-Type: application/json; charset=utf-8');

    $show = $_DB->query("SELECT shows.fileName, queue.playTime FROM queue INNER JOIN shows ON shows.show_id = queue.show_id WHERE queue.playTime <= NOW() AND channel_id = :CHANNEL ORDER BY queue.queue_id DESC LIMIT 1", true, [":CHANNEL" => $_POST["channel"]]);


    if ($show) die(json_encode(["fileName" => $show["fileName"], "playTime" => time() - strtotime($show["playTime"])]));
    else die(json_encode(["fileName" => "none", "playTime" => 0]));