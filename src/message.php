<!-- Created By CodingNepal -->
<?php
// connecting to database
$conn = mysqli_connect("sql6.freesqldatabase.com", "sql6405141", "BkxHy17U62","sql6405141") or die("Database Error");

// getting user message through ajax
$getMesg = mysqli_real_escape_string($conn, $_POST['text']);
$arr_date = array();
$arr_ymd = array();
$date = "NULL";
$date2 = "NULL";
$Matkul = "NULL";
$ArrMatkul = "";
$KataPenting = "NULL";
$ArrKataPenting = "";
$Topik = "NULL";
$ArrTopik = "NULL";

if (preg_match("/HELP|help|Help|[Bb]agaimana/",$getMesg)){
    echo "Tambah Deadline: Masukkan tanggal, matkul, jenis, topik(opsional) <br> Apa? : Menampilkan Deadline";
}

if (preg_match("/[Rr]eset|[Hh]apus [Ss]emua|[Dd]elete [Aa]ll/",$getMesg)){
    $sql = "ALTER TABLE tabel DROP id;";
    $sql .= "ALTER TABLE  `tabel` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
    if (mysqli_multi_query($conn, $sql)) {
        echo "Deadline berhasil disetting ulang";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
if (preg_match_all("/(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}/",$getMesg,$arr_date)) {
    for ($i = 0; $i < count($arr_date[0]); $i++){
        $arr_ymd = DateTime::createFromFormat('d-m-Y', $arr_date[0][$i])->format('Y-m-d');
        $date = sprintf($arr_ymd);
        echo $date."\n";
    }
    if (preg_match_all("/[A-Z]{2}[0-9]{4}/",$getMesg,$ArrMatkul)){
        $Matkul = sprintf($ArrMatkul[0][0]);
        echo $Matkul."\n";
        if (preg_match_all("/Tubes|Tucil|Kuis|Praktikum|UTS|UAS/",$getMesg,$ArrKataPenting)){
            $KataPenting = sprintf($ArrKataPenting[0][0]);
            echo $KataPenting."\n";
            if(preg_match_all("/topik(.*)/", $getMesg,$ArrTopik)){
                $Topik = sprintf($ArrTopik[1][0]);
                echo $Topik."\n";
            }else{
                $Topik = "NULL";
            }

            $sql = "INSERT INTO tabel (`date`, `matkul`, `katapenting`, `topik`) VALUES ('$date', '$Matkul', '$KataPenting','$Topik')";

            if (mysqli_query($conn, $sql)) {
                echo "<br> Deadline berhasil dimasukkan";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }

        }
        else{
            echo "Tugas atau praktikum? Cek masukkan kamu ya :)";
        }
    }else{
        echo "Maaf, kku gatau kode matkulnya? Cek masukkan kamu ya :)";
    }
}

if (preg_match("/[Aa]pa/",$getMesg)){
    $sql = "SELECT * FROM tabel";
    if($result = mysqli_query($conn, $sql)){
        if(mysqli_num_rows($result) > 0){
            echo "<table>";
                echo "<tr>";
                    echo "<th>Tanggal</th>";
                    echo "<th>Matkul</th>";
                    echo "<th>Jenis</th>";
                    echo "<th>Topik</th>";
                echo "</tr>";
            while($row = mysqli_fetch_array($result)){
                echo "<tr>";
                    $tanggal = DateTime::createFromFormat('Y-m-d', $row['date'])->format('d/m/Y');
                    echo "<td>" . $tanggal . "</td>";
                    echo "<td>" . $row['matkul'] . "</td>";
                    echo "<td>" . $row['katapenting'] . "</td>";
                    if ($row['topik']!="NULL"){
                        echo "<td>" . $row['topik'] . "</td>";
                    }else{
                        echo "<td>-</td>";
                    }
                echo "</tr>";
            }
            echo "</table>";
            // Free result set
            mysqli_free_result($result);
        } else{
            echo "No records matching your query were found.";
        }
    } else{
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }
}


/*
//checking user query to database query
//$check_data = "SELECT replies FROM chatbot WHERE queries LIKE '%$getMesg%'";
$check_data = "SELECT * FROM tabel WHERE arr_date = '$arr_ymd'";
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
