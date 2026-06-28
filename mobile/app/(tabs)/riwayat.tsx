import React, { useState, useCallback } from 'react';
import { useFocusEffect } from 'expo-router';
import { useQuery } from '@tanstack/react-query';
import * as SecureStore from 'expo-secure-store';
import { StyleSheet, Text, View, TextInput, TouchableOpacity, Platform, ScrollView, RefreshControl } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import DateTimePicker from '@react-native-community/datetimepicker';
import { tab2ApiService } from '../../services/Tab2apiservice';
import { SkeletonRiwayat } from '../../components/SkeletonLoader';
import { MinimalBlueBackground } from "@/components/BackgroundLinearGradient";
import PageHeader from "@/components/PageHeader";

export default function RiwayatScreen() {
  const [refreshing, setRefreshing] = useState(false);
  const [search, setSearch] = useState('');
  const [tglAwal, setTglAwal] = useState(new Date());
  const [tglAkhir, setTglAkhir] = useState(new Date());
  const [showPickerAwal, setShowPickerAwal] = useState(false);
  const [showPickerAkhir, setShowPickerAkhir] = useState(false);

  const onRefresh = async () => {
    setRefreshing(true);
    try { await refetchRiwayat(); } catch (error) { console.log(error); } finally { setRefreshing(false); }
  };
  
  const formatTanggalDisplay = (date: Date) => date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
  const formatRupiah = (angka: number | string) => `Rp ${Number(angka || 0).toLocaleString('id-ID')}`;

  const formatTanggalIndo = (tglISO: string) => {
    if (!tglISO) return '-';
    try {
      const date = new Date(tglISO);
      if (isNaN(date.getTime())) return tglISO;
      const bulanIndo = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      return `${date.getDate()} ${bulanIndo[date.getMonth()]} ${date.getFullYear()}`;
    } catch { return tglISO; }
  };

  const formatTanggalApi = (date: Date, isEnd: boolean) => {
    const d = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    return isEnd ? `${d} 23:59:59` : `${d} 00:00:00`;
  };

  const { data: userInfo } = useQuery({
    queryKey: ['userInfo'],
    queryFn: async () => { const raw = await SecureStore.getItemAsync('user_info'); return raw ? JSON.parse(raw) : null; },
    staleTime: Infinity,
  });

  const { data: riwayatTransaksiQuery = [], isLoading: isTanggalLoading, isRefetching, refetch: refetchRiwayat } = useQuery({
    queryKey: ['riwayatTransaksi', userInfo?.kelas_id, tglAwal, tglAkhir],
    queryFn: async () => {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/transaksi/riwayat-transaksi?kelas_id=${userInfo?.kelas_id}&tgl_awal=${formatTanggalApi(tglAwal, false)}&tgl_akhir=${formatTanggalApi(tglAkhir, true)}`,
        'riwayat'
      );
      return responseData?.data || [];
    },
    enabled: !!userInfo?.kelas_id,
    staleTime: 0,
  });

  const filteredData = (riwayatTransaksiQuery || []).filter((item: any) => 
    item?.nama?.toLowerCase().includes(search.toLowerCase()) || item?.nis?.includes(search)
  );

  useFocusEffect(useCallback(() => {
    const today = new Date();
    setTglAwal(new Date(today.getFullYear(), today.getMonth(), today.getDate()));
    setTglAkhir(new Date(today.getFullYear(), today.getMonth(), today.getDate()));
  }, []));

  return (
    <View style={styles.container}>
      <MinimalBlueBackground />
      <PageHeader title="Riwayat" subtitle="Data Transaksi" />
      
      <ScrollView 
        style={styles.body} 
        contentContainerStyle={{ paddingBottom: 40 }}
        showsVerticalScrollIndicator={false}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={['#2563eb']} />}
      >
        {/* Search & Filter Card */}
        <View style={styles.filterCard}>
            <View style={styles.searchWrapper}>
              <FontAwesome name="search" size={16} color="#94a3b8" />
              <TextInput style={styles.searchInput} placeholder="Cari nama atau NIS..." value={search} onChangeText={setSearch} />
            </View>

            <View style={styles.dateRow}>
              <TouchableOpacity style={styles.dateButton} onPress={() => setShowPickerAwal(true)}>
                <Text style={styles.dateText}>{formatTanggalDisplay(tglAwal)}</Text>
              </TouchableOpacity>
              <Text style={{color:'#cbd5e1', fontWeight:'bold'}}>ke</Text>
              <TouchableOpacity style={styles.dateButton} onPress={() => setShowPickerAkhir(true)}>
                <Text style={styles.dateText}>{formatTanggalDisplay(tglAkhir)}</Text>
              </TouchableOpacity>
            </View>
        </View>

        {/* List Data */}
        {isTanggalLoading || isRefetching ? <SkeletonRiwayat /> : (
          filteredData.length === 0 ? (
            <View style={styles.emptyState}><FontAwesome name="folder-open" size={40} color="#cbd5e1" /><Text style={styles.emptyText}>Data tidak ditemukan</Text></View>
          ) : (
            filteredData.map((item: any, index: number) => {
              const isSetor = item.tipe === 'setor';
              const color = isSetor ? '#16a34a' : '#dc2626';
              return (
                <View key={index} style={styles.card}>
                  <View style={[styles.iconBox, { backgroundColor: isSetor ? '#f0fdf4' : '#fef2f2' }]}>
                    <FontAwesome name={isSetor ? 'arrow-down' : 'arrow-up'} size={18} color={color} />
                  </View>
                  <View style={{ flex: 1, marginLeft: 12 }}>
                    <Text style={styles.title}>{item.nama_siswa}</Text>
                    <Text style={styles.subtitle}>{item.nis} • {formatTanggalIndo(item.tanggal_transaksi || item.created_at)}</Text>
                  </View>
                  <View style={{ alignItems: 'flex-end' }}>
                    <Text style={[styles.amount, { color: color }]}>{isSetor ? '+' : '-'}{formatRupiah(item.nominal)}</Text>
                  </View>
                </View>
              );
            })
          )
        )}
      </ScrollView>

      {showPickerAwal && <DateTimePicker value={tglAwal} mode="date" onChange={(_, d) => { setShowPickerAwal(false); if (d) setTglAwal(d); }} />}
      {showPickerAkhir && <DateTimePicker value={tglAkhir} mode="date" onChange={(_, d) => { setShowPickerAkhir(false); if (d) setTglAkhir(d); }} />}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  body: { flex: 1, marginTop: -18, paddingHorizontal: 18 },
  filterCard: { backgroundColor: '#fff', borderRadius: 20, padding: 16, marginBottom: 16, elevation: 3, shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 8 },
  searchWrapper: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#f1f5f9', borderRadius: 12, paddingHorizontal: 12, height: 48, marginBottom: 12 },
  searchInput: { flex: 1, marginLeft: 10, fontSize: 14 },
  dateRow: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  dateButton: { flex: 1, backgroundColor: '#f8fafc', paddingVertical: 10, borderRadius: 10, borderWidth: 1, borderColor: '#e2e8f0', alignItems: 'center' },
  dateText: { fontSize: 13, fontWeight: '600', color: '#475569' },
  card: { backgroundColor: '#fff', flexDirection: 'row', alignItems: 'center', padding: 16, borderRadius: 20, marginBottom: 12, borderWidth: 1, borderColor: '#f1f5f9' },
  iconBox: { width: 44, height: 44, borderRadius: 12, justifyContent: 'center', alignItems: 'center' },
  title: { fontSize: 15, fontWeight: '700', color: '#1e293b' },
  subtitle: { fontSize: 12, color: '#64748b', marginTop: 2 },
  amount: { fontSize: 14, fontWeight: '800' },
  emptyState: { alignItems: 'center', marginTop: 40 },
  emptyText: { color: '#94a3b8', marginTop: 10 }
});