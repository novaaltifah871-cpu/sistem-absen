
<?php
include 'koneksi.php';

// Mengambil data siswa dari database
$data_siswa = mysqli_query(
    $conn,
    "SELECT * FROM siswa ORDER BY siswa_nama ASC"
);

$error = "";

if (isset($_POST['submit'])) {

    // Mengambil data dari form
    $id_siswa = (int) $_POST['siswa_id'];
    $tanggal = $_POST['tanggal'];
    $jam_masuk = !empty($_POST['jam_masuk'])
        ? $_POST['jam_masuk']
        : null;
    $status = $_POST['status'];

    // Validasi status
    $status_valid = ['Hadir', 'Izin', 'Sakit', 'Alpa'];

    if (!in_array($status, $status_valid, true)) {

        $error = "Status absen tidak valid!";

    } else {

        // Query menyimpan data absensi
        $query = "INSERT INTO absen
                  (siswa_id, tanggal, jam_masuk, status)
                  VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {

            // Menghubungkan parameter
            mysqli_stmt_bind_param(
                $stmt,
                "isss",
                $id_siswa,
                $tanggal,
                $jam_masuk,
                $status
            );

            // Menjalankan query
            if (mysqli_stmt_execute($stmt)) {

                header("Location: index.php");
                exit;

            } else {

                $error = "Gagal menyimpan data: " .
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

    <title>Tambah Absensi Siswa</title>

    <link rel="stylesheet" href="style_absen.css">
</head>

<body>

<div class="container">

    <h2>Tambah Absensi Siswa</h2>

    <!-- Pesan Error -->
    <?php if (!empty($error)) { ?>

        <p class="error">
            <?= htmlspecialchars($error); ?>
        </p>

    <?php } ?>

    <!-- Form Absensi -->
    <form method="POST" action="">

        <!-- Pilih Siswa -->
        <div class="form-group">

            <label>Pilih Siswa:</label>

            <select name="siswa_id" required>

                <option value="">-- Pilih Siswa --</option>

                <?php while ($s = mysqli_fetch_assoc($data_siswa)) { ?>

                    <option value="<?= (int) $s['siswa_id']; ?>">

                        <?= htmlspecialchars($s['siswa_nama']); ?>

                        (<?= htmlspecialchars($s['kelas_nama']); ?>)

                    </option>

                <?php } ?>

            </select>

        </div>

        <!-- Tanggal Absensi -->
        <div class="form-group">

            <label>Tanggal Absensi:</label>

            <input
                type="date"
                name="tanggal"
                value="<?= date('Y-m-d'); ?>"
                required
            >

        </div>

        <!-- Jam Masuk -->
        <div class="form-group">

            <label>Jam Masuk:</label>

            <input
                type="time"
                name="jam_masuk"
            >

        </div>

        <!-- Status Kehadiran -->
        <div class="form-group">

            <label>Status Kehadiran:</label>

            <select name="status" required>

                <option value="">-- Pilih Status --</option>

                <option value="Hadir">Hadir</option>

                <option value="Izin">Izin</option>

                <option value="Sakit">Sakit</option>

                <option value="Alpa">Alpa</option>

            </select>

        </div>

        <!-- Tombol -->
        <button type="submit" name="submit" class="btn">
            Simpan Absensi
        </button>

        <a href="index.php" class="btn btn-secondary">
            Batal
        </a>

    </form>

</div>

</body>
</html>
