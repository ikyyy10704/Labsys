<div class="ml-64 p-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Data Karyawan</h1>
            <a href="<?= site_url('data_karyawan/create') ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>Tambah Data
            </a>
        </div>

        <?php if($this->session->flashdata('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
            <?= $this->session->flashdata('success') ?>
        </div>
        <?php endif; ?>
        <?php if($this->session->flashdata('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            <?= $this->session->flashdata('error') ?>
        </div>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">ID Karyawan</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">Alamat</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-semibold text-gray-600 uppercase tracking-wider">Posisi</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300"></th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php foreach($karyawan as $row): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                            <?= $row['id_krywn'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                            <?= $row['nama_krywn'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                            <?= $row['jenis_kelamin'] == 'L' ? 'Laki-Laki' : 'Perempuan' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                            <?= $row['alamat'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                            <?= $row['email'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                            <?= $row['status'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                            <?= $row['posisi'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                            <div class="flex">
                                <a href="<?= site_url('data_karyawan/edit/' . $row['id_krywn']) ?>" class="text-blue-600 hover:text-blue-900 mr-4">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="javascript:void(0)" onclick="confirmDelete('<?= $row['id_krywn'] ?>')" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <script>
                                    function confirmDelete(id) {
                                        if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                                            window.location.href = '<?= site_url('data_karyawan/delete/') ?>' + id;
                                        }
                                    }
                                </script>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
