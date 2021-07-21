<?php

$dataArray = array();

$tmp = parse_ini_file("variables.ini");
$i = 0;
foreach ($tmp as $login) {
    $dataArray[$i] = json_decode(getData($login), true);
    $i++;
    if ($i == 5) break;
}

$timeMonthAgo = new DateTime("now"); // сегодняшняя дата
$timeMonthAgo->modify('-1 month'); // дата 30 дней назад


for ($k = 0; $k < 5; $k++) { // для каждого полученного массива
    $dataArray[$k]['commitCount'] = 0;
    /* чтобы точно подсчитать кол-во коммитов за последние 30 дней,
       возьмем последние 6 недель  */
    for ($i = 47; $i < 52; $i++) {
        for ($j = 0; $j < 7; $j++) {
            $date = new DateTime($dataArray[$k]['data']['user']['contributionsCollection']['contributionCalendar']['weeks'][$i]['contributionDays'][$j]['date']);
            /* вычисляем разницу во времени между датой 30 дней назад
             и датой коммита */
            $interval = date_diff($timeMonthAgo, $date);
            if (str_contains($interval->format('%R%a days'), '+')) { // если дата коммита была позже
                $dataArray[$k]['commitCount'] +=
                    $dataArray[$k]['data']['user']['contributionsCollection']['contributionCalendar']['weeks'][$i]['contributionDays'][$j]['contributionCount'];

            }
        }
    }
    // последнюю неделю рассматриваем отдельно - в ней может быть меньше 7 дней
    for ($j = 0; $j < count($dataArray[$k]['data']['user']['contributionsCollection']['contributionCalendar']['weeks'][$i]['contributionDays']); $j++) {
        $dataArray[$k]['commitCount'] +=
            $dataArray[$k]['data']['user']['contributionsCollection']['contributionCalendar']['weeks'][$i]['contributionDays'][$j]['contributionCount'];
    }
}
// сортируем пользователей по кол-ву коммитов
for ($i = 0; $i < 5; $i++) {
    for ($j = 1; $j < 5 - $i; $j++) {
        if ($dataArray[$j]['commitCount'] > $dataArray[$j-1]['commitCount']) {
            $tmp = $dataArray[$j];
            $dataArray[$j] = $dataArray[$j - 1];
            $dataArray[$j - 1] = $tmp;
        }
    }
}
// выводим отсортированных пользователей
for ($i = 0; $i < 5; $i++)
{
    echo '<tr>';
    echo '<th scope="row">'. $i + 1 .'</th>';
	echo  '<td>'. $dataArray[$i]['data']['user']['login'] .'</td>';
	echo '<td>' . '<a href="'. $dataArray[$i]['data']['user']['url'] .
        '"><img src = "images/github.146e30.svg" width = 100 height = 40> </a> 
         <a href="profile.php?login='. $dataArray[$i]['data']['user']['login'] .'">
         <img src = "images/codersrank-icon.b6b6f5.svg" width = 70 height = 40> </a> </td>';
	echo '<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp ' . $dataArray[$i]['commitCount'] . '</td>';
    echo '</tr>';
}




function getData($name): bool|string // возвращает строку данных с гитхаб или false
{
    $url = "https://api.github.com/graphql";
    global $tmp;
    if ($ch = curl_init($url)) {

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36");
        $authorization = "Authorization: bearer " . $tmp['token'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $query =  '{ "query": "{  user (login: \"' . $name . '\" ) { name login url location contributionsCollection { contributionCalendar { weeks { contributionDays { date contributionCount } } } } } } " }';

        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
    }
    return false;
}
