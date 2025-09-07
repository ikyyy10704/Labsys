<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Hasil Pemeriksaan - Labsys</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
    <style>
        /* Custom CSS untuk full width yang benar-benar penuh */
        * {
            box-sizing: border-box;
        }
        
        .full-width-container {
            width: 100vw !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .main-content-full {
            width: 100vw !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            left: 0 !important;
            position: relative;
        }
        
        /* Override Tailwind constraints */
        .w-full-override {
            width: 100% !important;
            max-width: 100% !important;
        }
        
        .container-full {
            width: 100vw !important;
            max-width: none !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        
        /* Responsive full width */
        @media (min-width: 640px) {
            .container-full {
                padding-left: 1.5rem !important;
                padding-right: 1.5rem !important;
            }
        }
        
        @media (min-width: 1024px) {
            .container-full {
                padding-left: 2rem !important;
                padding-right: 2rem !important;
            }
        }
        
        /* Force table to use full width */
        .table-full-width {
            width: 100% !important;
            min-width: 100% !important;
        }
        
        /* Custom scrollbar untuk table */
        .custom-scrollbar::-webkit-scrollbar {
            height: 8px;
            width: 8px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Main Content - Full Width -->
    <main class="main-content-full min-h-screen bg-gray-50">
        <!-- Page Header -->
        <div class=" p-6 bg-gradient-to-r from-med-blue to-med-light-blue shadow-sm full-width-container">
            <div class="container-full py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <i data-lucide="edit" class="w-5 h-5 text-white"></i>
                            </div>
                            <span>Input Hasil Pemeriksaan</span>
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">Pilih pemeriksaan dan input hasil laboratorium</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="container-full py-6">
            <!-- Selection Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 w-full-override">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                        <i data-lucide="search" class="w-5 h-5 text-blue-600"></i>
                        <span>Pilih Pemeriksaan</span>
                    </h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="examination-select" class="block text-sm font-medium text-gray-700 mb-2">
                                Pemeriksaan yang Siap untuk Input Hasil
                            </label>
                            <select id="examination-select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih pemeriksaan...</option>
                                <?php if (isset($ready_examinations) && !empty($ready_examinations)): ?>
                                    <?php foreach ($ready_examinations as $exam): ?>
                                        <option value="<?= $exam['pemeriksaan_id'] ?>" 
                                                data-type="<?= strtolower(str_replace(' ', '_', $exam['jenis_pemeriksaan'])) ?>"
                                                data-patient="<?= htmlspecialchars($exam['nama_pasien']) ?>"
                                                data-number="<?= $exam['nomor_pemeriksaan'] ?>"
                                                data-exam-type="<?= htmlspecialchars($exam['jenis_pemeriksaan']) ?>">
                                            <?= $exam['nomor_pemeriksaan'] ?> - <?= htmlspecialchars($exam['nama_pasien']) ?> - <?= htmlspecialchars($exam['jenis_pemeriksaan']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">Tidak ada pemeriksaan yang siap untuk input hasil</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button id="load-examination" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                <span>Muat Pemeriksaan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Result Form -->
            <div id="result-form-container" class="hidden">
                <!-- Examination Info Card -->
                <div id="examination-info" class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 w-full-override">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                            <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                            <span>Informasi Pemeriksaan</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-600">Nomor:</span>
                                <span id="exam-number" class="ml-2 text-gray-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Pasien:</span>
                                <span id="patient-name" class="ml-2 text-gray-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Jenis:</span>
                                <span id="exam-type" class="ml-2 text-gray-900"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Forms -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 w-full-override">
                    <div class="p-6">
                        <form id="result-form">
                            <input type="hidden" id="examination_id" name="examination_id" value="">
                            <input type="hidden" id="result_type" name="result_type" value="">
                            
                            <!-- Kimia Darah Form -->
                            <div id="form-kimia_darah" class="result-form hidden">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                                    <i data-lucide="flask" class="w-5 h-5 text-red-600"></i>
                                    <span>Hasil Kimia Darah</span>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Gula Darah Sewaktu (mg/dL)</label>
                                        <input type="number" name="gula_darah_sewaktu" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: 70-140 mg/dL</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Gula Darah Puasa (mg/dL)</label>
                                        <input type="number" name="gula_darah_puasa" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: 70-110 mg/dL</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Gula Darah 2J PP (mg/dL)</label>
                                        <input type="number" name="gula_darah_2jam_pp" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: <140 mg/dL</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kolesterol Total (mg/dL)</label>
                                        <input type="number" name="cholesterol_total" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: <200 mg/dL</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kolesterol HDL (mg/dL)</label>
                                        <input type="number" name="cholesterol_hdl" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: >40 mg/dL (L), >50 mg/dL (P)</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kolesterol LDL (mg/dL)</label>
                                        <input type="number" name="cholesterol_ldl" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: <130 mg/dL</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Trigliserida (mg/dL)</label>
                                        <input type="number" name="trigliserida" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: <150 mg/dL</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Asam Urat (mg/dL)</label>
                                        <input type="number" name="asam_urat" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: 2.5-7.0 mg/dL</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ureum (mg/dL)</label>
                                        <input type="number" name="ureum" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: 10-50 mg/dL</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kreatinin (mg/dL)</label>
                                        <input type="number" name="creatinin" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: 0.6-1.2 mg/dL</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">SGPT (U/L)</label>
                                        <input type="number" name="sgpt" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: 7-56 U/L</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">SGOT (U/L)</label>
                                        <input type="number" name="sgot" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: 10-40 U/L</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Hematologi Form -->
                            <div id="form-hematologi" class="result-form hidden">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                                    <i data-lucide="droplet" class="w-5 h-5 text-red-600"></i>
                                    <span>Hasil Hematologi</span>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Hemoglobin (g/dL)</label>
                                        <input type="number" name="hemoglobin" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: 12-16 g/dL (P), 14-18 g/dL (L)</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Hematokrit (%)</label>
                                        <input type="number" name="hematokrit" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: 37-47% (P), 40-50% (L)</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Laju Endap Darah (mm/jam)</label>
                                        <input type="number" name="laju_endap_darah" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: <15 mm/jam (P), <10 mm/jam (L)</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Clotting Time</label>
                                        <input type="time" name="clotting_time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: 5-15 menit</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Bleeding Time</label>
                                        <input type="time" name="bleeding_time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: 1-7 menit</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah</label>
                                        <select name="golongan_darah" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih...</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="AB">AB</option>
                                            <option value="O">O</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rhesus</label>
                                        <select name="rhesus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih...</option>
                                            <option value="+">Positif (+)</option>
                                            <option value="-">Negatif (-)</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2 lg:col-span-3 xl:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Malaria</label>
                                        <textarea name="malaria" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Hasil pemeriksaan malaria..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Urinologi Form -->
                            <div id="form-urinologi" class="result-form hidden">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                                    <i data-lucide="beaker" class="w-5 h-5 text-yellow-600"></i>
                                    <span>Hasil Urinologi</span>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Makroskopis</label>
                                        <textarea name="makroskopis" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Warna, kejernihan, bau..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mikroskopis</label>
                                        <textarea name="mikroskopis" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Eritrosit, leukosit, epitel..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">pH Kimia</label>
                                        <input type="number" name="kimia_ph" step="0.1" min="1" max="14" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Normal: 4.6-8.0</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Protein</label>
                                        <textarea name="protein" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Hasil tes protein..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tes Kehamilan</label>
                                        <select name="tes_kehamilan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih...</option>
                                            <option value="Positif">Positif</option>
                                            <option value="Negatif">Negatif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Serologi Form -->
                            <div id="form-serologi" class="result-form hidden">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                                    <i data-lucide="shield" class="w-5 h-5 text-green-600"></i>
                                    <span>Hasil Serologi Imunologi</span>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">RDT Antigen</label>
                                        <select name="rdt_antigen" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih...</option>
                                            <option value="Positif">Positif</option>
                                            <option value="Negatif">Negatif</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">HbsAg</label>
                                        <select name="hbsag" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih...</option>
                                            <option value="Reaktif">Reaktif</option>
                                            <option value="Non-Reaktif">Non-Reaktif</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">NS1</label>
                                        <select name="ns1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih...</option>
                                            <option value="Positif">Positif</option>
                                            <option value="Negatif">Negatif</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">HIV</label>
                                        <select name="hiv" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih...</option>
                                            <option value="Reaktif">Reaktif</option>
                                            <option value="Non-Reaktif">Non-Reaktif</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2 lg:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Widal</label>
                                        <textarea name="widal" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Hasil tes widal..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- TBC Form -->
                            <div id="form-tbc" class="result-form hidden">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                                    <i data-lucide="lungs" class="w-5 h-5 text-purple-600"></i>
                                    <span>Hasil TBC</span>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Dahak</label>
                                        <select name="dahak" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih...</option>
                                            <option value="Negatif">Negatif</option>
                                            <option value="Scanty">Scanty</option>
                                            <option value="+1">+1</option>
                                            <option value="+2">+2</option>
                                            <option value="+3">+3</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">TCM</label>
                                        <select name="tcm" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih...</option>
                                            <option value="Detected">Detected</option>
                                            <option value="Not Detected">Not Detected</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- IMS Form -->
                            <div id="form-ims" class="result-form hidden">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                                    <i data-lucide="heart" class="w-5 h-5 text-pink-600"></i>
                                    <span>Hasil IMS</span>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Sifilis</label>
                                        <select name="sifilis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih...</option>
                                            <option value="Reaktif">Reaktif</option>
                                            <option value="Non-Reaktif">Non-Reaktif</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Duh Tubuh</label>
                                        <textarea name="duh_tubuh" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Hasil pemeriksaan duh tubuh..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- MLS Form -->
                            <div id="form-mls" class="result-form hidden">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                                    <i data-lucide="microscope" class="w-5 h-5 text-indigo-600"></i>
                                    <span>Hasil MLS (Lainnya)</span>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Tes</label>
                                        <input type="text" name="jenis_tes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama jenis tes...">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Metode</label>
                                        <input type="text" name="metode" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Metode pemeriksaan...">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                                        <input type="text" name="satuan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="mg/dL, U/L, dll...">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Rujukan</label>
                                        <input type="text" name="nilai_rujukan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Range nilai normal...">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Hasil</label>
                                        <textarea name="hasil" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Hasil pemeriksaan lengkap..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                                <button type="button" id="cancel-form" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                    Batal
                                </button>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                                    <i data-lucide="save" class="w-4 h-4"></i>
                                    <span>Simpan Hasil</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Load examination data
        document.getElementById('load-examination').addEventListener('click', function() {
            const select = document.getElementById('examination-select');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                const examType = selectedOption.getAttribute('data-type');
                loadExaminationForm(selectedOption, examType);
            } else {
                alert('Pilih pemeriksaan terlebih dahulu');
            }
        });

        function loadExaminationForm(option, examType) {
            // Show form container
            document.getElementById('result-form-container').classList.remove('hidden');
            
            // Update examination info
            document.getElementById('exam-number').textContent = option.getAttribute('data-number');
            document.getElementById('patient-name').textContent = option.getAttribute('data-patient');
            document.getElementById('exam-type').textContent = option.getAttribute('data-exam-type');
            
            // Set hidden form values
            document.getElementById('examination_id').value = option.value;
            document.getElementById('result_type').value = examType;
            
            // Hide all forms
            document.querySelectorAll('.result-form').forEach(form => {
                form.classList.add('hidden');
            });
            
            // Show selected form with fixed mapping
            const formId = 'form-' + examType;
            const targetForm = document.getElementById(formId);
            if (targetForm) {
                targetForm.classList.remove('hidden');
                console.log('Showing form:', formId); // Debug log
                
                // Load existing results if any
                loadExistingResults(option.value, examType);
            } else {
                console.error('Form not found:', formId); // Debug log
                alert('Form untuk jenis pemeriksaan ini belum tersedia');
            }
            
            // Scroll to form
            document.getElementById('result-form-container').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function loadExistingResults(examinationId, examType) {
            // AJAX call to get existing results
            fetch('<?= base_url("laboratorium/get_examination_data") ?>/' + examinationId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    examination_id: examinationId,
                    exam_type: examType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.existing_results) {
                    populateFormWithResults(data.existing_results, examType);
                }
            })
            .catch(error => {
                console.error('Error loading existing results:', error);
            });
        }

        function populateFormWithResults(results, examType) {
            if (!results) return;
            
            const form = document.getElementById('form-' + examType);
            if (!form) return;
            
            // Populate form fields based on results
            Object.keys(results).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input && results[key] !== null) {
                    input.value = results[key];
                }
            });
        }

        // Form submission
        document.getElementById('result-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const examinationId = document.getElementById('examination_id').value;
            const resultType = document.getElementById('result_type').value;
            
            if (!examinationId || !resultType) {
                alert('Data pemeriksaan tidak lengkap');
                return;
            }
            
            // Get form data
            const formData = new FormData(this);
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span>Menyimpan...</span>';
            submitBtn.disabled = true;
            
            // Send AJAX request
            fetch('<?= base_url("laboratorium/save_examination_results") ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Hasil pemeriksaan berhasil disimpan!');
                    
                    // Reset form
                    this.reset();
                    document.getElementById('result-form-container').classList.add('hidden');
                    document.getElementById('examination-select').value = '';
                } else {
                    alert('Error: ' + (data.message || 'Gagal menyimpan hasil'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan hasil');
            })
            .finally(() => {
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                lucide.createIcons();
            });
        });

        // Cancel form
        document.getElementById('cancel-form').addEventListener('click', function() {
            if (confirm('Yakin ingin membatalkan? Data yang sudah diinput akan hilang.')) {
                document.getElementById('result-form').reset();
                document.getElementById('result-form-container').classList.add('hidden');
                document.getElementById('examination-select').value = '';
            }
        });
    </script>
</body>
</html>