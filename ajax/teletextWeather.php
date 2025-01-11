<?php
    require "../includes/init.php";

    if (!file_exists("../cache/".date("d.m.y H").".weather")) {
        $weather = json_decode(file_get_contents("https://api.open-meteo.com/v1/forecast?latitude=51.48&longitude=6.86&daily=weathercode,temperature_2m_max,temperature_2m_min,rain_sum,showers_sum,snowfall_sum,windspeed_10m_max&forecast_days=14&timezone=auto"), true);
        file_put_contents("../cache/".date("d.m.y H").".weather", serialize($weather));
    } else {
        $weather = unserialize(file_get_contents("../cache/".date("d.m.y H").".weather"));
    }

    $weatherCodes = [
            "0" => [
                "name" => "Klarer Himmel",
                "icon" => "#8fcc0c",
                "photo" => "sun.png"
            ],
            "1" => [
                "name" => "Fast klarer Himmel",
                "icon" => "#8fcc0c",
                "photo" => "sun.png"
            ],
            "2" => [
                "name" => "Etwas bedeckt",
                "icon" => "#5d610a",
                "photo" => "clouds.png"
            ],
            "3" => [
                "name" => "Bedeckt",
                "icon" => "#7f807e",
                "photo" => "clouds.png"
            ],
            "45" => [
                "name" => "Nebel",
                "icon" => "#474746",
                "photo" => "fog.png"
            ],
            "48" => [
                "name" => "Eis-Nebel",
                "icon" => "#474746",
                "photo" => "fog.png"
            ],
            "51" => [
                "name" => "Leichter Nieselregen",
                "icon" => "#0e67cc",
                "photo" => "rain.png"
            ],
            "53" => [
                "name" => "Nieselregen",
                "icon" => "#0e67cc",
                "photo" => "rain.png"
            ],
            "55" => [
                "name" => "Starker Nieselregen",
                "icon" => "#084e9e",
                "photo" => "rain.png"
            ],
            "56" => [
                "name" => "Leichter Nieselhagel",
                "icon" => "#bababa",
                "photo" => "rain.png"
            ],
            "57" => [
                "name" => "Starker Nieselhagel",
                "icon" => "#9e9e9e",
                "photo" => "rain.png"
            ],
            "61" => [
                "name" => "Leichter Regen",
                "icon" => "#0515f5",
                "photo" => "rain.png"
            ],
            "62" => [
                "name" => "Regen",
                "icon" => "#0210d4",
                "photo" => "rain.png"
            ],
            "63" => [
                "name" => "Starker Regen",
                "icon" => "#000cb3",
                "photo" => "rain.png"
            ],
            "66" => [
                "name" => "Hagel",
                "icon" => "#084e9e",
                "photo" => "rain.png"
            ],
            "67" => [
                "name" => "Starker Hagel",
                "icon" => "#084e9e",
                "photo" => "rain.png"
            ],
            "71" => [
                "name" => "Leichter Schneefall",
                "icon" => "#c4c4c4",
                "photo" => "rain.png"
            ],
            "73" => [
                "name" => "Schneefall",
                "icon" => "#c4c4c4",
                "photo" => "rain.png"
            ],
            "75" => [
                "name" => "Starker Schneefall",
                "icon" => "#c4c4c4",
                "photo" => "rain.png"
            ],
            "77" => [
                "name" => "Scheekörner",
                "icon" => "#c4c4c4",
                "photo" => "rain.png"
            ],
            "80" => [
                "name" => "Leichter Regensturm",
                "icon" => "#0202e5",
                "photo" => "rain.png"
            ],
            "81" => [
                "name" => "Regensturm",
                "icon" => "#0202e5",
                "photo" => "rain.png"
            ],
            "82" => [
                "name" => "Starker Regensturm",
                "icon" => "#0202e5",
                "photo" => "storm.png"
            ],
            "85" => [
                "name" => "Leichter Schneesturm",
                "icon" => "#c4c4c4",
                "photo" => "rain.png"
            ],
            "86" => [
                "name" => "Starker Schneesturm",
                "icon" => "#c4c4c4",
                "photo" => "rain.png"
            ],
            "95" => [
                "name" => "Gewitter",
                "icon" => "#6c3200",
                "photo" => "storm.png"
            ],
            "96" => [
                "name" => "Gewitter mit Hagel",
                "icon" => "#6c3200",
                "photo" => "storm-hail.png"
            ],
            "99" => [
                "name" => "Gewitter mit Hagel",
                "icon" => "#6c3200",
                "photo" => "storm-hail.png"
            ]
    ]
    ?>
<div id="teletext-top">
    <div>
        Kanal <?= $_POST["channel"] ?>
    </div>
    <div>
        <div id="teletext-date"></div>
    </div>
</div>
<div id="teletext-middle" style="background-color:#bfbf00">
    Wetter Oberhausen
</div>
<div id="teletext-bottom">
    <div style="font-size:33px;margin-bottom: 15px;"><strong>Letzte Aktualisierung <?= date("H") ?>:00 Uhr</strong>:</div>
    <div id="teletext-weather">
        <?php if ($weather) : ?>
            <?php foreach ($weather["daily"]["time"] as $key => $date) : ?><div class="teletext-weather-item" style="background-color:<?= $weatherCodes[$weather["daily"]["weathercode"][$key]]["icon"] ?>">
                <?php
                    $veryHot = false;
                    $veryCold = false;

                    if ($weather["daily"]["temperature_2m_min"][$key] > 32 || $weather["daily"]["temperature_2m_max"][$key] > 32) {
                        $veryHot = true;
                    } else if ($weather["daily"]["temperature_2m_min"][$key] < -2 || $weather["daily"]["temperature_2m_max"][$key] < -2) {
                        $veryCold = true;
                    }

                    $date = date("d.m.Y", strtotime($date));
                    $year = explode(".", $date)[2];
                    $date = str_replace($year, $year - SUBTRACT_YEARS, $date);
                ?>
                <div style="display:inline-block;vertical-align:middle;height:100%;border-right: 3px dashed rgba(255, 255, 255, 1);padding-right: 8px;width: 25%">
                    <img src="/static/img/weather/<?= $weatherCodes[$weather["daily"]["weathercode"][$key]]["photo"] ?>" style="image-rendering: pixelated;width: 120%;top: 19%;right: 9%;;position: relative;">
                </div><div style="display:inline-block;vertical-align:middle;width:calc(75% - 16px);position:relative">
                    <div style="position: relative;
    top: 50%;
    transform: translateY(-50%);">
                        <div><?= $date ?></div>
                        <div style="font-weight: bold;">
                            <?= $weatherCodes[$weather["daily"]["weathercode"][$key]]["name"] ?>
                        </div>
                        <div<?php if ($veryHot) : ?> style="background-color:red;color:white;text-decoration: underline"<?php elseif ($veryCold) : ?> style="background-color:#69ffe9;color:#000000;text-decoration: underline"<?php endif ?>><?= $weather["daily"]["temperature_2m_min"][$key] ?>C - <?= $weather["daily"]["temperature_2m_max"][$key] ?>C</div>
                        <div>W: <?= $weather["daily"]["windspeed_10m_max"][$key] ?>km/h</div>
                        <div>R: <?= $weather["daily"]["rain_sum"][$key] ?>l</div>
                    </div>
                </div>
                </div><?php endforeach ?>
        <?php endif ?>
    </div>
</div>