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
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">Nama Karyawan</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">Nilai Kerja</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">Manajer</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">Departemen</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">Tanggal Pengelolaan</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php if (!empty($kinerja)) : ?>
                        <?php foreach ($kinerja as $index => $k) : ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                                    <?= $index + 1 ?>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                                    <?= $k->nama_krywn ?>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                                    <?= number_format($k->nilai_kerja, 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
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
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                                    <?= $k->nama_manajer ?>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                                    <?= $k->departemen ?>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                                    <?= date('d/m/Y', strtotime($k->tgl_pengelolaan)) ?>
                                </td>

                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                            <div class="flex">
                                <a href="<?= base_url('index.php/gaji/edit/'.$g->id_krywn.'/'.$g->tgl_gaji) ?>" 
                                class="text-blue-600 hover:text-blue-900 mr-4">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="javascript:void(0)" 
                                onclick="confirmDelete('<?= $g->id_krywn ?>', '<?= $g->tgl_gaji ?>')" 
                                class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <script>
                                    function confirmDelete(id, tanggal) {
                                        if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                                            window.location.href = '<?= base_url("index.php/gaji/delete/") ?>' + id + '/' + tanggal;
                                        }
                                    }
                                </script>
                            </div>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 border-b border-gray-300 text-center text-gray-500">
                                Data tidak tersedia
                            </td>
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
