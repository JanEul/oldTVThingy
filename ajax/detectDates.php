<?php
    require "../includes/init.php";
    set_time_limit(0);
    ignore_user_abort(1);
    ini_set('memory_limit', '8000M');

    $shows = $_DB->query("SELECT * FROM shows");

    foreach ($shows as $show) {
        preg_match('/(\d{2}\.\d{2}\.\d{4})/', $show["show_name"], $date);
        if ($date) {
            $_DB->update("shows", ["releaseDate" => date("Y-m-d 00:00:00", strtotime($date[0]))], ["show_id" => $show["show_id"]]);
        }
    }