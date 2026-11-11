
<?php
include 'koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_absen = (int) $_GET['id'];

// Ambil data absensi
$stmt = mysqli_prepare($conn, "
    SELECT * FROM absen WHERE id_absen = ?
");
mysqli_stmt_bind_param($stmt, "i", $id_absen);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    die("Data absen tidak ditemukan.");
}

// Ambil data siswa
$data_siswa = mysqli_query($conn, "SELECT * FROM siswa ORDER BY siswa_nama ASC");

$error = "";

if (isset($_POST['submit'])) {

    $id_siswa = (int) $_POST['siswa_id'];
    $tanggal = $_POST['tanggal'];
    $jam_masuk = !empty($_POST['jam_masuk']) ? $_POST['jam_masuk'] : null;
    $status = $_POST['status'];
    
    $status_valid = ['Hadir', 'Izin', 'Sakit', 'Alpa'];

    if (!in_array($status, $status_valid, true)) {
        $error = "Status absen tidak valid!";
    } else {

        $query = "UPDATE absen
                  SET siswa_id = ?,
                      tanggal = ?,
                      jam_masuk = ?,
                      status = ?,
                      
                  WHERE id_absen = ?";

        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "issssi",
                $id_siswa,
                $tanggal,
                $jam_masuk,
                $status,
                
                $id_absen
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal mengubah data: " .
                         mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);

        } else {
            $error = "Query gagal: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Absensi Siswa</title>
    <link rel="stylesheet" href="style_absen.css">
</head>
<body>

<div class="container">

    <h2>Edit Absensi Siswa</h2>

    <?php if ($error != "") { ?>
        <p class="error"><?= htmlspecialchars($error); ?></p>
    <?php } ?>

    <form method="POST">

        <div class="form-group">
            <label>Pilih Siswa:</label>

            <select name="siswa_id" required>
                <?php while ($s = mysqli_fetch_assoc($data_siswa)) { ?>

                    <option value="<?= $s['siswa_id']; ?>"
                        <?= $s['siswa_id'] == $data['siswa_id'] ? 'selected' : ''; ?>>

                        <?= htmlspecialchars($s['siswa_nama']); ?>
                        (<?= htmlspecialchars($s['kelas_nama']); ?>)

                    </option>

                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Tanggal Absensi:</label>

            <input type="date" name="tanggal"
                   value="<?= htmlspecialchars($data['tanggal']); ?>"
                   required>
        </div>

        <div class="form-group">
            <label>Jam Masuk:</label>

            <input type="time" name="jam_masuk"
                   value="<?= htmlspecialchars(substr($data['jam_masuk'] ?? '', 0, 5)); ?>">
        </div>

        <div class="form-group">
            <label>Status Kehadiran:</label>

            <select name="status" required>
                <?php
                $status_list = ['Hadir', 'Izin', 'Sakit', 'Alpa'];

                foreach ($status_list as $status) {
                ?>
                    <option value="<?= $status; ?>"
                        <?= $data['status'] == $status ? 'selected' : ''; ?>>
                        <?= $status; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        
        <button type="submit" name="submit" class="btn">
            Simpan Perubahan
        </button>

        <a href="index_absen.php" class="btn btn-secondary">
            Batal
        </a>

    </form>

</div>

</body>
</html>
