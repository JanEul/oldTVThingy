<?php
    require "includes/init.php";
	switch (@$_GET["page"]) {
		case "shows" :
			$PAGE = "shows";
		break;
		default :
			$PAGE = "shows";
		break;
	}

    if ($PAGE == "shows") {

        if (isset($_POST["selectedShows"], $_POST["delete"])) {
            if ($_POST["selectedShows"]) {
                foreach ($_POST["selectedShows"] as $show) {
                    $show = $_DB->query("SELECT show_id, fileName FROM shows WHERE show_id = $show", true);
                    if ($show) {
                        $_DB->query("DELETE FROM shows WHERE show_id = ".$show["show_id"]);
                        unlink("static/videos/".$show["fileName"].".mp4");
                    }
                }
            }
        }

        $SERIESSQL = "";
        if (isset($_GET["series"]) && $_GET["series"] != 0) {
            $SERIESSQL = " AND series.series_id = ".$_GET["series"];
        }
        $SEARCHSQL = "";
        if (isset($_GET["search"])) {
            $SEARCHSQL = " AND shows.show_name LIKE '%".$_GET["search"]."%'";
        }

        $shows = $_DB->query("SELECT shows.*, series.series_name FROM shows INNER JOIN seasons ON seasons.season_id = shows.season_id INNER JOIN series ON seasons.series_id = series.series_id WHERE 1 = 1 $SEARCHSQL $SERIESSQL ORDER BY shows.show_id DESC");
        $series = $_DB->query("SELECT * FROM series ORDER BY series_name ASC");
    }
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title><?= $PAGE ?></title>
        <script>
            function checkall() {
                get = document.getElementsByClassName('checker');
                for(var i=0; i<get.length; i++) {
                    if (get[i].checked) {
                        get[i].checked = false
                    } else {
                        get[i].checked = true
                    }

                }
            }
        </script>
    </head>
    <body>
        <?php if ($PAGE == "shows") : ?>
        <div>
            <form style="display:inline-block;" action="/admin.php" method="GET">
                <input type="hidden" name="page" value="shows">
                <select name="series">
                    <option value="0">All Series</option>
                    <?php foreach ($series as $serie) : ?>
                    <option value="<?= $serie["series_id"] ?>"<?php if (isset($_GET["series"]) && $_GET["series"] == $serie["series_id"]) : ?> selected<?php endif ?>><?= $serie["series_name"] ?></option>
                    <?php endforeach ?>
                </select>
                <input type="search" name="search"<?php if (isset($_GET["search"])) : ?> value="<?= $_GET["search"] ?>"<?php endif ?> placeholder="Search Term" size="30">
                <input type="submit" value="Aktualisieren">
            </form>
            <form style="display:inline-block;padding-left:22px;margin-left:18px;border-left:3px solid black" action="/admin.php" method="POST">
                <input type="submit" value="Delete Selected" name="delete">
        </div>
        <hr>
			<table border="1" style="width:100%">
				<tr>
                    <td><input type="checkbox" onclick="checkall()"></td>
					<td><strong>Series-Name</strong></td>
                    <td><strong>Show</strong></td>
                    <td><strong>Episode</strong></td>
                    <td><strong>Release</strong></td>
                    <td><strong>End</strong></td>
				</tr>
                <?php if ($shows) : ?>
                    <?php foreach ($shows as $show) : ?>
                    <tr>
                        <td><input type="checkbox" name="selectedShows[]" class="checker" value="<?= $show["show_id"] ?>"></td>
                        <td><?= $show["series_name"] ?></td>
                        <td><a href="/static/videos/<?= $show["fileName"] ?>.mp4" target="_blank"><?= $show["show_name"] ?></a></td>
                        <td><?= $show["episode"] ?></td>
                        <td><?= $show["releaseDate"] ?></td>
                        <td><?= $show["endDate"] ?></td>
                    </tr>
                    <?php endforeach ?>
                <?php endif ?>
			</table>
		<?php endif ?>
        </form>
    </body>
</html>