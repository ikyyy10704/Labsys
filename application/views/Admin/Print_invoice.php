<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
        }
        
        .invoice-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        
        .lab-info h1 {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .lab-info p {
            font-size: 12px;
            color: #666;
            margin: 2px 0;
        }
        
        .invoice-title {
            text-align: right;
        }
        
        .invoice-title h2 {
            font-size: 28px;
            color: #2563eb;
            margin-bottom: 10px;
        }
        
        .invoice-number {
            font-size: 14px;
            color: #666;
        }
        
        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 30px;
        }
        
        .detail-section h3 {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        .detail-item {
            display: flex;
            margin-bottom: 8px;
            font-size: 13px;
        }
        
        .detail-label {
            font-weight: bold;
            min-width: 120px;
            color: #374151;
        }
        
        .detail-value {
            flex: 1;
            color: #111827;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-lunas {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-belum-bayar {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .status-cicilan {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .invoice-table th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .invoice-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }
        
        .invoice-table tbody tr:hover {
            background-color: #f9fafb;
        }
        
        .amount {
            text-align: right;
            font-weight: bold;
        }
        
        .summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        
        .summary-table {
            width: 300px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 13px;
        }
        
        .summary-row.total {
            border-top: 2px solid #2563eb;
            font-weight: bold;
            font-size: 16px;
            color: #2563eb;
            padding-top: 12px;
        }
        
        .payment-info {
            background-color: #f3f4f6;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .payment-info h3 {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .payment-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .notes {
            background-color: #dbeafe;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin-bottom: 30px;
        }
        
        .notes h4 {
            font-size: 13px;
            font-weight: bold;
            color: #1d4ed8;
            margin-bottom: 8px;
        }
        
        .notes p {
            font-size: 12px;
            color: #1e40af;
        }
        
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 11px;
        }
        
        .print-info {
            margin-top: 20px;
            font-size: 10px;
            color: #9ca3af;
        }
        
        @media print {
            body {
                background: white;
            }
            
            .invoice-container {
                max-width: none;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            
            .no-print {
                display: none !important;
            }
            
            @page {
                margin: 1cm;
                size: A4;
            }
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .print-button:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">📄 Print Invoice</button>
    
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <?php if ($logo_info['logo_exists']): ?>
                    <img src="<?= $logo_info['logo_url'] ?>" alt="Logo" class="logo">
                <?php else: ?>
                    <div style="width: 80px; height: 80px; background-color: #2563eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">
                        LAB
                    </div>
                <?php endif; ?>
                
                <div class="lab-info">
                    <h1><?= $lab_info['nama'] ?></h1>
                    <p><?= $lab_info['alamat'] ?></p>
                    <p>Telepon: <?= $lab_info['telephone'] ?></p>
                    <p>Email: <?= $lab_info['email'] ?></p>
                </div>
            </div>
            
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <div class="invoice-number">
                    <strong><?= $invoice['nomor_invoice'] ?></strong><br>
                    Tanggal: <?= date('d F Y', strtotime($invoice['tanggal_invoice'])) ?>
                </div>
            </div>
        </div>
        
        <!-- Invoice Details -->
        <div class="invoice-details">
            <!-- Patient Information -->
            <div class="detail-section">
                <h3>Informasi Pasien</h3>
                <div class="detail-item">
                    <span class="detail-label">Nama:</span>
                    <span class="detail-value"><?= $invoice['nama_pasien'] ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">NIK:</span>
                    <span class="detail-value"><?= $invoice['nik'] ?: 'Tidak tersedia' ?></span>
                </div>
                <?php if (!empty($invoice['umur'])): ?>
                <div class="detail-item">
                    <span class="detail-label">Umur:</span>
                    <span class="detail-value"><?= $invoice['umur'] ?> tahun</span>
                </div>
                <?php endif; ?>
                <?php if (!empty($invoice['alamat_domisili'])): ?>
                <div class="detail-item">
                    <span class="detail-label">Alamat:</span>
                    <span class="detail-value"><?= $invoice['alamat_domisili'] ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($invoice['telepon'])): ?>
                <div class="detail-item">
                    <span class="detail-label">Telepon:</span>
                    <span class="detail-value"><?= $invoice['telepon'] ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Invoice Information -->
            <div class="detail-section">
                <h3>Informasi Invoice</h3>
                <div class="detail-item">
                    <span class="detail-label">No. Pemeriksaan:</span>
                    <span class="detail-value"><?= $invoice['nomor_pemeriksaan'] ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Jenis Pemeriksaan:</span>
                    <span class="detail-value"><?= $invoice['jenis_pemeriksaan'] ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Tanggal Pemeriksaan:</span>
                    <span class="detail-value"><?= date('d F Y', strtotime($invoice['tanggal_pemeriksaan'])) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Jenis Pembayaran:</span>
                    <span class="detail-value"><?= strtoupper($invoice['jenis_pembayaran']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <?php
                        $status_class = 'status-' . str_replace('_', '-', $invoice['status_pembayaran']);
                        $status_text = ucfirst(str_replace('_', ' ', $invoice['status_pembayaran']));
                        ?>
                        <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Service Details Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Deskripsi Layanan</th>
                    <th>Tanggal</th>
                    <th>Qty</th>
                    <th>Harga Satuan</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?= $invoice['jenis_pemeriksaan'] ?></strong><br>
                        <small style="color: #6b7280;">No. Pemeriksaan: <?= $invoice['nomor_pemeriksaan'] ?></small>
                    </td>
                    <td><?= date('d/m/Y', strtotime($invoice['tanggal_pemeriksaan'])) ?></td>
                    <td style="text-align: center;">1</td>
                    <td class="amount">Rp <?= number_format($invoice['total_biaya'], 0, ',', '.') ?></td>
                    <td class="amount">Rp <?= number_format($invoice['total_biaya'], 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="summary">
            <div class="summary-table">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>Rp <?= number_format($invoice['total_biaya'], 0, ',', '.') ?></span>
                </div>
                <div class="summary-row">
                    <span>Pajak (0%):</span>
                    <span>Rp 0</span>
                </div>
                <div class="summary-row total">
                    <span>TOTAL:</span>
                    <span>Rp <?= number_format($invoice['total_biaya'], 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
        
        <!-- Payment Information -->
        <?php if ($invoice['status_pembayaran'] === 'lunas'): ?>
        <div class="payment-info">
            <h3>Informasi Pembayaran</h3>
            <div class="payment-details">
                <div class="detail-item">
                    <span class="detail-label">Metode Pembayaran:</span>
                    <span class="detail-value"><?= $invoice['metode_pembayaran'] ?: 'Tidak ditentukan' ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Tanggal Pembayaran:</span>
                    <span class="detail-value"><?= $invoice['tanggal_pembayaran'] ? date('d F Y', strtotime($invoice['tanggal_pembayaran'])) : 'Tidak tersedia' ?></span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- BPJS Information -->
        <?php if ($invoice['jenis_pembayaran'] === 'bpjs'): ?>
        <div class="payment-info">
            <h3>Informasi BPJS</h3>
            <div class="payment-details">
                <?php if (!empty($invoice['nomor_kartu_bpjs'])): ?>
                <div class="detail-item">
                    <span class="detail-label">No. Kartu BPJS:</span>
                    <span class="detail-value"><?= $invoice['nomor_kartu_bpjs'] ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($invoice['nomor_sep'])): ?>
                <div class="detail-item">
                    <span class="detail-label">No. SEP:</span>
                    <span class="detail-value"><?= $invoice['nomor_sep'] ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        
        <!-- Footer -->
        <div class="footer">
            <p><strong><?= $lab_info['nama'] ?></strong></p>
            <p>Terima kasih atas kepercayaan Anda menggunakan layanan kami.</p>
            
            <div class="print-info">
                <p>Invoice dicetak pada: <?= $print_date ?> oleh: <?= $current_user ?></p>
                <p>Sistem Informasi Laboratorium - LabSy</p>
            </div>
        </div>
    </div>
    
    <script>
        // Auto print when page loads (optional)
        // window.addEventListener('load', function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // });
        
        // Print function
        function printInvoice() {
            window.print();
        }
        
        // Close window after printing (optional)
        window.addEventListener('afterprint', function() {
            // window.close();
        });
    </script>
</body>
</html>