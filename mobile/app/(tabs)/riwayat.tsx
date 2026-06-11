import React, { useState, useCallback } from 'react';
import { useFocusEffect } from 'expo-router';
import { useQuery } from '@tanstack/react-query';
import * as SecureStore from 'expo-secure-store'
import { StyleSheet, Text, View, TextInput, TouchableOpacity, Platform, ScrollView, RefreshControl } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import DateTimePicker from '@react-native-community/datetimepicker';
import { tab2ApiService } from '../../services/Tab2apiservice'
import { SkeletonRiwayat } from '../../components/SkeletonLoader';

export default function RiwayatScreen() {
  const [refreshing, setRefreshing] = useState(false)
  const [search, setSearch] = useState('');

  // State tanggal
  const [tglAwal, setTglAwal] = useState(new Date());
  const [tglAkhir, setTglAkhir] = useState(new Date());
  const [showPickerAwal, setShowPickerAwal] = useState(false);
  const [showPickerAkhir, setShowPickerAkhir] = useState(false);

  const onRefresh = async () => {
    setRefreshing(true);
    try {
      await refetchRiwayat();
    } catch (error) {
      console.log("Gagal memperbarui riwayat transaksi:", error);
    } finally {
      setRefreshing(false);
    }
  };
  
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
  
      const tanggal = date.getDate();         
      const bulan = bulanIndo[date.getMonth()]; 
      const tahun = date.getFullYear();       
  
      return `${tanggal} ${bulan} ${tahun}`;
    } catch (error) {
      return tglISO;
    }
  };

  const formatTanggalApi = (date: Date, isEnd: boolean) => {
    const tahun = date.getFullYear();
    const bulan = String(date.getMonth() + 1).padStart(2, '0');
    const tanggal = String(date.getDate()).padStart(2, '0');
  
    const d = `${tahun}-${bulan}-${tanggal}`;
    
    return isEnd ? `${d} 23:59:59` : `${d} 00:00:00`;
  };

  const { data: userInfo } = useQuery({
    queryKey: ['userInfo'],
    queryFn: async () => {
      const raw = await SecureStore.getItemAsync('user_info');
      return raw ? JSON.parse(raw) : null;
    },
    staleTime: Infinity,
  });

  const kelasIdQuery = userInfo?.kelas_id;

  const { data: riwayatTransaksiQuery = [], isLoading: isTanggalLoading, isRefetching, refetch: refetchRiwayat } = useQuery({
    queryKey: ['riwayatTransaksi', kelasIdQuery, tglAwal, tglAkhir],
    queryFn: async () => {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/transaksi/riwayat-transaksi?kelas_id=${kelasIdQuery}&tgl_awal=${formatTanggalApi(tglAwal, false)}&tgl_akhir=${formatTanggalApi(tglAkhir, true)}`,
        'riwayat'
      );
      return responseData?.data || [];
    },
    enabled: !!kelasIdQuery && !!tglAwal && !!tglAkhir,
    staleTime: 0,
  });

  const filteredData = (riwayatTransaksiQuery || []).filter((item: any) => {
    const nama = item?.nama?.toLowerCase() || '';
    const nis = item?.nis || '';
    return nama.includes(search.toLowerCase()) || nis.includes(search);
  });

  useFocusEffect(
    useCallback(() => {
      const hariIniAwal = new Date();
      hariIniAwal.setHours(0, 0, 0, 0);

      const hariIniAkhir = new Date();
      hariIniAkhir.setHours(23, 59, 59, 999);

      setTglAwal(hariIniAwal);
      setTglAkhir(hariIniAkhir);
    }, [])
  );

  return (
    <>
      <ScrollView 
        style={styles.container}
        contentContainerStyle={[{ flexGrow: 1 }, styles.contentContainer]} 
        bounces={true}                         
        alwaysBounceVertical={true}             
        overScrollMode="always"                
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={onRefresh}
            colors={['#0284c7']}
            tintColor="#0284c7"
          />
        }
      >
        
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
  
          </View>
        </View>
  
        {/* List Riwayat Transaksi */}
        {isTanggalLoading || isRefetching ? (
          <SkeletonRiwayat />
        ) : (
          <View style={{ paddingBottom: 20 }}>
            {filteredData.length === 0 ? (
              <View style={styles.emptyState}>
                <FontAwesome name="folder-open" size={36} color="#cbd5e1" />
                <Text style={styles.emptyText}>Data tidak ditemukan</Text>
              </View>
            ) : (
              filteredData.map((item: any, index: number) => {
                const isSetor = item.tipe === 'setor';
                const infoColor = isSetor ? '#16a34a' : '#dc2626'; 
                const iconName = isSetor ? 'arrow-circle-down' : 'arrow-circle-up';
                const sign = isSetor ? '+' : '-';
                
                // Fallback deteksi key tanggal dari database
                const tanggalData = item.tanggal_transaksi || item.created_at;
  
                return (
                  <View key={`${item.nis}-${tanggalData}-${index}`} style={styles.siswaCard}>
                    {/* Icon Status */}
                    <View style={styles.profileBadge}>
                      <FontAwesome name={iconName} size={32} color={infoColor} />
                    </View>
                    
                    {/* Info Transaksi */}
                    <View style={styles.infoSection}>
                      <Text style={styles.siswaNama} numberOfLines={1}>{item.nama_siswa}</Text>
                      <Text style={styles.siswaDetail}>NIS: {item.nis} • {item.nama_kelas || '-'}</Text>
                      
                      <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 5 }}>
                        <FontAwesome name="calendar" size={12} color="#64748b" style={{ marginRight: 5 }} />
                        <Text style={{ fontSize: 13, color: '#475569', fontWeight: '500' }}>
                          {formatTanggalIndo(tanggalData)}
                        </Text>
                        
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
                    
                    {/* Nominal */}
                    <View style={styles.saldoSection}>
                      <Text style={styles.saldoLabel}>Nominal</Text>
                      <Text style={[styles.saldoValue, { color: infoColor }]}>
                        {sign}{formatRupiah(item.nominal)}
                      </Text>
                    </View>
                  </View>
                );
              })
            )}
          </View>
        )}
      </ScrollView>
  
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
    </>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  contentContainer: { paddingHorizontal: 10, paddingBottom: 20 },
  
  // Search Bar Ramping
  searchSection: { backgroundColor: '#fff', paddingVertical: 10, paddingHorizontal: 4, borderBottomWidth: 1, borderColor: '#e2e8f0' },
  searchWrapper: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#f1f5f9', borderRadius: 8, paddingHorizontal: 10, height: 38 },
  searchIcon: { marginRight: 6 },
  searchInput: { flex: 1, fontSize: 14, color: '#1e293b' },

  // Filter rentang tanggal hemat ruang
  filterSection: { backgroundColor: '#fff', paddingVertical: 10, paddingHorizontal: 4, borderBottomWidth: 1, borderColor: '#e2e8f0', marginBottom: 10 },
  filterLabel: { fontSize: 12, color: '#64748b', marginBottom: 6, fontWeight: '600' },
  dateRow: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  dateButton: { flex: 1, flexDirection: 'row', alignItems: 'center', backgroundColor: '#f0f9ff', borderWidth: 1, borderColor: '#bae6fd', borderRadius: 8, paddingHorizontal: 8, paddingVertical: 6, justifyContent: 'center' },
  dateText: { fontSize: 13, color: '#0284c7', fontWeight: '600' },
  dateSeparator: { fontSize: 14, color: '#94a3b8' },

  // Desain kartu riwayat kompak namun huruf teks tetap terbaca tegas
  siswaCard: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', paddingVertical: 10, paddingHorizontal: 12, borderRadius: 10, marginBottom: 8, borderWidth: 1, borderColor: '#f1f5f9', elevation: 1 },
  profileBadge: { marginRight: 10 },
  infoSection: { flex: 1, paddingRight: 4 },
  siswaNama: { fontSize: 15, fontWeight: 'bold', color: '#0f172a' },
  siswaDetail: { fontSize: 13, color: '#64748b', marginTop: 1, fontWeight: '500' },
  saldoSection: { alignItems: 'flex-end', minWidth: 90 },
  saldoLabel: { fontSize: 11, color: '#94a3b8', fontWeight: '500' },
  saldoValue: { fontSize: 14, fontWeight: '700', marginTop: 1 },
  
  emptyState: { alignItems: 'center', marginTop: 30 },
  emptyText: { color: '#94a3b8', fontSize: 14, marginTop: 8 },
});