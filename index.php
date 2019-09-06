<?php
//inisialisasi variabel
$hasil_filter = array();
$file_stopwords = file_get_contents("stopwords.txt"); //membaca file yang berisi daftar stop word
$stopwords = preg_split('/[\s-]+/',$file_stopwords); //mengubah isi file menjadi array
//fungsi untuk menghilangkan stop word
function removeStopword($word,$stopwords_list)
{   
    $result = false;
    for ($i=0; $i < count($stopwords_list); $i++) { //melakukan pencarian kata yang terdapat pada daftar stop word
        if (strcmp(strtolower($word),$stopwords_list[$i]) === 0) {
            $result = true;
            break;
        }
    }
    return $result;
}
//melakukan proses upload dan preprocessing ketika tombol Unggah File diklik
if (isset($_POST['upload'])) {
    $target_path = "uploads/"; //lokasi folder upload
    $target_path = $target_path . basename($_FILES['dokumen']['name']); 
    $fileParts = pathinfo($target_path); //mengambil info file
    //jika file berekstensi txt dan ditemukan maka dilakukan preprocessing
    if(move_uploaded_file($_FILES['dokumen']['tmp_name'], $target_path) && $fileParts['extension'] == "txt") {        
        $isi_file = file_get_contents($target_path); //mengambil isi file
        //memecah isi file menjadi array dan menghilangkan tanda baca pada dokumen teks
        $dokumen = preg_split('/[\s,.–()-?!]+/',$isi_file); 
        //melakukan pre processing
        for ($i=0; $i < count($dokumen); $i++) { 
            if (removeStopword($dokumen[$i],$stopwords) !== true && $dokumen[$i] !== "") {
                array_push($hasil_filter,$dokumen[$i]);
            }
        }
        echo "File ".  basename( $_FILES['dokumen']['name']). " berhasil diunggah";
      } else {
        echo "Unggah file gagal, format file tidak cocok";
      }
}
?>
<!--bagian Front-End-->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Simple Text Document Preprocessing</title>
    <style>
    table, th, td {
    border: 1px solid black;
    }
    </style>
</head>
<body>
    <h1>Simple Text Document Preprocessing</h1>
    <h3>Sebuah program untuk melakukan preprocessing dalam sebuah dokumen teks</h3>
    <p>Referensi stopwords yang digunakan : <a href="http://hikaruyuuki.lecture.ub.ac.id/kamus-kata-dasar-dan-stopword-list-bahasa-indonesia/" target="_blank">Link</a></p>
    <p>Unggah file dokumen teks dalam format txt</p>
    <!--form digunakan untuk mengunggah file dokumen teks dalam bentuk file txt-->
    <form action="#" method="post" enctype="multipart/form-data">
        <input type="file" name="dokumen" id="dokumen">
        <input type="submit" value="Unggah file" name="upload">
    </form>
    <!--menampilkan hasil preprocessing-->
    <h2>Hasil Preprocessing</h2>
    <?php
        if (empty($hasil_filter) == true) {
            echo "";
        } else {
            $ratio = (count($hasil_filter) / count($dokumen)) * 100;
            
            echo "Rasio dari hasil preprocessing : " . round($ratio,2) . "%";
        }
    ?>
    <table style="width:25%">
    <tr>
        <th>Nomor</th>
        <th>Indeks (term)</th> 
    </tr>
    <?php 
    $count = 1;
    if (empty($hasil_filter) !== true) {
        for ($i=0; $i < count($hasil_filter); $i++) { 
            echo "<tr>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . $hasil_filter[$i] . "</td>";
            echo "</tr>";  
        }
    } else {
        echo "";
    }
    ?>
    </table>
</body>
</html>