<!doctype html>
<?php require 'profile_script.php' ;?>
<html lang="en">
<head>

    <link rel="stylesheet" href="css/profile.css">

    <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="css/style.css">

    <style type="text/css">
        #pic {
            float: left;
            padding-right: 20px;
        }
        #text {
            margin-left: 110px;
        }
    </style>
</head>
<body>
<section class="ftco-section">
    <div class="container">
<div id="pic">
    <img src="<?php echo addslashes($avatarUrl) ?>" align="top" alt="Фото"  style = "height: 450px!important; width: 420px!important;">
</div>
<div id="text" align="left" style="height: 450px!important;">
    <?php
    echo 'Имя: ' . $name . '<br/>';
    echo 'Логин: '. $login . '<br/>';
    echo 'Фолловеры: ' . $followersTotal . '<br/>';
    echo 'Следит за : ' . $followingTotal . '<br/>';
    echo 'Всего stargazers у репозиториев: ' . $stargazerCount . '<br/>';
    echo 'Статус: ' . $status . '<br/>';
    echo 'Вебсайт: <a href = "' . $websiteUrl .'">' . $websiteUrl . '</a><br/>';
    echo 'Био: ' . $bio . '<br/>';
    echo 'Компания: ' . $company . '<br/>';
    echo 'Электронная почта: '. $email . '<br/>';
    echo 'Твиттер: ' . $twitterUsername . '<br/><br/>';

    createLanguagesTop();
    ?>
</div>

<div class="border py-2 graph-before-activity-overview" style="position: absolute; margin-top: 30px">
<div class="js-calendar-graph mx-md-2 mx-3 d-flex flex-column flex-items-end flex-xl-items-center overflow-hidden pt-1 is-graph-loading graph-canvas ContributionCalendar height-full text-center">
    <svg width="828" height="128" class="js-calendar-graph-svg">
        <g transform="translate(10, 20)">
            <?php createTable()?>
        </g>
    </svg>
</div>
    <?php echo 'Всего действий: ' .$totalContributions?>

</div>
    </div>
</section>
</body>
</html>
