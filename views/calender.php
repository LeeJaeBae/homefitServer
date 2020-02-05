<?php

$user_id = 1;

$conn = mysqli_connect(
    '127.0.0.1',
    'homefit',
    '111111',
    'homefit'
);

?>

<?php

function get_year(){
    if (isset($_GET['year'])){
        return $_GET['year'];
    }else {
        return date('Y');
    }
}

function get_month(){
    if (isset($_GET['month'])){
        return $_GET['month'];
    }else {
        return date('m');
    }
}

function get_day(){
    if (isset($_GET['day']))
        return $_GET['day'];
    else
        return date('d');
}

function get_direct_day($date, $direct){

    $timestamp = strtotime($date.$direct);

    $year  = date("Y", $timestamp);
    $month = date("m", $timestamp);
    $day   = date("d", $timestamp);

    return "calender.php?year={$year}&month={$month}&day={$day}";

}

function print_date($date){

    $timestamp = strtotime($date);

    echo date("Y년 m월 d일", $timestamp);

}

?>

<?php
// 년, 월, 일(기본값: 오늘)
$year  = htmlspecialchars(get_year());
$month = htmlspecialchars(get_month());
$day   = htmlspecialchars(get_day());

$today      = date("Y-m-d");                // 오늘 날짜("년-월-일")
$first_day  = strtotime("$year-$month-1");
$first_week = date("w", $first_day);        // 이번달의 시작 요일
$total_days = date("t", $first_day);        // 이번달의 총 일수

$total_weeks = ceil(($first_week+$total_days)/7); // 이번달의 총 주차


$last_month   = $month-1;                                   // 지난달
$last_end_day = date("t", "$last_month");   // 지난달의 총 일수

$this_day = 1;                                              // 달력에 표시할 일

$selected_day = "{$year}-{$month}-{$day}";                  // 달력에서 선택된 날짜

$last_days = $total_days-$day;                              // 다음달까지의 남은 일수

?>

<?php

// 현재달의 출석정보
$sql = "SELECT 
        user, date, calender_id, MAX(attendance) AS attendance 
        FROM 
        calender 
        WHERE user= '{$user_id}' AND date LIKE \"$year-{$month}-%\"
        GROUP BY date
        ORDER BY date";

$result = mysqli_query($conn, $sql);

//
$this_month_attendance = array();

$max = 0;

$update_attendance = array();

$count = 0;

while($row = mysqli_fetch_array($result)){

    if ($today > $row['date'] && $row['attendance'] == 1){
        array_push($update_attendance, $row['calender_id']);
        $count++;
    }

    $filtered = array(
        'user_id'       => htmlspecialchars($row['user_id']),
        'date'          => htmlspecialchars($row['date']),
        'attendance'    => htmlspecialchars($row['attendance'])
    );

    array_push($this_month_attendance, $filtered['date'], $filtered['attendance']);

    $max+=2;

}

// 지난 계획 결석 처리
for ($i = 0; $i < $count; $i++){

    $sql = "UPDATE calender
        SET attendance = attendance+1
        WHERE calender_id = {$update_attendance[$i]}";

    mysqli_query($conn, $sql);

}

?>

<?php

$result = mysqli_query($conn, "SELECT * FROM exercise");

$exercise = array();

while ($row = mysqli_fetch_array($result)) {

    array_push($exercise,

        array(

            'exercise_id' => $row['exercise_id'],
            'name'        => $row['name']

        )

    );

}

$input_window = "";

$today_plan      = "";

$update_data     = "";

$calender_id = array();

$count = 0;

$sql = "SELECT calender_id, user, exercise, sets, reps, name
        FROM calender
        calender LEFT JOIN exercise ON exercise = exercise_id
        WHERE user = '{$user_id}' AND date = '{$selected_day}'
        ORDER BY calender_id";

$result = mysqli_query($conn, $sql);

$input_window = "<div class='form'><form id='input_window' action='calender_process.php' method='POST'>
                    <input type='hidden' name='date' value='{$selected_day}'>";

