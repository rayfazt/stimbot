<!-- Created By CodingNepal -->
<?php
// connecting to database
$conn = mysqli_connect("sql6.freesqldatabase.com", "sql6405141", "BkxHy17U62","sql6405141") or die("Database Error");

// getting user message through ajax
$getMesg = mysqli_real_escape_string($conn, $_POST['text']);
$date = "";
$KataPenting = "";
$Matkul = "";
$Topik = "";
$date = array();
if (preg_match_all("/[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/",$getMesg,$date)) {
    for ($i = 0; $i < count($date[0]); $i++){
        print_r($date[0][$i]);
        print_r("\n");
    }
    if (preg_match_all("/Tubes|Tucil|Kuis|Praktikum|UTS|UAS/",$getMesg,$KataPenting)){
        print_r($KataPenting[0][0]);
        print_r("\n");
        if (preg_match_all("/[A-Z]{2}[0-9]{4}/",$getMesg,$Matkul)){
            print_r($Matkul[0][0]);
            print_r("\n");
            if(preg_match_all("/topik(.*)|topiknya(.*)/", $getMesg,$Topik)){
                print_r($Topik[1][0]);
            }
        }
    }
} else {
    echo false;
}
/*
//checking user query to database query
$check_data = "SELECT replies FROM chatbot WHERE queries LIKE '%$getMesg%'";
$run_query = mysqli_query($conn, $check_data) or die("Error");

// if user query matched to database query we'll show the reply otherwise it go to else statement
if(mysqli_num_rows($run_query) > 0){
    //fetching replay from the database according to the user query
    $fetch_data = mysqli_fetch_assoc($run_query);
    //storing replay to a varible which we'll send to ajax
    $replay = $fetch_data['replies'];
    echo $replay;
}else{
    echo "Sorry can't be able to understand you!";
}
*/
?>
