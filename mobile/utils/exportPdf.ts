import * as Print from 'expo-print';
import * as Sharing from 'expo-sharing';
import * as FileSystem from 'expo-file-system/legacy';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Platform, Alert } from 'react-native';

const STORAGE_KEY = 'saved_directory_uri';

export const exportSiswaListPdf = async (dataSiswa: any[], namaKelas: string) => {
  // 1. Hitung total saldo dari semua siswa
  const totalSaldo = dataSiswa.reduce((acc, curr) => acc + (Number(curr.saldo) || 0), 0);

  const rows = dataSiswa.map((s, i) => `
    <tr style="background:${i % 2 === 0 ? '#f8fafc' : '#fff'}">
      <td>${i + 1}</td>
      <td>${s.nis}</td>
      <td>${s.nama}</td>
      <td>${s.nama_kelas}</td>
      <td style="color:#16a34a;font-weight:bold">${formatRupiah(s.saldo)}</td>
    </tr>
  `).join('');

  const html = `
    <html>
    <head>
      <meta charset="utf-8"/>
      <style>
        body { font-family: Arial, sans-serif; padding: 24px; color: #1e293b; }
        h2 { color: #0284c7; margin-bottom: 4px; }
        p { color: #64748b; font-size: 13px; margin: 0 0 16px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { background: #0284c7; color: #fff; padding: 10px 8px; text-align: left; }
        td { padding: 9px 8px; border-bottom: 1px solid #e2e8f0; }
        
        /* Style tambahan untuk baris total */
        .total-row { background: #f1f5f9; font-weight: bold; }
        .total-row td { border-top: 2px solid #0284c7; border-bottom: 2px solid #0284c7; padding: 12px 8px; }
        
        .footer { margin-top: 24px; font-size: 12px; color: #94a3b8; text-align: right; }
      </style>
    </head>
    <body>
      <h2>Rekap Tabungan Siswa</h2>
      <p>Kelas: ${namaKelas} &nbsp;|&nbsp; Dicetak: ${formatTanggal(new Date())}</p>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Saldo</th>
          </tr>
        </thead>
        <tbody>
          ${rows}
        </tbody>
        <tfoot>
          <tr class="total-row">
            <td colspan="4" style="text-align: right;">Total Tabungan :</td>
            <td style="color:#16a34a;">${formatRupiah(totalSaldo)}</td>
          </tr>
        </tfoot>
      </table>
      <div class="footer">Sistem Tabungan Siswa</div>
    </body>
    </html>
  `;

  // Pastikan nama fungsi pemanggilnya sudah sesuai dengan fungsi save kamu yang baru
  await generateAndSaveToDocuments(html, `Rekap_Tabungan_${namaKelas}`);
};

export const exportDetailTransaksiPdf = async (siswa: { nis: string; nama: string; nama_kelas: string; saldo: number }, transaksi: any[]) => {
  const rows = transaksi.map((t, i) => `
    <tr style="background:${i % 2 === 0 ? '#f8fafc' : '#fff'}">
        <td>${i + 1}</td>
        <td>${t.tanggal}</td>
        <td>
            <span style="color:${t.tipe === 'setor' ? '#16a34a' : '#dc2626'};font-weight:bold">
              ${t.tipe === 'setor' ? 'Setor' : 'Tarik'}
            </span>
        </td>
        <td style="color:${t.tipe === 'setor' ? '#16a34a' : '#dc2626'};font-weight:bold">
            ${t.tipe === 'setor' ? '+' : '-'}${formatRupiah(typeof t.nominal === 'string' ? t.nominal.replace(/\D/g, '') : t.nominal)}
        </td>
    </tr>
  `).join('');

  const html = `
    <html>
    <head>
      <meta charset="utf-8"/>
      <style>
        body { font-family: Arial, sans-serif; padding: 24px; color: #1e293b; }
        h2 { color: #0284c7; margin-bottom: 4px; }
        .info-box { background: #f0f9ff; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; font-size: 13px; }
        .info-box span { color: #64748b; }
        .info-box strong { color: #1e293b; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { background: #0284c7; color: #fff; padding: 10px 8px; text-align: left; }
        td { padding: 9px 8px; border-bottom: 1px solid #e2e8f0; }
        .saldo { text-align:right; margin-top: 16px; font-size: 14px; }
        .saldo strong { color: #16a34a; font-size: 16px; }
        .footer { margin-top: 24px; font-size: 12px; color: #94a3b8; text-align: right; }
      </style>
    </head>
    <body>
      <h2>Detail Transaksi Tabungan</h2>
      <div class="info-box">
        <table style="border:none;width:auto">
          <tr>
            <td style="color:#64748b;padding:2px 0">Nama</td>
            <td style="padding:2px 4px;color:#64748b">:</td>
            <td style="font-weight:bold;padding:2px 0">${siswa.nama}</td>
          </tr>
          <tr>
            <td style="color:#64748b;padding:2px 0">NIS</td>
            <td style="padding:2px 4px;color:#64748b">:</td>
            <td style="font-weight:bold;padding:2px 0">${siswa.nis}</td>
          </tr>
          <tr>
            <td style="color:#64748b;padding:2px 0">Kelas</td>
            <td style="padding:2px 4px;color:#64748b">:</td>
            <td style="font-weight:bold;padding:2px 0">${siswa.nama_kelas}</td>
          </tr>
          <tr>
            <td style="color:#64748b;padding:2px 0">Dicetak</td>
            <td style="padding:2px 4px;color:#64748b">:</td>
            <td style="font-weight:bold;padding:2px 0">${formatTanggal(new Date())}</td>
          </tr>
        </table>
      </div>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Jenis</th>
            <th>Nominal</th>
          </tr>
        </thead>
        <tbody>${rows}</tbody>
      </table>
      <div class="saldo">
        Saldo Akhir: <strong>${formatRupiah(siswa.saldo)}</strong>
      </div>
      <div class="footer">Sistem Tabungan Siswa</div>
    </body>
    </html>
  `;

  await generateAndSaveToDocuments(html, `Transaksi_${siswa.nama}`);
//   await generateAndShare(html, `Transaksi_${siswa.nama.replace(/\s/g, '_')}`);
};

