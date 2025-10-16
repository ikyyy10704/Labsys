<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Detail Pasien</h2>
            <div class="flex space-x-2">
                <a href="<?= base_url('administrasi/edit_patient/' . $patient['pasien_id']) ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Edit Data
                </a>
                <a href="<?= base_url('administrasi/patient_history') ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informasi Pribadi -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-3 text-gray-700 border-b pb-2">Informasi Pribadi</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Nama:</span> <?= $patient['nama'] ?></p>
                    <p><span class="font-medium">NIK:</span> <?= $patient['nik'] ?></p>
                    <p><span class="font-medium">No. Registrasi:</span> <?= $patient['nomor_registrasi'] ?></p>
                    <p><span class="font-medium">Jenis Kelamin:</span> <?= $patient['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></p>
                    <p><span class="font-medium">Tempat/Tanggal Lahir:</span> <?= $patient['tempat_lahir'] ?>, <?= date('d/m/Y', strtotime($patient['tanggal_lahir'])) ?></p>
                    <p><span class="font-medium">Umur:</span> <?= $patient['umur'] ?> tahun</p>
                    <p><span class="font-medium">Pekerjaan:</span> <?= $patient['pekerjaan'] ?></p>
                </div>
            </div>

            <!-- Kontak -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-3 text-gray-700 border-b pb-2">Kontak</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Telepon:</span> <?= $patient['telepon'] ?></p>
                    <p><span class="font-medium">Kontak Darurat:</span> <?= $patient['kontak_darurat'] ?></p>
                    <p><span class="font-medium">Alamat:</span> <?= $patient['alamat_domisili'] ?></p>
                </div>
            </div>

            <!-- Informasi Medis -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-3 text-gray-700 border-b pb-2">Informasi Medis</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Riwayat Pasien:</span> <?= $patient['riwayat_pasien'] ?: '-' ?></p>
                    <p><span class="font-medium">Permintaan Pemeriksaan:</span> <?= $patient['permintaan_pemeriksaan'] ?: '-' ?></p>
                </div>
            </div>

            <!-- Rujukan -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-3 text-gray-700 border-b pb-2">Rujukan</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Dokter Perujuk:</span> <?= $patient['dokter_perujuk'] ?: '-' ?></p>
                    <p><span class="font-medium">Asal Rujukan:</span> <?= $patient['asal_rujukan'] ?: '-' ?></p>
                    <p><span class="font-medium">No. Rujukan:</span> <?= $patient['nomor_rujukan'] ?: '-' ?></p>
                    <p><span class="font-medium">Tanggal Rujukan:</span> <?= $patient['tanggal_rujukan'] ? date('d/m/Y', strtotime($patient['tanggal_rujukan'])) : '-' ?></p>
                    <p><span class="font-medium">Diagnosis Awal:</span> <?= $patient['diagnosis_awal'] ?: '-' ?></p>
                    <p><span class="font-medium">Rekomendasi Pemeriksaan:</span> <?= $patient['rekomendasi_pemeriksaan'] ?: '-' ?></p>
                </div>
            </div>
        </div>

        <div class="mt-6 text-sm text-gray-500">
            <p>Tanggal Registrasi: <?= date('d/m/Y H:i', strtotime($patient['created_at'])) ?></p>
        </div>
    </div>
</div>