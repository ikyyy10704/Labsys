<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kinerja Karyawan</title>
</head>
<body>
    <div class="container mt-5">
        <h1>Tambah Data Kinerja Karyawan</h1>
        <form action="<?= site_url('kinerja/simpan') ?>" method="post">
            <div class="form-group">
                <label for="id_pengelolaan">Nama Karyawan</label>
                <select class="form-control" name="id_pengelolaan" required>
                    <option value="">Pilih Nama Karyawan</option>
                    <?php foreach ($karyawan as $krywn) : ?>
                        <option value="<?= $krywn->id_pengelolaan ?>"><?= $krywn->nama_krywn ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nilai_kerja">Nilai Kinerja</label>
                <input type="number" class="form-control" name="nilai_kerja" required>
            </div>
            <div class="form-group">
                <label for="status_pengelolaan">Status Pengelolaan</label>
                <input type="text" class="form-control" name="status_pengelolaan" required>
            </div>
            <div class="form-group">
                <label for="tgl_pengelolaan">Tanggal Pengelolaan</label>
                <input type="date" class="form-control" name="tgl_pengelolaan" required>
            </div>
            <div class="form-group">
                <label for="id_manajer">ID Manajer</label>
                <input type="text" class="form-control" name="id_manajer" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        </form>
    </div>
</body>
</html>
