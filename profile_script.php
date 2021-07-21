<?php
if (!extension_loaded('cURL')) // если по какой-то причине не включается библиотека cURL
    dl('cURL');

$login = $_GET['login'];

$dataArray = getData($login);

if ($dataArray != null) {
    $dataArray = json_decode($dataArray, true); // делаем из строки ассоциативный массив
    if ($dataArray['data']['user'] != null) {
        /* передаем значения из массива в отдельные переменные
         * так будет проще и понятнее в основном файле
         */
        $totalContributions = 0;
        $name = $dataArray['data']['user']['name'];
        $avatarUrl = $dataArray['data']['user']['avatarUrl'];
        $websiteUrl = $dataArray['data']['user']['websiteUrl'];
        $bio = $dataArray['data']['user']['bio'];
        $company = $dataArray['data']['user']['company'];
        $email = $dataArray['data']['user']['email'];
        $twitterUsername = $dataArray['data']['user']['twitterUsername'];
        $followersTotal = $dataArray['data']['user']['followers']['totalCount'];
        $followingTotal = $dataArray['data']['user']['following']['totalCount'];
        if ($dataArray['data']['user']['status'] != null)
            $status = $dataArray['data']['user']['status']['message'];
        else
            $status = "Пусто";
        $stargazerCount = 0;

        $language = array();

        // этот цикл определяет ассоциативный массив с языками, с которыми пользователь работал
        foreach ($dataArray['data']['user']['contributionsCollection']['commitContributionsByRepository'] as $contribution) {
            foreach ($contribution['contributions']['edges'] as $repository) {
                $stargazerCount += $repository['node']['repository']['stargazerCount'];
                foreach ($repository['node']['repository']['languages']['edges'] as $languages) {
                    $language[$languages['node']['name']] = 0;
                }
            }
        }
        // в уже определенный ассоциативный массив записываем значения
        foreach ($dataArray['data']['user']['contributionsCollection']['commitContributionsByRepository'] as $contribution) {
            foreach ($contribution['contributions']['edges'] as $repository) {
                $stargazerCount += $repository['node']['repository']['stargazerCount'];
                foreach ($repository['node']['repository']['languages']['edges'] as $languages) {
                    $language[$languages['node']['name']] += $languages['size'];
                }
            }
        }
        $languageArray = array();
        // добавляем значения в обычный массив - для будущей сортировки
        for ($i = 0; current($language) != null; $i++) {

            $languageArray[$i]['name'] = key($language);
            $languageArray[$i]['count'] = $language[key($language)];
            next($language);
        }
        // сортируем массив
        for ($i = 0; $i < count($languageArray) - 1; $i++) {
            for ($j = 0; $j < count($languageArray) - 1 - $i; $j++) {
                if ($languageArray[$j]['count'] < $languageArray[$j + 1]['count']) {
                    $tmp = $languageArray[$j];
                    $languageArray[$j] = $languageArray[$j + 1];
                    $languageArray[$j + 1] = $tmp;
                }
            }
        }



    }
}


function createLanguagesTop() // создает список с самыми используемыми языками у пользователя
{

    global $languageArray;
    echo "Топ языков пользователя:<br/>";
    for ($i = 0; $i < 3 && $i < count($languageArray); $i++) {
        echo $languageArray[$i]['name'] . " - " . $languageArray[$i]['count'] . " байтов<br/>";
    }
}


function getData($login): bool|string // возвращает строку данных от гитхаба или false - если не получилось соединиться
{

    $url = "https://api.github.com/graphql";

    if ($ch = curl_init($url)) {

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36");
        $authorization = "Authorization: bearer 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";
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $query =  '{ "query": "{  user (login: \"' . $login . '\" ) { name login avatarUrl websiteUrl bio company createdAt email twitterUsername followers { totalCount } following { totalCount } status { message } contributionsCollection { contributionCalendar { weeks { contributionDays { color date contributionCount } } } totalCommitContributions commitContributionsByRepository { contributions (first: 100) { edges { node { repository { stargazerCount languages (first: 100) { edges { node { name } size } } } } } } } } } }" }';

        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

        $dataArray = curl_exec($ch);

        curl_close($ch);

        return $dataArray;
    }
    return false;

}
function createTable () { // создает график с количеством действий в каждый день за последний год
    global $totalContributions;
    global $dataArray;
    $contributionWeeks = $dataArray['data']['user']['contributionsCollection']['contributionCalendar']['weeks'];

    // переменные для позиций каждого отдельного дня в графе действий
    $transformParameter = 0;
    $x = 16;
    $y = 15;

    // цикл, создающий графу действий
    for ($i = 0; $i < 52; $i++) {
        echo '<g transform="translate('. $transformParameter + 16 * $i .', 0)">';
        for ($j = 0; $j < 7; $j++) {
            // попутно считаем общее кол-во действий
            $totalContributions +=$contributionWeeks[$i]['contributionDays'][$j]['contributionCount'];
            echo '<rect width="11" height="11" x="'. $x - $i .'" y="'. $y * $j .
                '" class="ContributionCalendar-day" rx="2" ry="2"
                     style=" fill:'. $contributionWeeks[$i]['contributionDays'][$j]['color'] . '">
                     <title>Дата:' . $contributionWeeks[$i]['contributionDays'][$j]['date'] . '
                     Количество действий:' . $contributionWeeks[$i]['contributionDays'][$j]['contributionCount'] . '</title> </rect>';
        }
        echo '</g>';
    }
    echo '<g transform="translate('. $transformParameter + 16 * 52 .', 0)">';

    // в последней неделе может быть меньше 7 дней
    for ($j = 0; $j < count($contributionWeeks[52]['contributionDays']); $j++) {
        $totalContributions +=$contributionWeeks[52]['contributionDays'][$j]['contributionCount'];
        echo '<rect width="11" height="11" x="'. $x - 52 .'" y="'. $y * $j .
            '" class="ContributionCalendar-day" rx="2" ry="2"
                     style=" fill:'. $contributionWeeks[52]['contributionDays'][$j]['color'] . '">
                     <title>Дата:' . $contributionWeeks[52]['contributionDays'][$j]['date'] . '
                     Количество действий:' . $contributionWeeks[52]['contributionDays'][$j]['contributionCount'] . '</title> </rect>';
    }
    echo '</g>';
}

