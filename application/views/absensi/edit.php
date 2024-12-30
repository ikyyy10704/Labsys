<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Absensi</title>
</head>
<body>
    <h1>Edit Data Absensi</h1>
    <form action="" method="post">
        <label>ID Karyawan:</label>
        <input type="text" name="id_krywn" value="<?php echo $absensi->id_krywn; ?>" required><br>

        <label>Tanggal:</label>
        <input type="date" name="tanggal" value="<?php echo $absensi->tanggal; ?>" required><br>

        <label>Shift:</label>
        
        <input type="text" name="shift" value="<?php echo $absensi->shift; ?>" required><br>

        <label>Keterangan:</label>
        <input type="text" name="keterangan" value="<?php echo $absensi->keterangan; ?>"><br>

        <button type="submit">Simpan</button>
    </form>
</body>
</html>
