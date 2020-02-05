<?php

$user_id = 1;

$conn = mysqli_connect(
    '127.0.0.1',
    'homefit',
    '111111',
    'homefit'
);

$date = htmlspecialchars($_POST['date']);

$count = htmlspecialchars($_POST['count']);

for ($i = 0; $i < $count; $i++)
    $filtered = array_push($filtered, array(array(4)));

for ($i = 0; $i < $count; $i++){

    $filtered[$i]['update_id'] = htmlspecialchars($_POST['calender_id'][$i]);
    $filtered[$i]['exercise'] = htmlspecialchars($_POST['exercise'][$i]);
    $filtered[$i]['sets'] = htmlspecialchars($_POST['sets'][$i]);
    $filtered[$i]['reps'] = htmlspecialchars($_POST['reps'][$i]);

}

for ($i = 0; $i < $count; $i++){

    $sql_update  = array();

    $flag = false;

    $sql = "UPDATE calender
            SET ";

    if(!empty($filtered[$i]['exercise'])){
        array_push($sql_update, "exercise = {$filtered[$i]['exercise']}");
        $flag = true;
    }

    if(!empty($filtered[$i]['sets'])){
        array_push($sql_update, "sets = {$filtered[$i]['sets']}");
        $flag = true;
    }

    if(!empty($filtered[$i]['reps'])){
        array_push($sql_update, "reps = {$filtered[$i]['reps']}");
        $flag = true;
    }

    for ($j = 0; $j < count($sql_update); $j++){
        $sql .= $sql_update[$j];

        if ($j < count($sql_update)-1){
            $sql .= ",";
        }
    }

    if ($flag){
        $sql .= " WHERE calender_id = {$filtered[$i]['update_id']}";
        mysqli_query($conn, $sql);
    }

}

if(isset($_GET['delete'])) {

    if ($_GET['delete'] == 0) {

        $sql = "INSERT INTO calender (user, date, exercise, sets, reps, attendance)
        VALUES ({$user_id}, '{$date}', 1, 5, 10, 1)";

        mysqli_query($conn, $sql);

    }else {

        $sql = "DELETE FROM calender WHERE calender_id={$_GET['delete']}";

        mysqli_query($conn, $sql);

    }

}

$prevPage = $_SERVER['HTTP_REFERER'];

header('location:'.$prevPage);

?>

