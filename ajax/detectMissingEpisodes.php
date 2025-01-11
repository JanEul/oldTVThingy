<?php
    require "../includes/init.php";
    set_time_limit(0);
    ignore_user_abort(1);
    ini_set('memory_limit', '8000M');

    $shows = $_DB->query("SELECT shows.season_id, min(episode) as min_episode, max(episode) as max_episode FROM shows INNER JOIN seasons ON seasons.season_id = shows.season_id INNER JOIN series ON series.series_id = seasons.series_id GROUP BY shows.season_id");

    foreach ($shows as $show) {
        if ($show["min_episode"] && $show["max_episode"]) {
            for ($x = $show["min_episode"]; $x <= $show["max_episode"]; $x++) {
                $check = $_DB->query("SELECT show_id FROM shows WHERE season_id = :SEASON AND episode = :EPISODE", true, [":SEASON" => $show["season_id"], ":EPISODE" => $x]);
                if (!$check) echo "<span style='color:red'>".$show["season_id"].": Episode $x missing</span><br>";
            }
        }
        echo $show["season_id"]." ".$show["min_episode"]." ->".$show["max_episode"]."<br>";
    }