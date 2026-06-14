import React, { useState, useEffect } from 'react';
import { useQuery } from '@tanstack/react-query';
import * as SecureStore from 'expo-secure-store';
import { StyleSheet, Text, View, TouchableOpacity, ScrollView, RefreshControl, Alert } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import { Stack, useLocalSearchParams } from 'expo-router';
import { exportDetailTransaksiPdf } from '../../utils/exportPdf';
import { tab2ApiService } from '../../services/Tab2apiservice';
import { SkeletonTransaksi } from '../../components/SkeletonLoader';
import { formatRupiah, formatTanggalIndo } from '../../utils/formatTanggal';
import { CHECK_FEATURE } from '@/constants/Features'; // 🎯 Import helper penentu bisnis tiering

export default function DetailTransaksiScreen() {
  const { nis, nama, nama_kelas, saldo: saldoStr } = useLocalSearchParams<{ nis: string; nama: string, nama_kelas: string, saldo: string }>();
  const saldo = Number(saldoStr) || 0;
  const [isExporting, setIsExporting] = useState(false);
  
  // 🎯 State lokal penampung hak akses user login
  const [userInfo, setUserInfo] = useState<any>(null);

  useEffect(() => {
    const fetchUser = async () => {
      const raw = await SecureStore.getItemAsync('user_info');
      if (raw) setUserInfo(JSON.parse(raw));
    };
    fetchUser();
  }, []);

  const { data: transaksi = [], isLoading, isRefetching, refetch } = useQuery({
    queryKey: ['detailTransaksiSiswa', nis],
    queryFn: async () => {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/laporan-transaksi-siswa?nis=${nis}`,
        'siswa'
      );
      const data = responseData.data || [];

      return data.map((item: any) => ({
        tipe:    item.tipe,
        nominal: item.nominal,
        tanggal: formatTanggalIndo(item.created_at),
        created_at: item.created_at,
      }));
    },
    enabled: !!nis,
    staleTime: 0,
  });

  // 🎯 Proteksi Fitur Cetak PDF Riwayat Siswa
  const handleExport = async () => {
    const isAllowedToDownload = CHECK_FEATURE(userInfo?.paket_layanan, 'DOWNLOAD_REPORT');

    if (!isAllowedToDownload) {
      Alert.alert(
        '🔒 Fitur Premium Terkunci',
        'Fitur cetak PDF riwayat detail transaksi ini hanya tersedia pada paket Golden All Akses. Silakan hubungi manajemen atau Admin Utama sekolah untuk upgrade layanan!'
      );
      return;
    }

    try {
        setIsExporting(true);
        await exportDetailTransaksiPdf(
          { nis, nama: nama, nama_kelas, saldo },
          transaksi
        );
    } catch (e) {
        console.error('Export error:', e);
    } finally {
        setIsExporting(false);
    }
  };

  // Cek kasta paket untuk manajemen warna tombol UI
  const isGoldenTier = CHECK_FEATURE(userInfo?.paket_layanan, 'DOWNLOAD_REPORT');

  return (
    <>
      <Stack.Screen options={{ title: nama || 'Detail Transaksi' }} />
      <ScrollView
        style={styles.container}
        contentContainerStyle={[{ flexGrow: 1 }, styles.contentContainer]}
        bounces={true}
        alwaysBounceVertical={true}
        overScrollMode="always"
        refreshControl={
          <RefreshControl
            refreshing={isRefetching}
            onRefresh={refetch}
            colors={['#0284c7']}
            tintColor="#0284c7"
          />
        }
      >
        {/* Info Siswa */}
        <View style={styles.infoCard}>
          <FontAwesome name="user-circle" size={40} color="#0284c7" />
          <View style={{ marginLeft: 12 }}>
            <Text style={styles.namaText}>{nama}</Text>
            <Text style={styles.nisText}>NIS: {nis} • Kelas {nama_kelas}</Text>
          </View>
        </View>

        {/* 🎯 Export Button Interaktif Berdasarkan Paket */}
        <TouchableOpacity
          style={[
            styles.exportButton,
            !isGoldenTier && { backgroundColor: '#94a3b8' } // Ubah abu-abu jika Free / Middle
          ]}
          onPress={handleExport}
          disabled={isExporting || transaksi.length === 0}
        >
          <FontAwesome name={isGoldenTier ? "file-pdf-o" : "lock"} size={15} color="#fff" />
          <Text style={styles.exportText}>
            {isExporting ? 'Mengexport...' : isGoldenTier ? 'Export PDF Transaksi' : 'PDF (Golden Only)'}
          </Text>
        </TouchableOpacity>

        {/* List Transaksi */}
        {isLoading ? (
          <SkeletonTransaksi />
        ) : (
          <View style={{ padding: 5, paddingBottom: 40, paddingTop:10 }}>
            {transaksi.length === 0 ? (
              <View style={styles.emptyState}>
                <FontAwesome name="inbox" size={40} color="#cbd5e1" />
                <Text style={styles.emptyText}>Belum ada transaksi</Text>
              </View>
            ) : (
              transaksi.map((item: any, index: number) => {
                const isSetor = item.tipe === 'setor';
                const infoColor = isSetor ? '#16a34a' : '#dc2626';

                return (
                  <View key={`${nis}-${item.created_at}-${index}`} style={styles.transaksiCard}>
                    <View style={[styles.tipeIcon, { backgroundColor: isSetor ? '#dcfce7' : '#fee2e2' }]}>
                      <FontAwesome
                        name={isSetor ? 'arrow-down' : 'arrow-up'}
                        size={16}
                        color={infoColor}
                      />
                    </View>
                    <View style={styles.transaksiInfo}>
                      <Text style={styles.tipeText}>
                        {isSetor ? 'Setor' : 'Tarik'}
                      </Text>
                      <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 3 }}>
                        <FontAwesome name="calendar" size={11} color="#94a3b8" style={{ marginRight: 4 }} />
                        <Text style={styles.tanggalText}>{item.tanggal}</Text>
                      </View>
                    </View>
                    <Text style={[styles.nominalText, { color: infoColor }]}>
                      {isSetor ? '+' : '-'} {formatRupiah(item.nominal)}
                    </Text>
                  </View>
                );
              })
            )}
          </View>
        )}
      </ScrollView>
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
  exportButton: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', backgroundColor: '#dc2626', margin: 5, marginTop: 10, padding: 10, borderRadius: 10, gap: 8 },
  exportText: { color: '#fff', fontWeight: '600', fontSize: 14 },
  contentContainer: { paddingBottom: 20 },
});