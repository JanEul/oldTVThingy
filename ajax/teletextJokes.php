<?php
    require "../includes/init.php";

    if (!file_exists("../cache/".date("d.m.y").".jokes")) {
        $jokes = json_decode(file_get_contents("https://v2.jokeapi.dev/joke/Any?lang=de&amount=5"), true);
        $jokes2 = json_decode(file_get_contents("https://v2.jokeapi.dev/joke/Any?lang=en&amount=5"), true);
        $allJokes = [];
        foreach ($jokes["jokes"] as $joke) {
            $allJokes[] = [
                    1 => $joke["setup"] ?? "",
                    2 => $joke["joke"] ?? "",
                    3 => $joke["delivery"] ?? ""
            ];
        }
        foreach ($jokes2["jokes"] as $joke) {
            $allJokes[] = [
                1 => $joke["setup"] ?? "",
                2 => $joke["joke"] ?? "",
                3 => $joke["delivery"] ?? ""
            ];
        }
        file_put_contents("../cache/".date("d.m.y").".jokes", serialize($allJokes));
    } else {
        $allJokes = unserialize(file_get_contents("../cache/".date("d.m.y").".jokes"));
    }
    ?>
<div id="teletext-top">
    <div>
        Kanal <?= $_POST["channel"] ?>
    </div>
    <div>
        <div id="teletext-date"></div>
    </div>
</div>
<div id="teletext-middle" style="background-color:#00bb3a">
    Tägliche Witze
</div>
<div id="teletext-bottom">
    <div id="teletext-jokes">
        <?php if ($allJokes) : ?>
            <?php foreach ($allJokes as $joke) : ?><div class="teletext-joke-item">
                <?php if (!empty($joke["2"])) : ?>
                <div style="font-weight:bold"><?= mb_strtoupper(nl2br($joke["2"])) ?></div>
                <?php else : ?>
                    <div style="font-weight:bold"><?= mb_strtoupper($joke["1"]) ?></div>
                    <div><?= mb_strtoupper(nl2br($joke["3"])) ?></div>
                <?php endif ?>
                </div><?php endforeach ?>
        <?php endif ?>
    </div>
</div>