<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require "../includes/init.php";
    set_time_limit(0);
    ignore_user_abort(1);
    ini_set('memory_limit', '8000M');

    if (isset($_GET["days"])) $days = $_GET["days"];
    else                      $days = 3;

    if (!isset($_GET["channel"])) generateQueue();
    else                          generateQueue($_GET["channel"], $days);


    if (!isset($_GET["channel"])) $Queue = $_DB->query("SELECT * FROM queue INNER JOIN shows ON queue.show_id = shows.show_id WHERE channel_id = 3 ORDER BY playTime ASC");
    else $Queue = $_DB->query("SELECT * FROM queue INNER JOIN shows ON queue.show_id = shows.show_id WHERE channel_id = :ID ORDER BY playTime ASC", false, [":ID" => $_GET["channel"]]);
    ?>
<table>
    <tr>
        <td>Name</td>
        <td>Zeit</td>
    </tr>
    <?php foreach ($Queue as $show) : ?>
    <tr>
        <td><?= $show["show_name"] ?></td>
        <td>
            <?php if (!SUBTRACT_YEARS) : ?>
                <?= date("d.m.Y H:i:s", strtotime($show["playTime"])) ?>
            <?php else : ?>
                <?= date("d.m.Y H:i:s", strtotime("-".SUBTRACT_YEARS." year", strtotime($show["playTime"]))) ?>
            <?php endif ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>
