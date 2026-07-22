<?php
$conn = new mysqli('localhost', 'root', '', 'db_keuangan_pribadi');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "INSERT INTO anggaran (user_id, kategori_id, nominal_batas) VALUES (1, 1, 1000)";
if ($conn->query($sql) === TRUE) {
    echo "OK INSERT\n";
} else {
    echo "ERROR INSERT: " . $conn->error . "\n";
}
$conn->close();
?>
