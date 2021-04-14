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

function date_sort($a, $b) {
    return strtotime($a) - strtotime($b);
}


if (preg_match("/HELP|help|Help|[Bb]agaimana/",$getMesg)){
    echo "Tambah Deadline: Masukkan tanggal, matkul, jenis, topik(opsional) <br> Apa? : Menampilkan Deadline";
}


if (preg_match("/[Aa]pa/",$getMesg) && preg_match("/[Dd]eadline/",$getMesg)){
    if(preg_match_all("/(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}/",$getMesg,$arr_date)){
        //sort dulu tanggalnya supaya between di sql ga salah
        //Deadline Rentang waktu tertentu
        $arr_ymd = DateTime::createFromFormat('d-m-Y', $arr_date[0][0])->format('Y-m-d');
        $date1 = sprintf($arr_ymd);
        $arr_ymd2 = DateTime::createFromFormat('d-m-Y', $arr_date[0][1])->format('Y-m-d');
        $date2 = sprintf($arr_ymd2);
        $hasil = date_sort($date2, $date1);
        if($hasil > 0){
            $datek1 = $date1;
            $datek2 = $date2;
        }else{
            $datek1 = $date2;
            $datek2 = $date1;
        }
        $sql = "SELECT * FROM tabel WHERE (date BETWEEN '$datek1' AND '$datek2')";
    }else if(preg_match_all("/[0-9] [Mm]inggu/",$getMesg, $tampung)){
        //Deadline N minggu ke depan
        $minggu = sprintf($tampung[0][0][0]);
        $Nminggu = $minggu + 0;
        $sql = "SELECT * FROM tabel WHERE date BETWEEN CURDATE() AND CURDATE() + INTERVAL $Nminggu WEEK";
    }else if(preg_match_all("/[0-9] [Hh]ari/",$getMesg, $tampung)){
        //Deadline N hari ke depan
        $hari = sprintf($tampung[0][0][0]);
        $Nhari = $hari + 0;
        $sql = "SELECT * FROM tabel WHERE date BETWEEN CURDATE() AND CURDATE() + INTERVAL $Nhari DAY";
    }else if(preg_match_all("/[Hh]ari [Ii]ni/",$getMesg, $tampung)){
        //Deadline Hari ini
        $sql = "SELECT * FROM tabel WHERE date = CURDATE()";
    }else{
        //Deadline keseluruhan
        $sql = "SELECT * FROM tabel";
    }
    if($result = mysqli_query($conn, $sql)){
        if(mysqli_num_rows($result) > 0){
            echo "[DAFTAR DEADLINE]";
            echo "<table class='items'>";
                echo "<tr>";
                    echo "<th>ID</th>";
                    echo "<th>Tanggal</th>";
                    echo "<th>Matkul</th>";
                    echo "<th>Jenis</th>";
                    echo "<th>Topik</th>";
                echo "</tr>";
            while($row = mysqli_fetch_array($result)){
                echo "<tr>";
                    $tanggal = DateTime::createFromFormat('Y-m-d', $row['date'])->format('d-m-Y');
                    echo "<td>" . $row['id'];
                    echo "<td style='font-size:xx-small;font-weight:700'>" . $tanggal;
                    echo "<td>" . $row['matkul'];
                    echo "<td>" . $row['katapenting'];
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
            echo "Tidak ada deadline yang ditemukan pada rentang waktu tersebut.";
        }
    } else{
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
    }
}

//Rapihin ID
if (preg_match("/[Rr]eset|[Hh]apus [Ss]emua|[Dd]elete [Aa]ll/",$getMesg)){
    $sql = "ALTER TABLE tabel DROP id;";
    $sql .= "ALTER TABLE  `tabel` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
    if (mysqli_multi_query($conn, $sql)) {
        echo "Deadline berhasil disetting ulang";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

//Insert Deadline baru
if (preg_match_all("/(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}/",$getMesg,$arr_date) && !preg_match("/[Aa]pa/",$getMesg)) {
    for ($i = 0; $i < count($arr_date[0]); $i++){
        $arr_ymd = DateTime::createFromFormat('d-m-Y', $arr_date[0][$i])->format('Y-m-d');
        $date = sprintf($arr_ymd);
        echo $date."\n";
    }
    if (preg_match_all("/[A-Z]{2}[0-9]{4}/",$getMesg,$ArrMatkul)){
        $Matkul = sprintf($ArrMatkul[0][0]);
        echo $Matkul."\n";
        if (preg_match("/[Tt]ugas [Kk]ecil|[Tt]ucil/",$getMesg)){
            $KataPenting = "Tucil";
        }else if (preg_match("/[Tt]ugas [Bb]esar|[Tt]ubes/",$getMesg)){
            $KataPenting = "Tubes";
        }else if (preg_match("/[Pp]raktikum|[Pp]rak/",$getMesg)){
            $KataPenting = "Praktikum";
        }else if (preg_match("/[Kk]uis/",$getMesg)){
            $KataPenting = "Kuis";
        }else if (preg_match("/[Uu]jian [Tt]engah [Ss]emester|[Uu][Tt][Ss]/",$getMesg)){
            $KataPenting = "UTS";
        }else if (preg_match("/[Uu]jian [Aa]khir [Ss]emester|[Uu][Aa][Ss]/",$getMesg)){
            $KataPenting = "UAS";
        }
        else{
            echo " - Tugas atau praktikum? Cek masukkan kamu ya :)";
            $KataPenting = "NULL";
            return;
        }
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
    }else{
        echo "Maaf, aku gatau kode matkulnya? Cek masukkan kamu ya :)";
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
