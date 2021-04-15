<?php
// connecting to database
$conn = mysqli_connect("sql6.freesqldatabase.com", "sql6405141", "BkxHy17U62","sql6405141") or die("Database Error");
if (mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
// getting user message through ajax
$getMesg = mysqli_real_escape_string($conn, $_POST['text']);
$arr_date = array();
$arr_ymd = array();
$regexTucil = "/[Tt]ugas [Kk]ecil|[Tt]ucil/";
$regexTubes = "/[Tt]ugas [Bb]esar|[Tt]ubes/";
$regexPraktikum = "/[Pp]raktikum|[Pp]rak/";
$regexKuis = "/[Kk]uis/";
$regexUTS = "/[Uu]jian [Tt]engah [Ss]emester|[Uu][Tt][Ss]/";
$regexUAS = "/[Uu]jian [Aa]khir [Ss]emester|[Uu][Aa][Ss]/";
$regexDate = "/(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}/";
$regexMatkul = "/[A-Z]{2}[0-9]{4}/";

function date_sort($a, $b) {
    return strtotime($a) - strtotime($b);
}

function rapihinID(){
    $sql = "ALTER TABLE tabel DROP id;";
    $sql .= "ALTER TABLE  `tabel` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
    if (mysqli_multi_query($conn, $sql)) {
        echo "Deadline berhasil disetting ulang";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

if (preg_match("/HELP|help|Help|[Bb]agaimana/",$getMesg)){
    echo "[HELP/BANTUAN]";
    echo "<table class='items'>";
    echo "<tr>";
    echo "<th>Fitur</th>";
    echo "<th>Keterangan</th>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td style='font-size:small;font-weight:700'>" . "Tambah Jadwal";
    echo "<td>" . "Masukkan tanggal, matkul, jenis, topik(opsional)";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td style='font-size:small;font-weight:700'>" . "Show Jadwal";
    echo "<td>" . "Apa Deadline [Waktu]";
    echo "</tr>";
    
    echo "</table>";
    return;
}


if (preg_match("/[Kk]apan/",$getMesg) && preg_match_all($regexMatkul,$getMesg,$ArrMatkul)){
    $Matkul = sprintf($ArrMatkul[0][0]);
    if (preg_match($regexTucil,$getMesg)){
        $KataPenting = "Tucil";
    }else if (preg_match($regexTubes,$getMesg)){
        $KataPenting = "Tubes";
    }else if (preg_match($regexPraktikum,$getMesg)){
        $KataPenting = "Praktikum";
    }else if (preg_match($regexKuis,$getMesg)){
        $KataPenting = "Kuis";
    }else if (preg_match($regexUTS,$getMesg)){
        $KataPenting = "UTS";
    }else if (preg_match($regexUAS,$getMesg)){
        $KataPenting = "UAS";
    }else{
        $KataPenting = "NULL";
    }
    if($KataPenting != "NULL"){
        $sql = "SELECT * FROM tabel WHERE matkul = '$Matkul' AND katapenting = '$KataPenting'";
    }else{
        echo "Jadwal Tugas, Ujian, Atau Praktikum?? Coba lagi ya hehe";
        return;
    }
    if($result = mysqli_query($conn, $sql)){
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                $tanggal = DateTime::createFromFormat('Y-m-d', $row['date'])->format('d-m-Y');
                echo $tanggal;
            }
            // Free result set
            mysqli_free_result($result);
        } else{
            echo "Tidak ada deadline yang ditemukan pada waktu tersebut.";
        }
    }
    return;
}
//Insert/Ubah Deadline
if (preg_match_all($regexDate,$getMesg,$arr_date) && !preg_match("/[Aa]pa/",$getMesg)) {
    for ($i = 0; $i < count($arr_date[0]); $i++){
        $arr_ymd = DateTime::createFromFormat('d-m-Y', $arr_date[0][$i])->format('Y-m-d');
        $date = sprintf($arr_ymd);
        echo $date."\n";
    }
    /*if (preg_match("/[Uu]bah|[Uu]ndur|[Mm]aju|[Gg]anti/"),$getMesg){
        preg_match_all("/[Tt]ask[0-9][0-9]|[T/")
    }
    else*/ if (preg_match_all($regexMatkul,$getMesg,$ArrMatkul)){
        $Matkul = sprintf($ArrMatkul[0][0]);
        echo $Matkul."\n";
        if (preg_match($regexTucil,$getMesg)){
            $KataPenting = "Tucil";
        }else if (preg_match($regexTubes,$getMesg)){
            $KataPenting = "Tubes";
        }else if (preg_match($regexPraktikum,$getMesg)){
            $KataPenting = "Praktikum";
        }else if (preg_match($regexKuis,$getMesg)){
            $KataPenting = "Kuis";
        }else if (preg_match($regexUTS,$getMesg)){
            $KataPenting = "UTS";
        }else if (preg_match($regexUAS,$getMesg)){
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
    return;
}

//Rapihin 
if (preg_match("/[Rr]eset|[Hh]apus [Ss]emua|[Dd]elete [Aa]ll/",$getMesg)){
    rapihinID();
    return;
}

if (preg_match("/[Aa]pa/",$getMesg) || preg_match("/[Dd]eadline/",$getMesg)){
    //Filter N Waktu
    $sql = "";
    $waktu = False;
    if(preg_match_all($regexDate,$getMesg,$arr_date)){
        //sort dulu tanggalnya supaya between di sql ga salah
        //Deadline Rentang waktu tertentu
        if(count($arr_date[0])>1){
            $arr_ymd = DateTime::createFromFormat('d-m-Y', $arr_date[0][0])->format('Y-m-d');
            $date1 = sprintf($arr_ymd);
            $arr_ymd2 = DateTime::createFromFormat('d-m-Y', $arr_date[0][1])->format('Y-m-d');
            $date2 = sprintf($arr_ymd2);
            $hasil = date_sort($date2, $date1);
            if($hasil > 0){
                $datekk1 = strtotime($date1);
                $datek1 = date('Y-m-d',$datekk1);
                $datekk2 = strtotime($date2);
                $datek2 = date('Y-m-d',$datekk2);
            }else{
                $datekk1 = strtotime($date2);
                $datek1 = date('Y-m-d',$datekk1);
                $datekk2 = strtotime($date1);
                $datek2 = date('Y-m-d',$datekk2);
            }
            $sql = "SELECT * FROM tabel WHERE date BETWEEN '$datek1' AND '$datek2'";
        }else{
            $date1 = DateTime::createFromFormat('d-m-Y', $arr_date[0][0])->format('Y-m-d');
            $sql = "SELECT * FROM tabel WHERE date LIKE '%$date1%'";
        }
        $waktu = True;
    }else if(preg_match_all("/[0-9] [Mm]inggu/",$getMesg, $tampung)){
        //Deadline N minggu ke depan
        $minggu = sprintf($tampung[0][0][0]);
        $Nminggu = $minggu + 0;
        $sql = "SELECT * FROM tabel WHERE date BETWEEN CURDATE() AND CURDATE() + INTERVAL $Nminggu WEEK";
        $waktu = True;
    }else if(preg_match_all("/[0-9] [Hh]ari/",$getMesg, $tampung)){
        //Deadline N hari ke depan
        $hari = sprintf($tampung[0][0][0]);
        $Nhari = $hari + 0;
        $sql = "SELECT * FROM tabel WHERE date BETWEEN CURDATE() AND CURDATE() + INTERVAL $Nhari DAY";
        $waktu = True;
    }else if(preg_match_all("/[Hh]ari [Ii]ni/",$getMesg, $tampung)){
        //Deadline Hari ini
        $sql = "SELECT * FROM tabel WHERE date = CURDATE()";
        $waktu = True;
    }else if(preg_match_all("/[Bb]esok/",$getMesg, $tampung)){
        //Deadline Besok
        $sql = "SELECT * FROM tabel WHERE date = CURDATE() + INTERVAL 1 DAY";
        $waktu = True;
    }else if(preg_match_all("/[Mm]inggu [Ii]ni/",$getMesg, $tampung)){
        //Deadline Minggu ini
        $sql = "SELECT * FROM tabel WHERE date BETWEEN CURDATE() AND CURDATE() + INTERVAL 1 WEEK";
        $waktu = True;
    }else if(preg_match_all("/[Mm]inggu [Dd]epan/",$getMesg, $tampung)){
        //Deadline Minggu Depan
        $sql = "SELECT * FROM tabel WHERE date BETWEEN CURDATE() + INTERVAL 1 WEEK AND CURDATE() + INTERVAL 2 WEEK";
        $waktu = True;
    }
    
    //Filter Jenis
    if (preg_match($regexTucil,$getMesg)){
        $KataPenting = "Tucil";
    }else if (preg_match($regexTubes,$getMesg)){
        $KataPenting = "Tubes";
    }else if (preg_match($regexPraktikum,$getMesg)){
        $KataPenting = "Praktikum";
    }else if (preg_match($regexKuis,$getMesg)){
        $KataPenting = "Kuis";
    }else if (preg_match($regexUTS,$getMesg)){
        $KataPenting = "UTS";
    }else if (preg_match($regexUAS,$getMesg)){
        $KataPenting = "UAS";
    }else{
        $KataPenting = "NULL";
    }
    
    if($waktu && $KataPenting!="NULL"){
        $sql .= " AND katapenting LIKE '%$KataPenting%'";
    }else if (!$waktu){
        if($KataPenting != "NULL"){
            $sql = "SELECT * FROM tabel WHERE katapenting LIKE '%$KataPenting%'";
        }else if ($sql == ""){
            //Deadline keseluruhan
            $sql = "SELECT * FROM tabel";
        }
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
    return;
}




echo "Maaf, kami tidak mengerti maksud kamu :(";

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