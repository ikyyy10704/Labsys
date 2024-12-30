<div class="ml-64 p-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-6">Tambah Data Kinerja</h2>

        <?php if (validation_errors()): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <?php echo validation_errors(); ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('kinerja/simpan') ?>" method="post">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="id_krywn">
                    Karyawan
                </label>
                <select name="id_krywn" id="id_krywn" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Pilih Karyawan</option>
                    <?php foreach ($karyawan as $k): ?>
                        <option value="<?= $k->id_krywn ?>"><?= $k->nama_krywn ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nilai_kerja">
                    Nilai Kerja
                </label>
                <input type="number" step="0.01" name="nilai_kerja" id="nilai_kerja" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       placeholder="Masukkan nilai kerja" min="0" max="100">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="id_manajer">
                    Manajer
                </label>
                <select name="id_manajer" id="id_manajer" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Pilih Manajer</option>
                    <?php foreach ($manajer as $m): ?>
                        <option value="<?= $m->id_manajer ?>"><?= $m->nama_manajer ?> (<?= $m->departemen ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan
                </button>
                <a href="<?= base_url('kinerja') ?>" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>