<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Data Pasien</h2>

        <?php if (validation_errors()): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= validation_errors() ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('administrasi/edit_patient/' . $patient['pasien_id']) ?>" method="post">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informasi Pribadi -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-700">Informasi Pribadi</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="nama" value="<?= set_value('nama', $patient['nama']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK *</label>
                        <input type="text" name="nik" value="<?= set_value('nik', $patient['nik']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" required maxlength="16">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin *</label>
                        <select name="jenis_kelamin" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                            <option value="L" <?= set_select('jenis_kelamin', 'L', $patient['jenis_kelamin'] == 'L') ?>>Laki-laki</option>
                            <option value="P" <?= set_select('jenis_kelamin', 'P', $patient['jenis_kelamin'] == 'P') ?>>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="<?= set_value('tempat_lahir', $patient['tempat_lahir']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir *</label>
                        <input type="date" name="tanggal_lahir" value="<?= set_value('tanggal_lahir', $patient['tanggal_lahir']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                        <input type="text" name="pekerjaan" value="<?= set_value('pekerjaan', $patient['pekerjaan']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>

                <!-- Kontak & Alamat -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-700">Kontak & Alamat</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon *</label>
                        <input type="text" name="telepon" value="<?= set_value('telepon', $patient['telepon']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Darurat</label>
                        <input type="text" name="kontak_darurat" value="<?= set_value('kontak_darurat', $patient['kontak_darurat']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili</label>
                        <textarea name="alamat_domisili" class="w-full px-3 py-2 border border-gray-300 rounded-md"><?= set_value('alamat_domisili', $patient['alamat_domisili']) ?></textarea>
                    </div>
                </div>

                <!-- Informasi Medis -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-700">Informasi Medis</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Riwayat Pasien</label>
                        <textarea name="riwayat_pasien" class="w-full px-3 py-2 border border-gray-300 rounded-md"><?= set_value('riwayat_pasien', $patient['riwayat_pasien']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Permintaan Pemeriksaan</label>
                        <textarea name="permintaan_pemeriksaan" class="w-full px-3 py-2 border border-gray-300 rounded-md"><?= set_value('permintaan_pemeriksaan', $patient['permintaan_pemeriksaan']) ?></textarea>
                    </div>
                </div>

                <!-- Rujukan -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-700">Rujukan</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dokter Perujuk</label>
                        <input type="text" name="dokter_perujuk" value="<?= set_value('dokter_perujuk', $patient['dokter_perujuk']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asal Rujukan</label>
                        <input type="text" name="asal_rujukan" value="<?= set_value('asal_rujukan', $patient['asal_rujukan']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rujukan</label>
                        <input type="text" name="nomor_rujukan" value="<?= set_value('nomor_rujukan', $patient['nomor_rujukan']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Rujukan</label>
                        <input type="date" name="tanggal_rujukan" value="<?= set_value('tanggal_rujukan', $patient['tanggal_rujukan']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis Awal</label>
                        <input type="text" name="diagnosis_awal" value="<?= set_value('diagnosis_awal', $patient['diagnosis_awal']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rekomendasi Pemeriksaan</label>
                        <input type="text" name="rekomendasi_pemeriksaan" value="<?= set_value('rekomendasi_pemeriksaan', $patient['rekomendasi_pemeriksaan']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="<?= base_url('administrasi/patient_detail/' . $patient['pasien_id']) ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Batal
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>