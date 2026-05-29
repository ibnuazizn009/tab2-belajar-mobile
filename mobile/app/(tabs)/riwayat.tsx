import React, { useState, useCallback } from 'react';
import { useFocusEffect } from 'expo-router';
import * as SecureStore from 'expo-secure-store'
import { StyleSheet, Text, View, TextInput, FlatList, TouchableOpacity, Platform } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import DateTimePicker from '@react-native-community/datetimepicker';
import {tab2ApiService} from '../../services/Tab2apiservice'
import { SkeletonRiwayat } from '../../components/SkeletonLoader';

export default function RiwayatScreen() {
  const [search, setSearch] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [dataRiwayat, setDataRiwayat] = useState<any[]>([]);
  const [kelasId, setKelasId] = useState('');

  // State tanggal
  const [tglAwal, setTglAwal] = useState(new Date());
  const [tglAkhir, setTglAkhir] = useState(new Date());
  const [showPickerAwal, setShowPickerAwal] = useState(false);
  const [showPickerAkhir, setShowPickerAkhir] = useState(false);

  const formatTanggalDisplay = (date: Date) => {
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
  };

  const formatRupiah = (angka: number | string) => {
    if (!angka) return 'Rp 0';
    const format = Number(angka).toLocaleString('id-ID');
    return `Rp ${format}`;
  };

  const formatTanggalIndo = (tglISO: string) => {
    if (!tglISO) return '-';
  
    try {
      const date = new Date(tglISO);
  
      if (isNaN(date.getTime())) return tglISO;
  
      const bulanIndo = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
      ];
  
      const tanggal = date.getUTCDate();         
      const bulan = bulanIndo[date.getUTCMonth()]; 
      const tahun = date.getUTCFullYear();       
  
      return `${tanggal} ${bulan} ${tahun}`;
    } catch (error) {
      return tglISO;
    }
  };

  const formatTanggalApi = (date: Date, isEnd: boolean) => {
    const d = date.toISOString().split('T')[0];
    return isEnd ? `${d} 23:59:59` : `${d} 00:00:00`;
  };

  const filteredData = dataRiwayat.filter(item =>
    item.nama.toLowerCase().includes(search.toLowerCase()) || item.nis.includes(search)
  );

  const loadRiwayatTransaksi = async (idYangDipilih: string, awal: Date, akhir: Date) => {
    try {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/transaksi/riwayat-transaksi?kelasId=${idYangDipilih}&tgl_awal=${formatTanggalApi(awal, false)}&tgl_akhir=${formatTanggalApi(akhir, true)}`,
        'riwayat'
      );
      setDataRiwayat(responseData.data || []);
    } catch (error: any) {
      console.error('Load riwayat error:', error);
    }
  };

  useFocusEffect(
    useCallback(() => {
      const initializeData = async () => {
        setIsLoading(true);
        try {
          const raw = await SecureStore.getItemAsync('user_info');
          if (raw) {
            const user = JSON.parse(raw);
            setKelasId(user.kelas_id);
            await loadRiwayatTransaksi(user.kelas_id, tglAwal, tglAkhir);
          }
        } catch (error) {
          console.error('Gagal inisialisasi data:', error);
        } finally {
          setIsLoading(false);
        }
      };
      initializeData();
    }, [])
  );

  // Dipanggil saat user ganti tanggal dan tekan "Cari"
  const handleCariTanggal = async () => {
    if (tglAwal > tglAkhir) {
      alert('Tanggal awal tidak boleh lebih dari tanggal akhir');
      return;
    }
    setIsLoading(true);
    try {
      await loadRiwayatTransaksi(kelasId, tglAwal, tglAkhir);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <View style={styles.container}>

      {/* Search Bar */}
      <View style={styles.searchSection}>
        <View style={styles.searchWrapper}>
          <FontAwesome name="search" size={16} color="#94a3b8" style={styles.searchIcon} />
          <TextInput
            style={styles.searchInput}
            placeholder="Cari nama atau NIS siswa..."
            value={search}
            onChangeText={setSearch}
          />
        </View>
      </View>

      {/* Filter Tanggal */}
      <View style={styles.filterSection}>
        <Text style={styles.filterLabel}>Filter Tanggal</Text>
        <View style={styles.dateRow}>

          {/* Tanggal Awal */}
          <TouchableOpacity style={styles.dateButton} onPress={() => setShowPickerAwal(true)}>
            <FontAwesome name="calendar" size={14} color="#0284c7" style={{ marginRight: 6 }} />
            <Text style={styles.dateText}>{formatTanggalDisplay(tglAwal)}</Text>
          </TouchableOpacity>

          <Text style={styles.dateSeparator}>–</Text>

          {/* Tanggal Akhir */}
          <TouchableOpacity style={styles.dateButton} onPress={() => setShowPickerAkhir(true)}>
            <FontAwesome name="calendar" size={14} color="#0284c7" style={{ marginRight: 6 }} />
            <Text style={styles.dateText}>{formatTanggalDisplay(tglAkhir)}</Text>
          </TouchableOpacity>

          {/* Tombol Cari */}
          <TouchableOpacity style={styles.cariButton} onPress={handleCariTanggal}>
            <FontAwesome name="search" size={14} color="#fff" />
            <Text style={styles.cariText}>Cari</Text>
          </TouchableOpacity>

        </View>
      </View>

      {/* DateTimePicker Awal */}
      {showPickerAwal && (
        <DateTimePicker
          value={tglAwal}
          mode="date"
          display={Platform.OS === 'ios' ? 'spinner' : 'default'}
          onChange={(event, selectedDate) => {
            setShowPickerAwal(false);
            if (selectedDate) setTglAwal(selectedDate);
          }}
        />
      )}

      {/* DateTimePicker Akhir */}
      {showPickerAkhir && (
        <DateTimePicker
          value={tglAkhir}
          mode="date"
          display={Platform.OS === 'ios' ? 'spinner' : 'default'}
          onChange={(event, selectedDate) => {
            setShowPickerAkhir(false);
            if (selectedDate) setTglAkhir(selectedDate);
          }}
        />
      )}

      {/* List */}
      {isLoading ? (
        <SkeletonRiwayat />
      ) : (
        <FlatList
            data={filteredData}
            keyExtractor={(item, index) => `${item.nis}-${item.created_at}-${index}`}
            contentContainerStyle={{ padding: 20, paddingBottom: 40 }}
            ListEmptyComponent={
                <View style={styles.emptyState}>
                <FontAwesome name="folder-open" size={40} color="#cbd5e1" />
                <Text style={styles.emptyText}>Data tidak ditemukan</Text>
                </View>
            }
            renderItem={({ item }) => {
                // 1. Tentukan warna & simbol berdasarkan tipe transaksi ('setor' atau 'tarik')
                const isSetor = item.tipe === 'setor';
                const infoColor = isSetor ? '#16a34a' : '#dc2626'; // Hijau untuk setor, Merah untuk tarik
                const iconName = isSetor ? 'arrow-circle-down' : 'arrow-circle-up';
                const sign = isSetor ? '+' : '-';

                return (
                <View style={styles.siswaCard}>
                    {/* Badge Icon menyesuaikan tipe transaksi */}
                    <View style={styles.profileBadge}>
                    <FontAwesome name={iconName} size={36} color={infoColor} />
                    </View>
                    
                    <View style={styles.infoSection}>
                    <Text style={styles.siswaNama}>{item.nama}</Text>
                    <Text style={styles.siswaDetail}>NIS: {item.nis} • Kelas {item.nama_kelas}</Text>
                    
                    <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 4 }}>
                        <FontAwesome name="calendar" size={12} color="#64748b" style={{ marginRight: 5 }} />
                        <Text style={{ fontSize: 12, color: '#64748b' }}>
                        {formatTanggalIndo(item.created_at)}
                        </Text>
                        
                        {/* Label Text Tambahan (Setor / Tarik) */}
                        <Text style={{ 
                        fontSize: 10, 
                        color: '#fff', 
                        backgroundColor: infoColor, 
                        paddingHorizontal: 6, 
                        paddingVertical: 1, 
                        borderRadius: 4, 
                        marginLeft: 8,
                        fontWeight: 'bold',
                        textTransform: 'uppercase'
                        }}>
                        {item.tipe}
                        </Text>
                    </View>
                    </View>
                    
                    <View style={styles.saldoSection}>
                    <Text style={styles.saldoLabel}>Nominal</Text>
                    {/* Nominal berubah warna dan mendapat simbol + atau - */}
                    <Text style={[styles.saldoValue, { color: infoColor, fontWeight: 'bold' }]}>
                        {sign} {formatRupiah(item.nominal)}
                    </Text>
                    </View>
                </View>
                );
            }}
        />
      )}

    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  searchSection: { backgroundColor: '#fff', paddingHorizontal: 20, paddingVertical: 12, borderBottomWidth: 1, borderColor: '#e2e8f0' },
  searchWrapper: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#f1f5f9', borderRadius: 10, paddingHorizontal: 12, height: 40 },
  searchIcon: { marginRight: 8 },
  searchInput: { flex: 1, fontSize: 14, color: '#1e293b' },

  // Filter tanggal
  filterSection: { backgroundColor: '#fff', paddingHorizontal: 20, paddingVertical: 12, borderBottomWidth: 1, borderColor: '#e2e8f0' },
  filterLabel: { fontSize: 12, color: '#94a3b8', marginBottom: 8, fontWeight: '500' },
  dateRow: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  dateButton: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#f0f9ff', borderWidth: 1, borderColor: '#bae6fd', borderRadius: 8, paddingHorizontal: 10, paddingVertical: 7 },
  dateText: { fontSize: 13, color: '#0284c7', fontWeight: '500' },
  dateSeparator: { fontSize: 16, color: '#94a3b8' },
  cariButton: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#0284c7', borderRadius: 8, paddingHorizontal: 14, paddingVertical: 7, gap: 6 },
  cariText: { color: '#fff', fontSize: 13, fontWeight: '600' },

  siswaCard: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', padding: 16, borderRadius: 12, marginBottom: 12, borderWidth: 1, borderColor: '#f1f5f9', elevation: 1 },
  profileBadge: { marginRight: 12 },
  infoSection: { flex: 1 },
  siswaNama: { fontSize: 15, fontWeight: 'bold', color: '#1e293b' },
  siswaDetail: { fontSize: 12, color: '#64748b', marginTop: 2 },
  saldoSection: { alignItems: 'flex-end' },
  saldoLabel: { fontSize: 11, color: '#94a3b8', fontWeight: '500' },
  saldoValue: { fontSize: 14, fontWeight: 'bold', color: '#16a34a', marginTop: 2 },
  emptyState: { alignItems: 'center', marginTop: 40 },
  emptyText: { color: '#94a3b8', fontSize: 14, marginTop: 10 },
});