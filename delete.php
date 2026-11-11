
<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST'
    && isset($_POST['id'])
    && is_numeric($_POST['id'])) {

    $id_absen = (int) $_POST['id'];

    $query = "DELETE FROM absen WHERE id_absen = ?";

    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        die("Query gagal: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $id_absen);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal menghapus data: " .
             mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);

} else {

    header("Location: index.php");
    exit;
}
?>
