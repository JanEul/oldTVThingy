<?php
function inStr($needle, $haystack) {
    return strpos($haystack, $needle) !== false;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateQueue($channel = null, $days = 5) {
    global $_DB;

    $TIMELIMIT          = time() + (86400 * $days);
    $STARTTIME          = time();
    $FAKETIME           = strtotime("-".SUBTRACT_YEARS." year");
    $CURRENTTIME        = time();
    $LASTEPISODE        = [];
    $SEASONID           = 0;
    $ADDSQL             = "";
    $DATECHECKPOINT     = date("Y-m-d H:i:s", time());
    $SERIES_EPISODE_COUNT = [];
    $DATECHECKPOINTSQL  = "";
    $ORDERBY = "ORDER BY rand()";
    $FAKEDATESQL        = "";
    if (!isset($channel))   {
        $_DB->query("DELETE FROM queue");
        $channels = $_DB->query("SELECT channel, adsAmount, hasAds, ident_season FROM channels ORDER BY channel ASC");
    } else {
        $_DB->query("DELETE FROM queue WHERE channel_id = $channel");
        $channels = $_DB->query("SELECT channel, adsAmount, hasAds, ident_season FROM channels WHERE channel = :CHANNEL", false, [":CHANNEL" => $channel]);
    }

    foreach ($channels as $channel) {
        $channelArray       = $channel;
        $channel            = $channel["channel"];
        $SEASONID           = 0;
        $LASTEPISODE        = [];
        $CURRENTTIME        = $STARTTIME;
        $CURRENTFAKETIME    = $FAKETIME;
        $SERIES_SHOWN_EPSIODE_COUNT = [];
        $ADDSQL             = "";
        $GETEPISODESQL      = "";
        $AD_ALTERNATE       = 0;
        $FOUND_SHOW         = false;
        $ADVERTISMENTCOUNT  = 0;
        $ADVERTISMENTSQL    = "";
        $DATECHECKPOINTSQL  = "";
        $FAKEDATESQL        = "";
        while ($CURRENTTIME < $TIMELIMIT) {
            if (ONLY_TIME_SPECIFIC_SHOWS) $FAKEDATESQL      = " AND ((shows.releaseDate <= '".date("Y-m-d H:i:s", $CURRENTFAKETIME)."' OR shows.releaseDate IS NULL) AND (shows.endDate >= '".date("Y-m-d H:i:s", $CURRENTFAKETIME)."' OR shows.endDate IS NULL)) ";
            else                          $FAKEDATESQL      = "";

            if ($SEASONID && empty($ADDSQL)) {
                $SEASONS    = $_DB->query("SELECT season_id FROM seasons WHERE series_id = ".$SEASONID);
                $ADDSQL     = " AND (";
                foreach ($SEASONS as $KEY => $SEASON) {
                    $ADDSQL .= "shows.season_id = ".$SEASON["season_id"];
                    if (isset($SEASONS[$KEY + 1])) $ADDSQL .= " OR ";
                }
                $ADDSQL .= ")";
            } else if ($SEASONID == 0) $ADDSQL = "";


            // ADVERTISNMENTS
            if ($channelArray["hasAds"]) {
                if ($AD_ALTERNATE && $FOUND_SHOW) {
                    if ($ADVERTISMENTCOUNT <= $channelArray["adsAmount"]) {
                        $ADVERTISMENTSQL = "AND shows.isAd = 1";
                        if (isset($channelArray["ident_season"]) && ($ADVERTISMENTCOUNT == 0 || $ADVERTISMENTCOUNT == $channelArray["adsAmount"])) {
                            if      ($ADVERTISMENTCOUNT == 0)                           $ADVERTISMENTSQL .= " AND shows.adType = 1 ";
                            else if ($ADVERTISMENTCOUNT == $channelArray["adsAmount"])  $ADVERTISMENTSQL .= " AND shows.adType = 2 ";
                            $ADVERTISMENTSQL .= " AND channels_seasons.cs_id = ".$channelArray["ident_season"];
                        } else {
                            $ADVERTISMENTSQL .= " AND shows.adType = 0 "; $AD_ALTERNATE = 1;
                        }
                        $ADVERTISMENTCOUNT++;
                    } else {
                        $FOUND_SHOW         = false;
                        $ADVERTISMENTCOUNT  = 0;
                        $AD_ALTERNATE       = 0;
                        $ADVERTISMENTSQL    = "AND shows.isAd = 0"; $AD_ALTERNATE = 1;
                    }
                } else { $ADVERTISMENTSQL = "AND shows.isAd = 0"; $AD_ALTERNATE = 1; }
            } else $ADVERTISMENTSQL = "AND shows.isAd = 0";

            if ($ADVERTISMENTSQL == "AND shows.isAd = 0") $show = $_DB->query("SELECT shows.episode, shows.duration, shows.show_id, queue.queue_id, seasons.series_id, shows.isAd, shows.season_id FROM channels_seasons INNER JOIN shows ON channels_seasons.season_id = shows.season_id INNER JOIN seasons ON seasons.season_id = shows.season_id LEFT JOIN queue ON queue.show_id = shows.show_id AND queue.channel_id = $channel $DATECHECKPOINTSQL WHERE channels_seasons.channel_id = $channel $FAKEDATESQL $GETEPISODESQL $ADVERTISMENTSQL AND queue.queue_id IS NULL $ADDSQL $ORDERBY LIMIT 1", true);
            else $show = $_DB->query("SELECT shows.episode, shows.duration, shows.show_id, queue.queue_id, seasons.series_id, shows.isAd, shows.season_id FROM channels_seasons INNER JOIN shows ON channels_seasons.season_id = shows.season_id INNER JOIN seasons ON seasons.season_id = shows.season_id LEFT JOIN queue ON queue.show_id = shows.show_id AND queue.channel_id = $channel AND queue.playTime >= '".date("Y-m-d H:i:s", $CURRENTTIME - 180)."' WHERE channels_seasons.channel_id = $channel AND queue.queue_id IS NULL $FAKEDATESQL $ADVERTISMENTSQL ORDER BY rand() LIMIT 1", true);
            //file_put_contents("debug.txt", file_get_contents("debug.txt").PHP_EOL."SELECT shows.episode, shows.duration, shows.show_id, queue.queue_id, seasons.series_id, shows.isAd, shows.season_id FROM channels_seasons INNER JOIN shows ON channels_seasons.season_id = shows.season_id INNER JOIN seasons ON seasons.season_id = shows.season_id LEFT JOIN queue ON queue.show_id = shows.show_id AND queue.channel_id = $channel $DATECHECKPOINTSQL WHERE channels_seasons.channel_id = $channel $FAKEDATESQL $GETEPISODESQL $ADVERTISMENTSQL AND queue.queue_id IS NULL $ADDSQL $ORDERBY LIMIT 1");
            if ($show) {
                $SEASONID = $show["series_id"];
                if (
                    (!isset($show["episode"]))
                    ||  ($show["isAd"])
                    ||  ($show["episode"] == 1 && !isset($LASTEPISODE[$show["series_id"]]))
                    ||  (isset($LASTEPISODE[$show["series_id"]]) && $show["episode"] != 1 && in_array($show["episode"] - 1, $LASTEPISODE[$show["series_id"]]))
                ) {
                    $ORDERBY = "ORDER BY rand()";
                    $GETEPISODESQL = "";

                    $SEASONID = 0;
                    if (isset($show["episode"])) $LASTEPISODE[$show["series_id"]][] = $show["episode"];
                    $FOUND_SHOW = true;
                    $_DB->insert("queue", [
                        "channel_id"    => $channel,
                        "show_id"       => $show["show_id"],
                        "playTime"      => date("Y-m-d H:i:s", $CURRENTTIME)
                    ]);
                    $CURRENTTIME        += $show["duration"];
                    $CURRENTFAKETIME    += $show["duration"];
                } elseif (isset($LASTEPISODE[$show["series_id"]]) && $show["episode"] != 1 && !in_array($show["episode"] - 1, $LASTEPISODE[$show["series_id"]])) {
                    //die($show["show_name"]." ".$show["series_id"]." ".$show["episode"]);
                    $ORDERBY = "";
                    $SEASONID = 0;
                    $GETEPISODESQL = " AND seasons.series_id = ".$show["series_id"]." AND shows.episode = ".count($LASTEPISODE[$show["series_id"]]) + 1;
                }
            } else if ($ADVERTISMENTSQL == "AND shows.isAd = 0") {
                    $ORDERBY = "ORDER BY rand()";
                    if ($LASTEPISODE) {
                        foreach ($LASTEPISODE as $sh => $L) {
                            $check = $_DB->query("SELECT max(shows.episode) as m FROM shows INNER JOIN seasons ON seasons.season_id = shows.season_id WHERE seasons.series_id = :SERIES $FAKEDATESQL", true, [":SERIES" => $sh])["m"];
                            if ($check == count($L)) {
                                unset($LASTEPISODE[$sh]);
                            }
                        }
                    }
                    //die("SELECT shows.episode, shows.duration, shows.show_id, queue.queue_id, seasons.series_id, shows.isAd, shows.season_id FROM channels_seasons INNER JOIN shows ON channels_seasons.season_id = shows.season_id INNER JOIN seasons ON seasons.season_id = shows.season_id LEFT JOIN queue ON queue.show_id = shows.show_id AND queue.channel_id = $channel $DATECHECKPOINTSQL WHERE channels_seasons.channel_id = $channel $FAKEDATESQL $GETEPISODESQL $ADVERTISMENTSQL AND queue.queue_id IS NULL $ADDSQL $ORDERBY LIMIT 1");
                    $GETEPISODESQL = "";
                    $DATECHECKPOINT = date("Y-m-d H:i:s", $CURRENTTIME);
                    $DATECHECKPOINTSQL = " AND (queue.playTime >= '$DATECHECKPOINT')";
                    $SEASONID = 0;
                    $ADDSQL = "";
            }
        }
    }
}