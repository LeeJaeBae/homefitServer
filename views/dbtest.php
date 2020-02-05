<?php
$conn = mysqli_connect(
    '127.0.0.1',
    'homefit',
    '111111',
    'homefit'
);
if($conn){
    echo "success";
} else {
    echo "failed";
}

?>
