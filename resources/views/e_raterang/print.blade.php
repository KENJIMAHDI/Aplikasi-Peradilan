<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat - {{ $permohonan->nomor_permohonan }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; line-height: 1.6; padding: 40px; max-width: 800px; margin: auto; }
        .header { text-align: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 30px; }
        .header h2 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 0; font-size: 14px; }
        .content { margin-top: 20px; text-align: justify; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 5px; }
        .subtitle { text-align: center; margin-bottom: 30px; }
        .signature { margin-top: 80px; text-align: right; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <button class="no-print" onclick="window.print()" style="margin-bottom: 20px; padding: 10px; cursor: pointer;">Cetak Sekarang</button>
    
    <div class="header">
        <h2>PENGADILAN NEGERI CONTOH</h2>
        <p>Jl. Keadilan No. 1, Kota Hukum, Kode Pos 12345</p>
        <p>Email: info@pn-contoh.go.id | Website: www.pn-contoh.go.id</p>
    </div>

    <div class="title">SURAT KETERANGAN</div>
    <div class="subtitle">Nomor: {{ $permohonan->nomor_permohonan }}</div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini, Panitera Pengadilan Negeri Contoh, menerangkan dengan sesungguhnya bahwa:</p>
        <table style="margin-left: 30px; margin-bottom: 20px;">
            <tr><td width="150">Nama Lengkap</td><td>: <strong>{{ $permohonan->nama_pemohon }}</strong></td></tr>
            <tr><td>NIK</td><td>: {{ $permohonan->nik_pemohon }}</td></tr>
        </table>
        
        <p>Berdasarkan hasil pemeriksaan Register Perkara Pidana dan Perdata di Pengadilan Negeri Contoh, bahwa nama tersebut di atas <strong>{{ strtoupper($permohonan->jenis_surat) }}</strong>.</p>
        
        <p>Demikian Surat Keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="signature">
        <p>Kota Hukum, {{ date('d F Y') }}</p>
        <p><strong>Panitera,</strong></p>
        <br><br><br>
        <p><u>(Nama Panitera)</u><br>NIP. 19800101 200501 1 001</p>
    </div>
</body>
</html>
