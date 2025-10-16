<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Professional Print Styles */
        @page {
            size: A4;
            margin: 15mm;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #2c3e50;
            margin: 0;
            padding: 0;
            background: white;
            -webkit-font-smoothing: antialiased;
        }
        
        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 0;
            position: relative;
        }
        
        /* Modern Header Design */
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 1;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
            margin-right: 25px;
            border-radius: 15px;
            overflow: hidden;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .logo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 12px;
        }
        
        .logo-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: white;
            border-radius: 12px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .lab-info {
            flex: 1;
        }
        
        .lab-info h1 {
            margin: 0 0 8px 0;
            font-size: 22px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            letter-spacing: 0.5px;
        }
        
        .lab-info .subtitle {
            margin: 4px 0;
            font-size: 11px;
            opacity: 0.9;
            font-weight: 400;
        }
        
        .lab-info .contact-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 8px;
            font-size: 10px;
            opacity: 0.8;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Professional Document Title */
        .document-title {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 12px;
            border-left: 5px solid #1e40af;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: relative;
        }
        
        .document-title::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #1e40af, #3b82f6, #60a5fa);
            border-radius: 12px 12px 0 0;
        }
        
        .document-title h2 {
            margin: 0 0 10px 0;
            font-size: 18px;
            color: #1e40af;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .exam-number {
            margin: 0;
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }
        
        .exam-number strong {
            color: #1e40af;
        }
        
        /* Enhanced Patient Information Section */
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 15px;
            padding: 25px;
            border-left: 5px solid #10b981;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        
        .info-column h3 {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: #1e40af;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-column h3::before {
            content: '';
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            border-radius: 2px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
            align-items: flex-start;
        }
        
        .info-label {
            font-weight: 600;
            color: #475569;
            width: 100px;
            flex-shrink: 0;
            font-size: 10px;
        }
        
        .info-value {
            color: #1e293b;
            flex: 1;
            font-size: 10px;
            font-weight: 500;
        }
        
        /* Professional Results Section */
        .results-section {
            margin: 30px 0;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .results-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 15px 20px;
            font-size: 14px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0.5px;
            position: relative;
        }
        
        .results-header::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #60a5fa, #3b82f6, #1e40af);
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            background: white;
        }
        
        .results-table th {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border: 1px solid #d1d5db;
            padding: 12px 10px;
            text-align: center;
            font-weight: 700;
            color: #374151;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .results-table td {
            border: 1px solid #d1d5db;
            padding: 12px 10px;
            color: #1e293b;
            font-size: 10px;
            vertical-align: middle;
        }
        
        .results-table tr:nth-child(even) {
            background: linear-gradient(135deg, #fafbfc 0%, #f9fafb 100%);
        }
        
        .results-table tr:hover {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        }
        
        /* Enhanced Status Indicators */
        .status-normal {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 9px;
            border: 1px solid #10b981;
        }
        
        .status-high {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 9px;
            border: 1px solid #ef4444;
        }
        
        .status-low {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 9px;
            border: 1px solid #f59e0b;
        }
        
        .status-abnormal {
            background: linear-gradient(135deg, #fce7f3, #fbcfe8);
            color: #be185d;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 9px;
            border: 1px solid #ec4899;
        }
        
        /* Professional Invoice Section */
        .invoice-section {
            margin: 30px 0;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(34, 197, 94, 0.1);
            border: 1px solid #d1fae5;
        }
        
        .invoice-header {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            padding: 15px 20px;
            font-size: 14px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0.5px;
        }
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        .invoice-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        
        .invoice-table td:first-child {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            font-weight: 700;
            color: #374151;
            width: 140px;
            border-right: 2px solid #e5e7eb;
        }
        
        .payment-status-lunas {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 9px;
            border: 1px solid #10b981;
        }
        
        .payment-status-belum {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 9px;
            border: 1px solid #ef4444;
        }
        
        .currency {
            color: #059669;
            font-weight: 700;
            font-size: 12px;
        }
        
        .date-highlight {
            color: #1e40af;
            font-weight: 600;
        }
        
        /* Professional Notes Section */
        .notes-section {
            margin: 30px 0;
            padding: 20px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 5px solid #f59e0b;
            border-radius: 0 12px 12px 0;
            box-shadow: 0 2px 15px rgba(245, 158, 11, 0.2);
            border-top: 1px solid #f59e0b;
            border-right: 1px solid #f59e0b;
            border-bottom: 1px solid #f59e0b;
        }
        
        .notes-section h4 {
            margin: 0 0 12px 0;
            color: #92400e;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .notes-section h4::before {
            content: '⚠️';
            font-size: 14px;
        }
        
        .notes-section ul {
            margin: 0;
            padding-left: 20px;
            color: #78350f;
            font-size: 9px;
            line-height: 1.6;
        }
        
        .notes-section li {
            margin-bottom: 6px;
        }
        
        /* Professional Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }
        
        .print-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 9px;
            color: #6b7280;
            font-style: italic;
            padding: 10px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            margin-top: 30px;
        }
        
        .signature-box {
            text-align: center;
            padding: 15px;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }
        
        .signature-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .signature-line {
            border-bottom: 2px solid #374151;
            margin: 0 20px 10px 20px;
            height: 1px;
        }
        
        .signature-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 10px;
        }
        
        .signature-title {
            font-size: 9px;
            color: #6b7280;
            margin-top: 4px;
            font-style: italic;
        }
        
        .footer-disclaimer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #6b7280;
            line-height: 1.5;
            background: #f9fafb;
            padding: 15px;
            border-radius: 10px;
        }
        
        .footer-disclaimer strong {
            color: #1e40af;
        }
        
        /* No Data Available Style */
        .no-data {
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-style: italic;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border: 1px dashed #d1d5db;
            border-radius: 10px;
        }
        
        /* Print Specific Styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .print-container {
                box-shadow: none;
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-button {
                display: none !important;
            }
        }
        
        /* Responsive Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 11px;
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .print-button:hover {
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.4);
        }
        
        .print-button::before {
            content: '🖨️ ';
            margin-right: 5px;
        }
        
        /* Watermark for authenticity */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(30, 64, 175, 0.03);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <?php if (!isset($preview_mode) || $preview_mode !== true): ?>
    <button class="print-button no-print" onclick="window.print()">Cetak Hasil</button>
    <?php endif; ?>
    
    <!-- Watermark -->
    <div class="watermark no-print">LABSYS</div>
    
    <div class="print-container">
        <!-- Professional Header with Logo -->
        <div class="header">
            <div class="header-content">
                <div class="logo-container">
                    <?php if (isset($logo_info) && $logo_info['logo_exists'] && !empty($logo_info['logo_url'])): ?>
                        <img src="<?php echo $logo_info['logo_url']; ?>" alt="Logo Laboratorium" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="logo-placeholder" style="display:none;">LAB</div>
                    <?php else: ?>
                        <div class="logo-placeholder">LAB</div>
                    <?php endif; ?>
                </div>
                <div class="lab-info">
                    <h1><?php echo isset($lab_info) ? strtoupper($lab_info['nama']) : 'SISTEM INFORMASI LABORATORIUM'; ?></h1>
                    <p class="subtitle"><?php echo isset($lab_info) ? $lab_info['alamat'] : 'Jl. Tata Bumi No.3, Area Sawah, Banyuraden, Kec. Gamping, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55293'; ?></p>
                    <div class="contact-info">
                        <div class="contact-item">
                            <span>📞</span>
                            <span><?php echo isset($lab_info) ? $lab_info['telephone'] : '(021) 123-4567'; ?></span>
                        </div>
                        <div class="contact-item">
                            <span>📧</span>
                            <span><?php echo isset($lab_info) ? $lab_info['email'] : 'info@labsys.com'; ?></span>
                        </div>
                        <div class="contact-item">
                            <span>🌐</span>
                            <span>www.labsys.com</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Document Title -->
        <div class="document-title">
            <h2>HASIL PEMERIKSAAN LABORATORIUM</h2>
            <p class="exam-number">Nomor Pemeriksaan: <strong><?php echo $examination['nomor_pemeriksaan']; ?></strong></p>
        </div>
        
        <!-- Patient & Examination Information -->
        <div class="info-section">
            <div class="info-column">
                <h3>👤 INFORMASI PASIEN</h3>
                <div class="info-row">
                    <span class="info-label">Nama:</span>
                    <span class="info-value"><?php echo htmlspecialchars($examination['nama_pasien']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">NIK:</span>
                    <span class="info-value"><?php echo $examination['nik'] ?: 'Tidak tersedia'; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jenis Kelamin:</span>
                    <span class="info-value"><?php echo ($examination['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan'; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Umur:</span>
                    <span class="info-value"><?php echo $examination['umur'] ?: 'Tidak tersedia'; ?> tahun</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Alamat:</span>
                    <span class="info-value"><?php echo $examination['alamat_domisili'] ?: 'Tidak tersedia'; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Telepon:</span>
                    <span class="info-value"><?php echo $examination['telepon'] ?: 'Tidak tersedia'; ?></span>
                </div>
            </div>
            
            <div class="info-column">
                <h3>🔬 INFORMASI PEMERIKSAAN</h3>
                <div class="info-row">
                    <span class="info-label">Tanggal:</span>
                    <span class="info-value date-highlight"><?php echo date('d F Y', strtotime($examination['tanggal_pemeriksaan'])); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jenis:</span>
                    <span class="info-value"><?php echo $examination['jenis_pemeriksaan']; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dokter Perujuk:</span>
                    <span class="info-value"><?php echo $examination['dokter_perujuk'] ?: 'Tidak tersedia'; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Asal Rujukan:</span>
                    <span class="info-value"><?php echo $examination['asal_rujukan'] ?: 'Tidak tersedia'; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Petugas Lab:</span>
                    <span class="info-value"><?php echo $examination['nama_petugas'] ?: 'Tidak tersedia'; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value status-normal"><?php echo ucfirst($examination['status_pemeriksaan']); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Results Section -->
        <?php if ($results && !empty($results)): ?>
        <div class="results-section">
            <h3 class="results-header">HASIL PEMERIKSAAN <?php echo strtoupper($examination['jenis_pemeriksaan']); ?></h3>
            
            <table class="results-table">
                <thead>
                    <tr>
                        <th style="width: 40%">Parameter</th>
                        <th style="width: 20%">Hasil</th>
                        <th style="width: 20%">Nilai Rujukan</th>
                        <th style="width: 20%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Display results based on examination type
                    if (strtolower($examination['jenis_pemeriksaan']) == 'kimia darah'):
                        $parameters = array(
                            'gula_darah_puasa' => array('name' => 'Gula Darah Puasa', 'unit' => 'mg/dL', 'min' => 70, 'max' => 100),
                            'gula_darah_sewaktu' => array('name' => 'Gula Darah Sewaktu', 'unit' => 'mg/dL', 'min' => 70, 'max' => 140),
                            'gula_darah_2jam_pp' => array('name' => 'Gula Darah 2 Jam PP', 'unit' => 'mg/dL', 'min' => 70, 'max' => 140),
                            'cholesterol_total' => array('name' => 'Kolesterol Total', 'unit' => 'mg/dL', 'min' => 0, 'max' => 200),
                            'cholesterol_hdl' => array('name' => 'Kolesterol HDL', 'unit' => 'mg/dL', 'min' => 40, 'max' => 999),
                            'cholesterol_ldl' => array('name' => 'Kolesterol LDL', 'unit' => 'mg/dL', 'min' => 0, 'max' => 130),
                            'trigliserida' => array('name' => 'Trigliserida', 'unit' => 'mg/dL', 'min' => 0, 'max' => 150),
                            'asam_urat' => array('name' => 'Asam Urat', 'unit' => 'mg/dL', 'min' => 3.5, 'max' => 7.0),
                            'ureum' => array('name' => 'Ureum', 'unit' => 'mg/dL', 'min' => 10, 'max' => 50),
                            'creatinin' => array('name' => 'Kreatinin', 'unit' => 'mg/dL', 'min' => 0.6, 'max' => 1.3),
                            'sgpt' => array('name' => 'SGPT (ALT)', 'unit' => 'U/L', 'min' => 7, 'max' => 56),
                            'sgot' => array('name' => 'SGOT (AST)', 'unit' => 'U/L', 'min' => 10, 'max' => 40)
                        );
                        
                        foreach ($parameters as $key => $param):
                            if (isset($results[$key]) && $results[$key] !== null):
                                $value = $results[$key];
                                $is_normal = ($value >= $param['min'] && $value <= $param['max']);
                                $status_class = $is_normal ? 'status-normal' : ($value > $param['max'] ? 'status-high' : 'status-low');
                                $status_text = $is_normal ? 'Normal' : ($value > $param['max'] ? 'Tinggi' : 'Rendah');
                    ?>
                    <tr>
                        <td><strong><?php echo $param['name']; ?></strong></td>
                        <td style="text-align: center;"><strong><?php echo $value . ' ' . $param['unit']; ?></strong></td>
                        <td style="text-align: center;"><?php echo $param['min'] . ' - ' . $param['max'] . ' ' . $param['unit']; ?></td>
                        <td style="text-align: center;"><span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                    </tr>
                    <?php 
                            endif;
                        endforeach;
                    
                    elseif (strtolower($examination['jenis_pemeriksaan']) == 'hematologi'):
                        // Hematologi parameters
                        if (isset($results['hemoglobin']) && $results['hemoglobin'] !== null):
                            $hb_min = ($examination['jenis_kelamin'] == 'L') ? 14.0 : 12.0;
                            $hb_max = ($examination['jenis_kelamin'] == 'L') ? 18.0 : 16.0;
                            $is_normal = ($results['hemoglobin'] >= $hb_min && $results['hemoglobin'] <= $hb_max);
                    ?>
                    <tr>
                        <td><strong>Hemoglobin</strong></td>
                        <td style="text-align: center;"><strong><?php echo $results['hemoglobin']; ?> g/dL</strong></td>
                        <td style="text-align: center;"><?php echo $hb_min . ' - ' . $hb_max; ?> g/dL</td>
                        <td style="text-align: center;"><span class="<?php echo $is_normal ? 'status-normal' : 'status-low'; ?>"><?php echo $is_normal ? 'Normal' : 'Rendah'; ?></span></td>
                    </tr>
                    <?php endif;
                        
                        if (isset($results['hematokrit']) && $results['hematokrit'] !== null):
                            $ht_min = ($examination['jenis_kelamin'] == 'L') ? 42.0 : 36.0;
                            $ht_max = ($examination['jenis_kelamin'] == 'L') ? 52.0 : 46.0;
                            $is_normal = ($results['hematokrit'] >= $ht_min && $results['hematokrit'] <= $ht_max);
                    ?>
                    <tr>
                        <td><strong>Hematokrit</strong></td>
                        <td style="text-align: center;"><strong><?php echo $results['hematokrit']; ?>%</strong></td>
                        <td style="text-align: center;"><?php echo $ht_min . ' - ' . $ht_max; ?>%</td>
                        <td style="text-align: center;"><span class="<?php echo $is_normal ? 'status-normal' : 'status-low'; ?>"><?php echo $is_normal ? 'Normal' : 'Rendah'; ?></span></td>
                    </tr>
                    <?php endif; 
                        
                        if (isset($results['laju_endap_darah']) && $results['laju_endap_darah'] !== null):
                            $is_normal = ($results['laju_endap_darah'] >= 0 && $results['laju_endap_darah'] <= 20);
                    ?>
                    <tr>
                        <td><strong>Laju Endap Darah (LED)</strong></td>
                        <td style="text-align: center;"><strong><?php echo $results['laju_endap_darah']; ?> mm/jam</strong></td>
                        <td style="text-align: center;">0 - 20 mm/jam</td>
                        <td style="text-align: center;"><span class="<?php echo $is_normal ? 'status-normal' : 'status-high'; ?>"><?php echo $is_normal ? 'Normal' : 'Tinggi'; ?></span></td>
                    </tr>
                    <?php endif;
                        
                        // TAMBAHAN: Clotting Time
                        if (isset($results['clotting_time']) && $results['clotting_time'] !== null): ?>
                    <tr>
                        <td><strong>Clotting Time</strong></td>
                        <td style="text-align: center;"><strong><?php echo $results['clotting_time']; ?></strong></td>
                        <td style="text-align: center;">5-15 menit</td>
                        <td style="text-align: center;"><span class="status-normal">-</span></td>
                    </tr>
                    <?php endif;
                        
                        // TAMBAHAN: Bleeding Time
                        if (isset($results['bleeding_time']) && $results['bleeding_time'] !== null): ?>
                    <tr>
                        <td><strong>Bleeding Time</strong></td>
                        <td style="text-align: center;"><strong><?php echo $results['bleeding_time']; ?></strong></td>
                        <td style="text-align: center;">1-6 menit</td>
                        <td style="text-align: center;"><span class="status-normal">-</span></td>
                    </tr>
                    <?php endif;
                        
                        if (isset($results['golongan_darah']) && $results['golongan_darah'] !== null): ?>
                    <tr>
                        <td><strong>Golongan Darah</strong></td>
                        <td style="text-align: center;"><strong><?php echo $results['golongan_darah'] . ($results['rhesus'] ?: ''); ?></strong></td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: center;"><span class="status-normal">Normal</span></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if (isset($results['malaria']) && $results['malaria'] !== null): ?>
                    <tr>
                        <td><strong>Malaria</strong></td>
                        <td colspan="3" style="text-align: center;"><strong><?php echo htmlspecialchars($results['malaria']); ?></strong></td>
                    </tr>
                    <?php endif;
                    
                    elseif (strtolower($examination['jenis_pemeriksaan']) == 'urinologi'):
                        // Urinologi parameters
                        $urin_parameters = array(
                            'warna' => array('name' => 'Warna', 'unit' => '', 'normal' => 'Kuning muda'),
                            'kejernihan' => array('name' => 'Kejernihan', 'unit' => '', 'normal' => 'Jernih'),
                            'berat_jenis' => array('name' => 'Berat Jenis', 'unit' => '', 'min' => 1.003, 'max' => 1.030),
                            'ph' => array('name' => 'pH', 'unit' => '', 'min' => 4.5, 'max' => 8.0),
                            'protein' => array('name' => 'Protein', 'unit' => '', 'normal' => 'Negatif'),
                            'glukosa' => array('name' => 'Glukosa', 'unit' => '', 'normal' => 'Negatif'),
                            'keton' => array('name' => 'Keton', 'unit' => '', 'normal' => 'Negatif'),
                            'urobilinogen' => array('name' => 'Urobilinogen', 'unit' => '', 'normal' => 'Normal'),
                            'bilirubin' => array('name' => 'Bilirubin', 'unit' => '', 'normal' => 'Negatif'),
                            'nitrit' => array('name' => 'Nitrit', 'unit' => '', 'normal' => 'Negatif'),
                            'leukosit_esterase' => array('name' => 'Leukosit Esterase', 'unit' => '', 'normal' => 'Negatif'),
                            'eritrosit' => array('name' => 'Eritrosit', 'unit' => '/lpb', 'min' => 0, 'max' => 3),
                            'leukosit' => array('name' => 'Leukosit', 'unit' => '/lpb', 'min' => 0, 'max' => 5),
                            'epitel' => array('name' => 'Epitel', 'unit' => '/lpb', 'min' => 0, 'max' => 5),
                            'bakteri' => array('name' => 'Bakteri', 'unit' => '', 'normal' => 'Sedikit'),
                            'kristal' => array('name' => 'Kristal', 'unit' => '', 'normal' => 'Negatif'),
                            'silinder' => array('name' => 'Silinder', 'unit' => '/lpb', 'normal' => 'Negatif')
                        );
                        
                        foreach ($urin_parameters as $key => $param):
                            if (isset($results[$key]) && $results[$key] !== null && $results[$key] !== ''):
                                $value = $results[$key];
                                $reference = '';
                                
                                // Determine status
                                if (isset($param['normal'])) {
                                    $is_normal = (strtolower($value) == strtolower($param['normal']));
                                    $status_text = $is_normal ? 'Normal' : 'Abnormal';
                                    $status_class = $is_normal ? 'status-normal' : 'status-abnormal';
                                    $reference = $param['normal'];
                                } elseif (isset($param['min']) && isset($param['max'])) {
                                    $is_normal = ($value >= $param['min'] && $value <= $param['max']);
                                    $status_text = $is_normal ? 'Normal' : ($value > $param['max'] ? 'Tinggi' : 'Rendah');
                                    $status_class = $is_normal ? 'status-normal' : ($value > $param['max'] ? 'status-high' : 'status-low');
                                    $reference = $param['min'] . ' - ' . $param['max'] . ' ' . $param['unit'];
                                } else {
                                    $status_text = 'Normal';
                                    $status_class = 'status-normal';
                                    $reference = '-';
                                }
                    ?>
                    <tr>
                        <td><strong><?php echo $param['name']; ?></strong></td>
                        <td style="text-align: center;"><strong><?php echo $value . ' ' . $param['unit']; ?></strong></td>
                        <td style="text-align: center;"><?php echo $reference; ?></td>
                        <td style="text-align: center;"><span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                    </tr>
                    <?php 
                            endif;
                        endforeach;
                        
                    elseif (strtolower($examination['jenis_pemeriksaan']) == 'serologi' || strtolower($examination['jenis_pemeriksaan']) == 'serologi imunologi'):
                        // TAMBAHAN: RDT Antigen
                        if (isset($results['rdt_antigen']) && $results['rdt_antigen'] !== null): 
                            $is_normal = (strtolower($results['rdt_antigen']) == 'negatif');
                        ?>
                    <tr>
                        <td><strong>RDT Antigen</strong></td>
                        <td style="text-align: center;"><strong><?php echo $results['rdt_antigen']; ?></strong></td>
                        <td style="text-align: center;">Negatif</td>
                        <td style="text-align: center;"><span class="<?php echo $is_normal ? 'status-normal' : 'status-abnormal'; ?>"><?php echo $is_normal ? 'Normal' : 'Abnormal'; ?></span></td>
                    </tr>
                    <?php endif;
                        
                        // TAMBAHAN: Widal (general field)
                        if (isset($results['widal']) && $results['widal'] !== null && $results['widal'] !== ''): ?>
                    <tr>
                        <td><strong>Widal</strong></td>
                        <td colspan="3" style="text-align: center;"><strong><?php echo htmlspecialchars($results['widal']); ?></strong></td>
                    </tr>
                    <?php endif;
                    
                        // Serologi Imunologi parameters
                        $serologi_parameters = array(
                            'hbsag' => array('name' => 'HBsAg', 'unit' => '', 'normal' => 'Non Reaktif'),
                            'anti_hbs' => array('name' => 'Anti HBs', 'unit' => '', 'normal' => 'Reaktif'),
                            'anti_hcv' => array('name' => 'Anti HCV', 'unit' => '', 'normal' => 'Non Reaktif'),
                            'anti_hiv' => array('name' => 'Anti HIV', 'unit' => '', 'normal' => 'Non Reaktif'),
                            'hiv' => array('name' => 'HIV', 'unit' => '', 'normal' => 'Non Reaktif'),
                            'vdrl' => array('name' => 'VDRL', 'unit' => '', 'normal' => 'Non Reaktif'),
                            'widal_s_typhi_o' => array('name' => 'Widal S.Typhi O', 'unit' => '', 'normal' => '< 1/80'),
                            'widal_s_typhi_h' => array('name' => 'Widal S.Typhi H', 'unit' => '', 'normal' => '< 1/80'),
                            'widal_s_paratyphi_a_h' => array('name' => 'Widal S.Paratyphi A H', 'unit' => '', 'normal' => '< 1/80'),
                            'widal_s_paratyphi_b_h' => array('name' => 'Widal S.Paratyphi B H', 'unit' => '', 'normal' => '< 1/80'),
                            'ns1_antigen' => array('name' => 'NS1 Antigen', 'unit' => '', 'normal' => 'Non Reaktif'),
                            'ns1' => array('name' => 'NS1 Antigen', 'unit' => '', 'normal' => 'Negatif'),
                            'igm_dengue' => array('name' => 'IgM Dengue', 'unit' => '', 'normal' => 'Non Reaktif'),
                            'igg_dengue' => array('name' => 'IgG Dengue', 'unit' => '', 'normal' => 'Non Reaktif')
                        );
                        
                        foreach ($serologi_parameters as $key => $param):
                            if (isset($results[$key]) && $results[$key] !== null && $results[$key] !== ''):
                                $value = $results[$key];
                                $is_normal = (strtolower($value) == strtolower($param['normal']) || 
                                            (strpos(strtolower($param['normal']), '<') !== false && strpos(strtolower($value), '<') !== false));
                                $status_class = $is_normal ? 'status-normal' : 'status-abnormal';
                                $status_text = $is_normal ? 'Normal' : 'Abnormal';
                    ?>
                    <tr>
                        <td><strong><?php echo $param['name']; ?></strong></td>
                        <td style="text-align: center;"><strong><?php echo $value; ?></strong></td>
                        <td style="text-align: center;"><?php echo $param['normal']; ?></td>
                        <td style="text-align: center;"><span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                    </tr>
                    <?php 
                            endif;
                        endforeach;
                        
                    elseif (strtolower($examination['jenis_pemeriksaan']) == 'tbc'):
                        // TBC parameters - PERBAIKAN MAPPING
                        if (isset($results['dahak']) && $results['dahak'] !== null): 
                            $is_normal = (strtolower($results['dahak']) == 'negatif');
                        ?>
                    <tr>
                        <td><strong>Pemeriksaan Dahak (BTA)</strong></td>
                        <td style="text-align: center;"><strong><?php echo $results['dahak']; ?></strong></td>
                        <td style="text-align: center;">Negatif</td>
                        <td style="text-align: center;">
                            <span class="<?php echo $is_normal ? 'status-normal' : 'status-abnormal'; ?>">
                                <?php echo $is_normal ? 'Normal' : 'Positif'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endif;
                        
                        if (isset($results['tcm']) && $results['tcm'] !== null): 
                            $is_normal = (strtolower($results['tcm']) == 'not detected');
                        ?>
                    <tr>
                        <td><strong>TCM (GeneXpert)</strong></td>
                        <td style="text-align: center;"><strong><?php echo $results['tcm']; ?></strong></td>
                        <td style="text-align: center;">Not Detected</td>
                        <td style="text-align: center;">
                            <span class="<?php echo $is_normal ? 'status-normal' : 'status-abnormal'; ?>">
                                <?php echo $is_normal ? 'Normal' : 'Terdeteksi'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endif;
                        
                    elseif (strtolower($examination['jenis_pemeriksaan']) == 'ims'):
                        // TAMBAHAN: Sifilis
                        if (isset($results['sifilis']) && $results['sifilis'] !== null): 
                            $is_normal = (strtolower($results['sifilis']) == 'non-reaktif');
                        ?>
                    <tr>
                        <td><strong>Sifilis</strong></td>
                        <td style="text-align: center;"><strong><?php echo $results['sifilis']; ?></strong></td>
                        <td style="text-align: center;">Non-Reaktif</td>
                        <td style="text-align: center;"><span class="<?php echo $is_normal ? 'status-normal' : 'status-abnormal'; ?>"><?php echo $is_normal ? 'Normal' : 'Abnormal'; ?></span></td>
                    </tr>
                    <?php endif;
                        
                        // TAMBAHAN: Duh Tubuh
                        if (isset($results['duh_tubuh']) && $results['duh_tubuh'] !== null && $results['duh_tubuh'] !== ''): ?>
                    <tr>
                        <td><strong>Duh Tubuh</strong></td>
                        <td colspan="3" style="text-align: left;"><?php echo htmlspecialchars($results['duh_tubuh']); ?></td>
                    </tr>
                    <?php endif;
                        
                        // IMS parameters  
                        $ims_parameters = array(
                            'gram_staining' => array('name' => 'Gram Staining', 'normal' => 'Normal'),
                            'candida' => array('name' => 'Candida', 'normal' => 'Negatif'),
                            'trichomonas' => array('name' => 'Trichomonas', 'normal' => 'Negatif'),
                            'clue_cells' => array('name' => 'Clue Cells', 'normal' => 'Negatif'),
                            'bakteri_kokus' => array('name' => 'Bakteri Kokus', 'normal' => 'Negatif'),
                            'bakteri_batang' => array('name' => 'Bakteri Batang', 'normal' => 'Negatif')
                        );
                        
                        foreach ($ims_parameters as $key => $param):
                            if (isset($results[$key]) && $results[$key] !== null && $results[$key] !== ''):
                                $value = $results[$key];
                                $is_normal = (strtolower($value) == strtolower($param['normal']));
                                $status_class = $is_normal ? 'status-normal' : 'status-abnormal';
                                $status_text = $is_normal ? 'Normal' : 'Abnormal';
                    ?>
                    <tr>
                        <td><strong><?php echo $param['name']; ?></strong></td>
                        <td style="text-align: center;"><strong><?php echo $value; ?></strong></td>
                        <td style="text-align: center;"><?php echo $param['normal']; ?></td>
                        <td style="text-align: center;"><span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                    </tr>
                    <?php 
                            endif;
                        endforeach;
                        
                    elseif (strtolower($examination['jenis_pemeriksaan']) == 'mls'):
                        // MLS parameters - PERBAIKAN STRUKTUR
                        if (isset($results['jenis_tes']) && $results['jenis_tes'] !== null): ?>
                    <tr>
                        <td><strong>Jenis Tes</strong></td>
                        <td colspan="3" style="text-align: center;"><strong><?php echo htmlspecialchars($results['jenis_tes']); ?></strong></td>
                    </tr>
                    <?php endif;
                        
                        if (isset($results['hasil']) && $results['hasil'] !== null): ?>
                    <tr>
                        <td><strong>Hasil</strong></td>
                        <td style="text-align: center;"><strong><?php echo htmlspecialchars($results['hasil']); ?></strong></td>
                        <td style="text-align: center;"><?php echo isset($results['nilai_rujukan']) ? htmlspecialchars($results['nilai_rujukan']) : '-'; ?></td>
                        <td style="text-align: center;"><span class="status-normal">-</span></td>
                    </tr>
                    <?php endif;
                        
                        if (isset($results['satuan']) && $results['satuan'] !== null && $results['satuan'] !== ''): ?>
                    <tr>
                        <td><strong>Satuan</strong></td>
                        <td colspan="3" style="text-align: center;"><?php echo htmlspecialchars($results['satuan']); ?></td>
                    </tr>
                    <?php endif;
                        
                        if (isset($results['metode']) && $results['metode'] !== null && $results['metode'] !== ''): ?>
                    <tr>
                        <td><strong>Metode</strong></td>
                        <td colspan="3" style="text-align: left;"><?php echo htmlspecialchars($results['metode']); ?></td>
                    </tr>
                    <?php endif;
                    
                    else:
                        // Generic handling untuk jenis pemeriksaan yang belum terdefinisi
                        if ($results && is_array($results)):
                            foreach ($results as $key => $value):
                                if ($key != 'pemeriksaan_id' && $value !== null && $value !== ''):
                                    $field_name = ucwords(str_replace('_', ' ', $key));
                    ?>
                    <tr>
                        <td><strong><?php echo $field_name; ?></strong></td>
                        <td style="text-align: center;"><strong><?php echo $value; ?></strong></td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: center;"><span class="status-normal">-</span></td>
                    </tr>
                    <?php 
                                endif;
                            endforeach;
                        endif;
                    endif; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="results-section">
            <h3 class="results-header">HASIL PEMERIKSAAN <?php echo strtoupper($examination['jenis_pemeriksaan']); ?></h3>
            <div class="no-data">
                Data hasil pemeriksaan belum tersedia atau belum diinputkan.
            </div>
        </div>
        <?php endif; ?>
        
        <div>
            <div style="height:30px;"></div>
        <div class="footer">
            <div class="print-info">
                📄 Hasil dicetak pada: <strong><?php echo isset($print_date) ? $print_date : date('d F Y, H:i:s'); ?> WIB</strong>
                <br>
                👤 Dicetak oleh: <strong><?php echo $this->session->userdata('username'); ?></strong>
            </div>
            
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-label"> Diperiksa oleh:</div>
                    <div class="signature-name"><?php echo $examination['nama_petugas'] ?: 'Petugas Laboratorium'; ?></div>
                    <div class="signature-title">Analis Laboratorium</div>
                </div>
            </div>
            
            <div class="footer-disclaimer">
                📝 <strong>Dokumen ini dicetak secara otomatis oleh sistem dan sah tanpa tanda tangan basah.</strong><br>
                📞 Untuk verifikasi keaslian, silakan hubungi laboratorium dengan nomor pemeriksaan: <strong><?php echo $examination['nomor_pemeriksaan']; ?></strong><br>
                🏥 <em><strong>Sistem Informasi Laboratorium - Terpercaya, Akurat, Profesional</strong></em>
            </div>
        </div>
    </div>
    
    <script>
        // Enhanced print functionality
        function printResult() {
            // Add print timestamp before printing
            const printInfo = document.querySelector('.print-info');
            if (printInfo) {
                const now = new Date();
                const formattedDate = now.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                printInfo.innerHTML = `📄 Hasil dicetak pada: <strong>${formattedDate} WIB</strong>`;
            }
            
            window.print();
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                printResult();
            }
        });
        
        // Print button functionality
        const printButton = document.querySelector('.print-button');
        if (printButton) {
            printButton.addEventListener('click', printResult);
        }
        
        // Auto-focus for accessibility
        document.addEventListener('DOMContentLoaded', function() {
            // Set document title for better identification
            document.title = 'Hasil Lab - <?php echo $examination['nomor_pemeriksaan']; ?> - <?php echo $examination['nama_pasien']; ?>';
        });
    </script>
</body>
</html>