if ($today <= $selected_day) {

    if (0 < mysqli_num_rows($result)) {

        while ($row = mysqli_fetch_array($result)) {

            $selected_exercise = "";
            $lest_exercise     = "";

            $input_window .=
                "<input type='hidden' name='calender_id[]' value='{$row['calender_id']}'>
                <p><select name='exercise[]'> ";

            for ($i = 0; $i < count($exercise); $i++) {

                if ($row['exercise'] == $exercise[$i]['exercise_id']){
                    $selected_exercise .= "<option value=''>{$exercise[$i]['name']}</option>";
                }else{
                    $lest_exercise .= "<option value='{$exercise[$i]['exercise_id']}'>{$exercise[$i]['name']}</option>";
                }

            }
            $input_window .= $selected_exercise;
            $input_window .= $lest_exercise;

            $input_window .= "</select>
            <input class='inputted_value' type='text' name='reps[]' value='{$row['reps']}'>
            <input class='inputted_value' type='text' name='sets[]' value='{$row['sets']}'>
            </p>";

            array_push($calender_id, $row['calender_id']);

            $count++;

        }
    }
    $input_window .= "<input type='hidden' name='count' value='{$count}'>
                    </div><div class='form'>";

    for ($i = 0; $i < $count; $i++) {
        $input_window .= "<p><input type='submit' value='삭제' formaction='calender_process.php?delete={$calender_id[$i]}'></p>";
    }
    $input_window .= "</div><input type='submit' value='추가' formaction='calender_process.php?delete=0'>";

    if (0 < mysqli_num_rows($result)) {
        $input_window .=
            "<div><input type='submit' value='저장'></div></form>";
    }

}

?>

<!

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
    <script>
        // 값 전송시 value속성 삭제
        $('#input_window').submit(function () {
            $('.inputted_value').removeAttribute('value');
        })
        // 선택된 날짜 표시
        $(function () {
            $("#selected_day").closest("td").css("background","aqua");
        })
    </script>
    <style>
        /*임시 스타일*/
        a{color: black; text-decoration: none }
        a:link {color: black; text-decoration: none;}
        a:visited { color: black; text-decoration: none;}
        a:hover { color: black; text-decoration: none;}
            .form{
            float: left;
        }
        td{
            width: 60px;
            height: 60px;
        }
        #r{
            color: red;
        }
        #o{
            color: orange;
    </style>
</head>
<body>

<div>
    <a href="<?=get_direct_day($selected_day, '-1 months')?>">지난달</a>
    <a href="<?=get_direct_day($selected_day, '-1 days')?>">지난날</a>
    <strong><<?= print_date($selected_day) ?>></strong>
    <a href="<?=get_direct_day($selected_day, '+1 days')?>">다음날</a>
    <a href="<?=get_direct_day($selected_day, '+1 months')?>">다음달</a>
</div>


<table id="calender" border="1">
    <tr>
        <th>일</th>
        <th>월</th>
        <th>화</th>
        <th>수</th>
        <th>목</th>
        <th>금</th>
        <th>토</th>
    </tr>

    <?php for ($i = 0; $i < $total_weeks; $i++): ?>
        <tr>
            <?php for ($j = 0; $j < 7; $j++): ?>
                <!-- selected -->
                <td>
                    <?php if(($i == 0 && $j > $first_week-1) || ($i > 0 && $this_day <= $total_days)): ?>
                        <a
                            <?php
                            if ($day == $this_day){
                                echo "id=\"selected_day\"";
                            }
                            ?>
                            href="calender.php?year=<?=$year ?>&month=<?=$month ?>&day=<?= sprintf('%02d', $this_day) ?>">
                        <?php
                            for ($k = 0; $k < $max; $k+=2){

                                if ($this_month_attendance[$k] == $year."-".$month."-".sprintf('%02d', $this_day) ) {

                                    if ($this_month_attendance[$k+1] == 1)
                                        echo "<p>계획</p>";
                                    elseif ($this_month_attendance[$k+1] == 2)
                                        echo "<p id='r'>결석</p>";
                                    elseif ($this_month_attendance[$k+1] == 3)
                                        echo "<p id='o'>출석</p>";
                                    elseif ($this_month_attendance[$k+1] == 4)
                                        echo "<p>미흡</p>";
                                    elseif ($this_month_attendance[$k+1] == 5)
                                        echo "<p>달성</p>";
                                }

                            }
                        echo $this_day++;
                        ?>
                        </a>
                    <?php endif; ?>
                </td>
            <?php endfor; ?>
        </tr>
    <?php endfor; ?>
</table>


<?= $input_window ?>

</body>
</html>