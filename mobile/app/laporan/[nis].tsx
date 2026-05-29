import React, {useState} from 'react';
import { StyleSheet, Text, View, TextInput, FlatList, TouchableOpacity } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import { Stack, useLocalSearchParams } from 'expo-router';
import { exportDetailTransaksiPdf } from '../../utils/exportPdf';

const DUMMY_TRANSAKSI: Record<string, any[]> = {
  '202601001': [
    { id: '1', tipe: 'setor', nominal: 'Rp 100.000', tanggal: '01 Jun 2025' },
    { id: '2', tipe: 'setor', nominal: 'Rp 200.000', tanggal: '10 Jun 2025' },
    { id: '3', tipe: 'tarik', nominal: 'Rp 50.000',  tanggal: '15 Jun 2025' },
    { id: '4', tipe: 'setor', nominal: 'Rp 200.000', tanggal: '20 Jun 2025' },
  ],
  '202601002': [
    { id: '1', tipe: 'setor', nominal: 'Rp 500.000', tanggal: '05 Jun 2025' },
    { id: '2', tipe: 'setor', nominal: 'Rp 700.000', tanggal: '18 Jun 2025' },
  ],
};

export default function DetailTransaksiScreen() {
  const { nis, nama, nama_kelas, saldo: saldoStr } = useLocalSearchParams<{ nis: string; nama: string, nama_kelas: string, saldo: string }>();
  const transaksi = DUMMY_TRANSAKSI[nis] || [];
  const [isExporting, setIsExporting] = useState(false);
  const saldo = Number(saldoStr) || 0;
  // Fungsi export — siswa didapat dari params + data API
    const handleExport = async () => {
        try {
            setIsExporting(true);
            await exportDetailTransaksiPdf(
            { nis, nama, nama_kelas, saldo },
            transaksi
            );
        } catch (e) {
            console.error('Export error:', e);
        } finally {
            setIsExporting(false);
        }
    };

  return (
    <>
      <Stack.Screen options={{ title: nama || 'Detail Transaksi' }} />
      <View style={styles.container}>

        {/* Info Siswa */}
        <View style={styles.infoCard}>
          <FontAwesome name="user-circle" size={40} color="#0284c7" />
          <View style={{ marginLeft: 12 }}>
            <Text style={styles.namaText}>{nama}</Text>
            <Text style={styles.nisText}>NIS: {nis}</Text>
          </View>
        </View>

        <TouchableOpacity
            style={styles.exportButton}
            onPress={handleExport}
            disabled={isExporting || transaksi.length === 0}
            >
            <FontAwesome name="file-pdf-o" size={15} color="#fff" />
            <Text style={styles.exportText}>
                {isExporting ? 'Mengexport...' : 'Export PDF'}
            </Text>
        </TouchableOpacity>
        {/* List Transaksi */}
        <FlatList
          data={transaksi}
          keyExtractor={(item) => item.id}
          contentContainerStyle={{ padding: 20, paddingBottom: 40 }}
          ListEmptyComponent={
            <View style={styles.emptyState}>
              <FontAwesome name="inbox" size={40} color="#cbd5e1" />
              <Text style={styles.emptyText}>Belum ada transaksi</Text>
            </View>
          }
          renderItem={({ item }) => (
            <View style={styles.transaksiCard}>
              <View style={[styles.tipeIcon, { backgroundColor: item.tipe === 'setor' ? '#dcfce7' : '#fee2e2' }]}>
                <FontAwesome
                  name={item.tipe === 'setor' ? 'arrow-down' : 'arrow-up'}
                  size={16}
                  color={item.tipe === 'setor' ? '#16a34a' : '#dc2626'}
                />
              </View>
              <View style={styles.transaksiInfo}>
                <Text style={styles.tipeText}>
                  {item.tipe === 'setor' ? 'Setor Tunai' : 'Tarik Tunai'}
                </Text>
                <Text style={styles.tanggalText}>{item.tanggal}</Text>
              </View>
              <Text style={[styles.nominalText, { color: item.tipe === 'setor' ? '#16a34a' : '#dc2626' }]}>
                {item.tipe === 'setor' ? '+' : '-'}{item.nominal}
              </Text>
            </View>
          )}
        />

      </View>
    </>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  infoCard: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', padding: 20, borderBottomWidth: 1, borderColor: '#e2e8f0' },
  namaText: { fontSize: 16, fontWeight: 'bold', color: '#1e293b' },
  nisText: { fontSize: 13, color: '#64748b', marginTop: 2 },
  transaksiCard: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', padding: 16, borderRadius: 12, marginBottom: 10, borderWidth: 1, borderColor: '#f1f5f9', elevation: 1 },
  tipeIcon: { width: 40, height: 40, borderRadius: 20, justifyContent: 'center', alignItems: 'center', marginRight: 12 },
  transaksiInfo: { flex: 1 },
  tipeText: { fontSize: 14, fontWeight: '600', color: '#1e293b' },
  tanggalText: { fontSize: 12, color: '#94a3b8', marginTop: 2 },
  nominalText: { fontSize: 15, fontWeight: 'bold' },
  emptyState: { alignItems: 'center', marginTop: 40 },
  emptyText: { color: '#94a3b8', fontSize: 14, marginTop: 10 },
  exportButton: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', backgroundColor: '#dc2626', margin: 20, marginBottom: 0, padding: 10, borderRadius: 10, gap: 8 },
  exportText: { color: '#fff', fontWeight: '600', fontSize: 14 },

});