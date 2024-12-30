<!-- application/views/kinerja/kinerja_list.php -->
<div class="ml-64 p-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Data Kinerja Karyawan</h1>
            <a href="<?= base_url('kinerja/tambah') ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>Tambah Data
            </a>
        </div>

        <?php if ($this->session->flashdata('success')) : ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                <?= $this->session->flashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')) : ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <?= $this->session->flashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 uppercase">Nama Karyawan</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 uppercase">Nilai Kerja</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 uppercase">Manajer</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 uppercase">Departemen</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 uppercase">Tanggal Pengelolaan</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (!empty($kinerja)) : ?>
                        <?php foreach ($kinerja as $index => $k) : ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $index + 1 ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $k->nama_krywn ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= number_format($k->nilai_kerja, 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php
                                    switch ($k->status_pengelolaan) {
                                        case 'Sangat Baik':
                                            echo 'bg-green-100 text-green-800';
                                            break;
                                        case 'Baik':
                                            echo 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'Cukup':
                                            echo 'bg-yellow-100 text-yellow-800';
                                            break;
                                        default:
                                            echo 'bg-red-100 text-red-800';
                                    }
                                    ?>">
                                        <?= $k->status_pengelolaan ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $k->nama_manajer ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $k->departemen ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= date('d/m/Y', strtotime($k->tgl_pengelolaan)) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <div class="flex justify-end space-x-3">
                                        <a href="<?= base_url('kinerja/edit/'.$k->id_pengelolaan) ?>" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)" onclick="confirmDelete('<?= $k->id_pengelolaan ?>')" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">Data tidak tersedia</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        window.location.href = '<?= base_url("kinerja/hapus/") ?>' + id;
    }
}
</script>