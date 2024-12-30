<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kinerja Karyawan</title>
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Data Kinerja Karyawan</h1>
        <form action="<?= site_url('kinerja/update/'.$kinerja->id_pengelolaan) ?>" method="post">
            <div class="form-group">
                <label for="id_pengelolaan">ID Pengelolaan</label>
                <input type="text" class="form-control" name="id_pengelolaan" value="<?= $kinerja->id_pengelolaan ?>" required>
            </div>
            <div class="form-group">
                <label for="nilai_kerja">Nilai Kinerja</label>
                <input type="number" class="form-control" name="nilai_kerja" value="<?= $kinerja->nilai_kerja ?>" required>
            </div>
            <div class="form-group">
                <label for="status_pengelolaan">Status Pengelolaan</label>
                <input type="text" class="form-control" name="status_pengelolaan" value="<?= $kinerja->status_pengelolaan ?>" required>
            </div>
            <div class="form-group">
                <label for="tgl_pengelolaan">Tanggal Pengelolaan</label>
                <input type="date" class="form-control" name="tgl_pengelolaan" value="<?= $kinerja->tgl_pengelolaan ?>" required>
            </div>
            <div class="form-group">
                <label for="id_manajer">ID Manajer</label>
                <input type="text" class="form-control" name="id_manajer" value="<?= $kinerja->id_manajer ?>" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update</button>
        </form>
    </div>
</body>
</html>