// ── helpers ──────────────────────────────────────────
const formatRupiah = (angka: number | string) => {
    if (angka === undefined || angka === null || angka === '') return 'Rp 0';
    
    // Jika tipenya string, bersihkan semua karakter selain angka (menghapus Rp, titik, spasi, dll)
    let cleanAngka = angka;
    if (typeof angka === 'string') {
      cleanAngka = angka.replace(/[^0-9.-]/g, ''); // Hanya menyisakan angka, titik desimal, dan minus
    }
  
    const parsed = Number(cleanAngka);
    
    // Jika setelah dibersihkan tetap gagal di-parse menjadi angka, kembalikan Rp 0
    if (isNaN(parsed)) return 'Rp 0';
  
    return `Rp ${parsed.toLocaleString('id-ID')}`;
};

const formatTanggal = (date: Date) => {
  return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
};

const generateAndShare = async (html: string, filename: string) => {
  const { uri } = await Print.printToFileAsync({ html });
  await Sharing.shareAsync(uri, {
    mimeType: 'application/pdf',
    dialogTitle: `Export ${filename}`,
    UTI: 'com.adobe.pdf',
  });
};

const generateAndSave = async (html: string, filename: string) => {
    try {
      // 1. Generate PDF ke cache internal
      const { uri: tempUri } = await Print.printToFileAsync({ html });
      const finalFilename = `${filename}.pdf`;
  
      if (Platform.OS === 'android') {
        // 2. Akses SAF melalui objek FileSystem utama
        const permissions = await FileSystem.StorageAccessFramework.requestDirectoryPermissionsAsync();
        
        if (permissions.granted) {
          const directoryUri = permissions.directoryUri;
          
          // Buat file baru menggunakan SAF
          const fileUri = await FileSystem.StorageAccessFramework.createFileAsync(
            directoryUri,
            finalFilename,
            'application/pdf'
          );
          
          // Baca file temp bawaan Expo Print dalam bentuk Base64
          const base64Data = await FileSystem.readAsStringAsync(tempUri, {
            encoding: FileSystem.EncodingType.Base64,
          });
          
          // Tulis data ke file SAF yang baru dibuat
          await FileSystem.StorageAccessFramework.writeAsStringAsync(fileUri, base64Data, {
            encoding: FileSystem.EncodingType.Base64,
          });
          
          alert('PDF berhasil disimpan ke folder pilihan Anda!');
        } else {
          // Jika izin ditolak, fallback ke share sheet biasa
          await Sharing.shareAsync(tempUri, { mimeType: 'application/pdf', dialogTitle: `Export ${filename}` });
        }
      } else {
        // Untuk iOS, shareAsync bawaan sudah sangat baik (ada tombol Save to Files)
        await Sharing.shareAsync(tempUri, {
          mimeType: 'application/pdf',
          dialogTitle: `Export ${filename}`,
          UTI: 'com.adobe.pdf',
        });
      }
    } catch (error) {
      console.error("Gagal menyimpan file:", error);
      alert("Terjadi kesalahan saat menyimpan PDF.");
    }
};

const generateAndSaveToDocuments = async (html: string, filename: string) => {
    try {
      const { uri: tempUri } = await Print.printToFileAsync({ html });
      const finalFilename = `${filename}.pdf`;
  
      if (Platform.OS === 'android') {
        // 1. Cek apakah user sudah pernah pilih folder
        let directoryUri = await AsyncStorage.getItem(STORAGE_KEY);
  
        // 2. Kalau belum, minta pilih folder (sekali saja)
        if (!directoryUri) {
          const permissions =
            await FileSystem.StorageAccessFramework.requestDirectoryPermissionsAsync();
  
          if (!permissions.granted) {
            Alert.alert('Info', 'Izin folder ditolak.');
            return;
          }
  
          directoryUri = permissions.directoryUri;
          await AsyncStorage.setItem(STORAGE_KEY, directoryUri);
        }
  
        // 3. Langsung tulis ke folder yang sudah dipilih
        const fileUri = await FileSystem.StorageAccessFramework.createFileAsync(
          directoryUri,
          finalFilename,
          'application/pdf'
        );
  
        const base64Data = await FileSystem.readAsStringAsync(tempUri, {
          encoding: FileSystem.EncodingType.Base64,
        });
  
        await FileSystem.StorageAccessFramework.writeAsStringAsync(
          fileUri,
          base64Data,
          { encoding: FileSystem.EncodingType.Base64 }
        );
  
        Alert.alert('Berhasil', `PDF tersimpan di folder pilihan Anda.`);
      } else {
        // iOS: share sheet → user pilih "Save to Files" → Documents
        await Sharing.shareAsync(tempUri, {
          mimeType: 'application/pdf',
          UTI: 'com.adobe.pdf',
        });
      }
    } catch (error: any) {
      console.error(error);
      Alert.alert('Error', error?.message ?? 'Gagal menyimpan PDF');
    }
};
  