<?php
    require "includes/init.php";

    if (isset($_POST["season"])) {
        $CHANNEL = $_DB->query("SELECT channelName FROM channels WHERE channel = :CHANNEL", true, [":CHANNEL" => $_POST["channel"]])["channelName"];

        if (isset($_POST["isAd"])) $isAd = 1;
        else                       $isAd = 0;

        foreach ($_FILES["videoFile"]["tmp_name"] as $key => $uploadFile) {
            $fileName = generateRandomString(30);

            $location = ROOT."/static/videos/$fileName.mp4";
            if (move_uploaded_file($uploadFile, $location)) {
                if (empty($_POST["showName"])) $showTitle = $_FILES["videoFile"]["name"][$key];
                else                           $showTitle = $_POST["showName"];
                $showTitle = trim(str_replace("German dTV iND", "", str_replace("StreamZZto", "", str_replace("yt5s.com-", "", str_replace("Y2Mate.is - ", "", str_replace("(360p)", "", str_replace("(480p)", "",  str_replace("(240p)", "", $showTitle))))))));


                $duration = ceil(shell_exec(FFPROBE." -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $location 2>&1"));

                $_DB->insert("shows", [
                    "season_id"         => $_POST["season"],
                    "fileName"          => $fileName,
                    "show_name"         => $showTitle,
                    "duration"          => $duration,
                    "isAd"              => $isAd
                ]);
            } else {
                //header("location: /upload.php?error"); die();
            }
        }
        header("location: /upload.php?success"); die();


    }
    $CHANNELS = $_DB->query("SELECT channel, channelName FROM channels ORDER BY channel ASC");
    $SEASONS = $_DB->query("SELECT * FROM seasons INNER JOIN series ON seasons.series_id = series.series_id ORDER BY series_name ASC, season_number ASC");
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Upload Show</title>
        <style>
            input[type="text"], textarea, select {
                width: 30%;
            }
            label * {
                vertical-align: middle;
            }
            div {
                margin-bottom: 10px
            }
        </style>
    </head>
    <body oncontextmenu="return false">
        <?php if (isset($_GET["success"])) : ?>
        <div style="font-weight:bold;font-size:16px;color:green">Success!</div>
        <?php endif ?>
        <?php if (isset($_GET["error"])) : ?>
            <div style="font-weight:bold;font-size:16px;color:red">Error!</div>
        <?php endif ?>
        <form action="/upload.php" method="POST" enctype="multipart/form-data">
            <div>
                <label>Video File: <input type="file" name="videoFile[]" multiple accept="video/mp4"></label>
            </div>
            <div>
                <label>
                    Show/Season: <select name="season">
                        <?php foreach ($SEASONS as $SEASON) : ?>
                            <option value="<?= $SEASON["season_id"] ?>"><?= $SEASON["series_name"] ?> - Season <?= $SEASON["season_number"] ?></option>
                        <?php endforeach ?>
                    </select>
                </label>
            </div>
            <div>
                <label>Show Name: <input type="text" name="showName"></label>
            </div>
            <div>
                <label>
                    Show Description: <textarea rows="4" cols="66" name="showDescription"></textarea>
                </label>
            </div>
            <div>
                <label>Is AD: <input type="checkbox" name="isAd"></label>
            </div>
            <div>
                <input type="submit" value="Upload">
            </div>
        </form>
    </body>
</html>