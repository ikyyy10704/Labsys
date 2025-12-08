<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
            padding: 0;
        }
        
        .invoice-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm;
            background: white;
        }
        
        /* Header Section */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 4px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .header-left {
            display: table-cell;
            width: 65%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            text-align: right;
        }
        
        .logo-section {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            flex-shrink: 0;
        }
        
        .logo-placeholder {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            font-weight: bold;
            flex-shrink: 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .lab-info h1 {
            font-size: 26px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 8px;
            line-height: 1.2;
        }
        
        .lab-info p {
            font-size: 13px;
            color: #666;
            margin: 3px 0;
            line-height: 1.4;
        }
        
        .lab-info .contact-info {
            margin-top: 5px;
            font-size: 12px;
        }
        
        .invoice-title h2 {
            font-size: 32px;
            color: #2563eb;
            margin-bottom: 10px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .invoice-number {
            font-size: 14px;
            color: #666;
            line-height: 1.8;
        }
        
        .invoice-number strong {
            color: #1f2937;
            font-size: 15px;
        }
        
        /* Invoice Details Grid */
        .invoice-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-spacing: 20px 0;
        }
        
        .detail-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 20px;
            border-radius: 8px;
        }
        
        .detail-section.patient {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
        }
        
        .detail-section.invoice {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
        }
        
        .detail-section h3 {
            font-size: 15px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 8px;
        }
        
        .detail-item {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            font-size: 13px;
        }
        
        .detail-label {
            display: table-cell;
            font-weight: 600;
            width: 45%;
            color: #4b5563;
            padding-right: 10px;
        }
        
        .detail-value {
            display: table-cell;
            color: #111827;
            font-weight: 500;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-lunas {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        
        .status-belum-bayar {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        .status-cicilan {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }
        
        /* Breakdown Header */
        .breakdown-header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 12px 15px;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 0;
            border-radius: 8px 8px 0 0;
        }
        
        /* Invoice Table */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 8px 8px;
            overflow: hidden;
        }
        
        .invoice-table thead {
            background-color: #1e40af;
        }
        
        .invoice-table th {
            color: white;
            font-weight: 600;
            padding: 14px 15px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .invoice-table th.text-right {
            text-align: right;
        }
        
        .invoice-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }
        
        .invoice-table tbody tr {
            background-color: white;
        }
        
        .invoice-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .invoice-table tbody tr:hover {
            background-color: #f3f4f6;
        }
        
        .invoice-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .invoice-table .item-name {
            font-weight: 600;
            color: #1f2937;
        }
        
        .invoice-table .item-result {
            color: #059669;
            font-weight: 500;
        }
        
        .amount {
            text-align: right;
            font-weight: 600;
            color: #1f2937;
        }
        
        .empty-breakdown {
            text-align: center;
            color: #9ca3af;
            padding: 40px 20px;
            font-style: italic;
            background-color: #f9fafb;
        }
        
        /* Summary Section */
        .summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        
        .summary-table {
            width: 400px;
            border: 2px solid #d1d5db;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .summary-row {
            display: table;
            width: 100%;
            padding: 12px 20px;
            font-size: 14px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .summary-row:last-child {
            border-bottom: none;
        }
        
        .summary-row .label {
            display: table-cell;
            font-weight: 500;
            color: #4b5563;
        }
        
        .summary-row .value {
            display: table-cell;
            text-align: right;
            font-weight: 600;
            color: #1f2937;
        }
        
        .summary-row.subtotal {
            background-color: #f9fafb;
        }
        
        .summary-row.total {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 16px 20px;
        }
        
        .summary-row.total .label,
        .summary-row.total .value {
            color: white;
            font-size: 16px;
            font-weight: bold;
        }
        
        .summary-row.total .value {
            font-size: 20px;
        }
        
        /* Additional Info Sections */
        .info-section {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid;
        }
        
        .info-section.payment {
            background-color: #eff6ff;
            border-color: #bfdbfe;
        }
        
        .info-section.bpjs {
            background-color: #fef3c7;
            border-color: #fcd34d;
        }
        
        .info-section.notes {
            background-color: #fef9c3;
            border-color: #fde047;
            border-left-width: 4px;
        }
        
        .info-section h3 {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-grid .info-item {
            display: table-row;
        }
        
        .info-grid .info-label {
            display: table-cell;
            font-weight: 600;
            width: 45%;
            padding: 5px 10px 5px 0;
            color: #4b5563;
            font-size: 13px;
        }
        
        .info-grid .info-value {
            display: table-cell;
            color: #111827;
            padding: 5px 0;
            font-weight: 500;
            font-size: 13px;
        }
        
        .notes-text {
            font-size: 13px;
            color: #854d0e;
            line-height: 1.6;
            white-space: pre-wrap;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding-top: 30px;
            border-top: 2px solid #e5e7eb;
            margin-top: 40px;
        }
        
        .footer-main {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 8px;
        }
        
        .footer-message {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 20px;
        }
        
        .print-info {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
        }
        
        .print-info p {
            font-size: 11px;
            color: #9ca3af;
            margin: 4px 0;
        }
        
        /* Print Button */
        .print-button {
            position: fixed;
            top: 30px;
            right: 30px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.4);
            transition: all 0.3s ease;
        }
        
        .print-button:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.5);
        }
        
        .print-button:active {
            transform: translateY(0);
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            
            .invoice-container {
                max-width: none;
                margin: 0;
                padding: 10mm;
                box-shadow: none;
            }
            
            .no-print {
                display: none !important;
            }
            
            .header {
                page-break-after: avoid;
            }
            
            .invoice-table {
                page-break-inside: auto;
            }
            
            .invoice-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            .invoice-table thead {
                display: table-header-group;
            }
            
            .summary {
                page-break-inside: avoid;
            }
            
            .info-section {
                page-break-inside: avoid;
            }
            
            .footer {
                page-break-before: avoid;
            }
            
            @page {
                margin: 15mm;
                size: A4;
            }
            
            /* Ensure colors print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        
        /* Responsive for screen preview */
        @media screen and (max-width: 800px) {
            .invoice-container {
                padding: 15px;
            }
            
            .header-left,
            .header-right {
                display: block;
                width: 100%;
            }
            
            .header-right {
                text-align: left;
                margin-top: 20px;
            }
            
            .detail-section {
                display: block;
                width: 100%;
                margin-bottom: 15px;
            }
            
            .summary-table {
                width: 100%;
            }
            
            .print-button {
                top: 10px;
                right: 10px;
                padding: 10px 20px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Print Invoice
    </button>
    
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="logo-section">
                    <?php if (!empty($logo_info['logo_exists']) && $logo_info['logo_exists']): ?>
                        <img src="<?= htmlspecialchars($logo_info['logo_url']) ?>" alt="Logo Laboratorium" class="logo">
                    <?php else: ?>
                        <div class="logo-placeholder">LAB</div>
                    <?php endif; ?>
                    
                    <div class="lab-info">
                        <h1><?= htmlspecialchars($lab_info['nama']) ?></h1>
                        <p><?= htmlspecialchars($lab_info['alamat']) ?></p>
                        <div class="contact-info">
                            <p>📞 Telepon: <?= htmlspecialchars($lab_info['telephone']) ?></p>
                            <p>📧 Email: <?= htmlspecialchars($lab_info['email']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="header-right">
                <div class="invoice-title">
                    <h2>INVOICE</h2>
                    <div class="invoice-number">
                        <strong><?= htmlspecialchars($invoice['nomor_invoice']) ?></strong><br>
                        Tanggal: <?= date('d F Y', strtotime($invoice['tanggal_invoice'])) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Invoice Details -->
        <div class="invoice-details">
            <!-- Patient Information -->
            <div class="detail-section patient">
                <h3>👤 Informasi Pasien</h3>
                <div class="detail-item">
                    <span class="detail-label">Nama Lengkap:</span>
                    <span class="detail-value"><?= htmlspecialchars($invoice['nama_pasien']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">NIK:</span>
                    <span class="detail-value"><?= htmlspecialchars($invoice['nik'] ?: 'Tidak tersedia') ?></span>
                </div>
                <?php if (!empty($invoice['umur'])): ?>
                <div class="detail-item">
                    <span class="detail-label">Umur:</span>
                    <span class="detail-value"><?= htmlspecialchars($invoice['umur']) ?> tahun</span>
                </div>
                <?php endif; ?>
                <?php if (!empty($invoice['alamat_domisili'])): ?>
                <div class="detail-item">
                    <span class="detail-label">Alamat:</span>
                    <span class="detail-value"><?= htmlspecialchars($invoice['alamat_domisili']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($invoice['telepon'])): ?>
                <div class="detail-item">
                    <span class="detail-label">Telepon:</span>
                    <span class="detail-value"><?= htmlspecialchars($invoice['telepon']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Invoice Information -->
            <div class="detail-section invoice">
                <h3>📋 Informasi Invoice</h3>
                <div class="detail-item">
                    <span class="detail-label">No. Pemeriksaan:</span>
                    <span class="detail-value"><?= htmlspecialchars($invoice['nomor_pemeriksaan']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Jenis Pemeriksaan:</span>
                    <span class="detail-value"><?= htmlspecialchars($invoice['jenis_pemeriksaan']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Tanggal Pemeriksaan:</span>
                    <span class="detail-value"><?= date('d F Y', strtotime($invoice['tanggal_pemeriksaan'])) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Jenis Pembayaran:</span>
                    <span class="detail-value"><?= strtoupper(htmlspecialchars($invoice['jenis_pembayaran'])) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status Pembayaran:</span>
                    <span class="detail-value">
                        <?php
                        $status_class = 'status-' . str_replace('_', '-', $invoice['status_pembayaran']);
                        $status_text = ucfirst(str_replace('_', ' ', $invoice['status_pembayaran']));
                        ?>
                        <span class="status-badge <?= $status_class ?>">
                            <?= htmlspecialchars($status_text) ?>
                        </span>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Service Details Table with Breakdown -->
        <div class="breakdown-header">
            RINCIAN BIAYA PEMERIKSAAN
        </div>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Item Pemeriksaan</th>
                    <th style="width: 25%;">Hasil Pemeriksaan</th>
                    <th class="text-right" style="width: 25%;">Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($invoice['cost_breakdown']) && is_array($invoice['cost_breakdown']) && count($invoice['cost_breakdown']) > 0): ?>
                    <?php foreach ($invoice['cost_breakdown'] as $index => $item): ?>
                        <tr>
                            <td>
                                <span class="item-name"><?= htmlspecialchars($item['item']) ?></span>
                            </td>
                            <td>
                                <span class="item-result"><?= htmlspecialchars($item['hasil'] ?? '-') ?></span>
                            </td>
                            <td class="amount">
                                Rp <?= number_format($item['harga'], 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="empty-breakdown">
                            Belum ada detail breakdown biaya pemeriksaan.<br>
                            <small>Hasil pemeriksaan mungkin belum diinput atau invoice menggunakan sistem lama.</small>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="summary">
            <div class="summary-table">
                <div class="summary-row subtotal">
                    <span class="label">Subtotal:</span>
                    <span class="value">Rp <?= number_format($invoice['total_biaya'], 0, ',', '.') ?></span>
                </div>
                <div class="summary-row">
                    <span class="label">Diskon:</span>
                    <span class="value">Rp 0</span>
                </div>
                <div class="summary-row">
                    <span class="label">Pajak (0%):</span>
                    <span class="value">Rp 0</span>
                </div>
                <div class="summary-row total">
                    <span class="label">TOTAL BIAYA:</span>
                    <span class="value">Rp <?= number_format($invoice['total_biaya'], 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
        
        <!-- Payment Information (if paid) -->
        <?php if ($invoice['status_pembayaran'] === 'lunas'): ?>
        <div class="info-section payment">
            <h3>Informasi Pembayaran</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Metode Pembayaran:</span>
                    <span class="info-value"><?= htmlspecialchars($invoice['metode_pembayaran'] ?: 'Tidak ditentukan') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Pembayaran:</span>
                    <span class="info-value">
                        <?= $invoice['tanggal_pembayaran'] ? date('d F Y', strtotime($invoice['tanggal_pembayaran'])) : 'Tidak tersedia' ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><strong>LUNAS</strong></span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- BPJS Information -->
        <?php if ($invoice['jenis_pembayaran'] === 'bpjs'): ?>
        <div class="info-section bpjs">
            <h3>🏥 Informasi BPJS</h3>
            <div class="info-grid">
                <?php if (!empty($invoice['nomor_kartu_bpjs'])): ?>
                <div class="info-item">
                    <span class="info-label">Nomor Kartu BPJS:</span>
                    <span class="info-value"><?= htmlspecialchars($invoice['nomor_kartu_bpjs']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($invoice['nomor_sep'])): ?>
                <div class="info-item">
                    <span class="info-label">Nomor SEP:</span>
                    <span class="info-value"><?= htmlspecialchars($invoice['nomor_sep']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (empty($invoice['nomor_kartu_bpjs']) && empty($invoice['nomor_sep'])): ?>
                <div class="info-item">
                    <span class="info-value" style="font-style: italic; color: #9ca3af;">
                        Informasi BPJS tidak tersedia
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Notes (if any) -->
        <?php if (!empty($invoice['keterangan'])): ?>
        <div class="info-section notes">
            <h3>📌 Catatan</h3>
            <div class="notes-text"><?= nl2br(htmlspecialchars($invoice['keterangan'])) ?></div>
        </div>
        <?php endif; ?>
        
        <!-- Footer -->
        <div class="footer">
            <p class="footer-main"><?= htmlspecialchars($lab_info['nama']) ?></p>
            <p class="footer-message">
                Terima kasih atas kepercayaan Anda menggunakan layanan kami.<br>
                Semoga cepat sembuh dan sehat selalu! 🌟
            </p>
            
            <div class="print-info">
                <p>📄 Invoice dicetak pada: <strong><?= htmlspecialchars($print_date) ?></strong></p>
                <p>👤 Dicetak oleh: <strong><?= htmlspecialchars($current_user) ?></strong></p>
                <p>💻 Sistem Informasi Laboratorium - <strong>LabSy</strong> v1.0</p>
                <p style="margin-top: 10px; color: #d1d5db;">
                    Dokumen ini dicetak secara otomatis dan sah tanpa tanda tangan
                </p>
            </div>
        </div>
    </div>
    
    <script>
        // Print function
        function printInvoice() {
            window.print();
        }
        
        // Auto focus for better print experience
        window.addEventListener('load', function() {
            // Optional: Auto print after load (uncomment if needed)
            // setTimeout(function() {
            //     window.print();
            // }, 500);
        });
        
        // Handle after print event
        window.addEventListener('afterprint', function() {
            console.log('Invoice printed successfully');
            // Optional: Close window after print (uncomment if needed)
            // window.close();
        });
        
        // Handle keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+P or Cmd+P
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            
            // ESC to close (if opened in new window)
            if (e.key === 'Escape') {
                window.close();
            }
        });
    </script>
</body>
</html>