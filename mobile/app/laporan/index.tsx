import React, { useState, useEffect } from 'react';
import * as SecureStore from 'expo-secure-store';
import { StyleSheet, Text, View, TextInput, TouchableOpacity, ScrollView, RefreshControl } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import { Stack, router } from 'expo-router';
import { tab2ApiService } from '../../services/Tab2apiservice';
import { SkeletonRiwayat } from '../../components/SkeletonLoader';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { MinimalBlueBackground } from "@/components/BackgroundLinearGradient";

export default function LaporanScreen() {
  const insets = useSafeAreaInsets();

  const [search, setSearch] = useState('');
  const [laporanKeuangan, setLaporanKeuangan] = useState<any[]>([]);
  const [refreshing, setRefreshing] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const [kelasId, setKelasId] = useState('');
  
  // 🎯 State menampung data user murni dari SecureStore
  const [userInfo, setUserInfo] = useState<any>(null);

  const formatRupiah = (angka: number | string) => {
    if (!angka) return 'Rp 0';
    const format = Number(angka).toLocaleString('id-ID');
    return `Rp ${format}`;
  };

  const loadLaporanKeuangan = async (idKelas: string) => {
    if (!idKelas) return;
    try {
      setIsLoading(true);
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/laporan-keuangan-siswa?kelasId=${idKelas}`,
        'siswa'
      );
      setLaporanKeuangan(responseData.data || []);
    } catch (error) {
      console.log("Gagal memuat laporan keuangan:", error);
    } finally {
      setIsLoading(false);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await loadLaporanKeuangan(kelasId);
    setRefreshing(false);
  };

  useEffect(() => {
    const initializeData = async () => {
      try {
        const raw = await SecureStore.getItemAsync('user_info');
        if (raw) {
          const user = JSON.parse(raw);
          setUserInfo(user);
          setKelasId(user.kelas_id);
          await loadLaporanKeuangan(user.kelas_id);
        }
      } catch (error) {
        console.error('Gagal inisialisasi user di Laporan:', error);
      } finally {
        setIsLoading(false);
      }
    };

    initializeData();
  }, []);

  const filteredData = laporanKeuangan.filter((item: any) =>
    item.nama_siswa.toLowerCase().includes(search.toLowerCase()) || item.nis.includes(search)
  );

  return (
    <>
      <Stack.Screen options={{ headerShown:false }} />
      
      <MinimalBlueBackground />

      <View style={[styles.header, { paddingTop: insets.top + 10 }]}>
        <View style={styles.headerContent}>
          <TouchableOpacity
            style={styles.backButton}
            onPress={() => router.back()}
          >
            <FontAwesome name="arrow-left" size={18} color="#FFFFFF" />
          </TouchableOpacity>

          <Text style={styles.headerTitle}>Laporan Tabungan</Text>

          <View style={{ width: 40 }} />
        </View>
      </View>
      <ScrollView
        style={[styles.container, { backgroundColor: 'transparent' }]}
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
              placeholder="Cari nama atau NIS..."
              value={search}
              onChangeText={setSearch}
            />
          </View>
        </View>

        {/* List Siswa */}
        {isLoading ? (
          <SkeletonRiwayat />
        ) : (
          <View style={{ paddingBottom: 40, paddingTop:10 }}>
            {filteredData.length === 0 ? (
              <View style={styles.emptyState}>
                <FontAwesome name="folder-open" size={40} color="#cbd5e1" />
                <Text style={styles.emptyText}>Siswa tidak ditemukan</Text>
              </View>
            ) : (
              filteredData.map((item: any) => (
                <TouchableOpacity
                  key={item.nis} 
                  style={styles.siswaCard}
                  onPress={() =>
                    router.navigate({
                      pathname: '/laporan/[nis]',
                      params: {
                        nis: item.nis,
                        nama: item.nama_siswa,
                        nama_kelas: item.nama_kelas,
                        saldo: item.saldo,
                      },
                    })
                  }
                >
                  <View style={styles.profileBadge}>
                    <FontAwesome name="user-circle" size={36} color="#0284c7" />
                  </View>
                  <View style={styles.infoSection}>
                    <Text style={styles.siswaNama}>{item.nama_siswa}</Text>
                    <Text style={styles.siswaDetail}>NIS: {item.nis}</Text>
                  </View>
                  <View style={styles.saldoSection}>
                    <Text style={styles.saldoLabel}>Saldo</Text>
                    <Text style={styles.saldoValue}>{formatRupiah(item.saldo)}</Text>
                  </View>
                  <FontAwesome name="chevron-right" size={14} color="#94a3b8" style={{ marginLeft: 8 }} />
                </TouchableOpacity>
              ))
            )}
          </View>
        )}
      </ScrollView>
    </>
  );
}

const styles = StyleSheet.create({
  header: {
    backgroundColor: '#2563eb',
    borderBottomWidth: 0,
    paddingBottom: 15
  },
  headerContent:{
    flexDirection:'row',
    justifyContent:'space-between',
    alignItems:'center',
    paddingHorizontal:16
  },
  backButton: {
    width: 42,
    height: 42,
    borderRadius: 12,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 0, 
  },
  headerTitle: {
    fontSize: 19,
    fontWeight: '700',
    color: '#FFFFFF'
  },
  container: { flex: 1, backgroundColor: '#f8fafc' },
  searchSection: { backgroundColor: '#fff', paddingHorizontal: 5, paddingVertical: 12, borderBottomWidth: 1, borderColor: '#e2e8f0' },
  searchWrapper: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#f1f5f9', borderRadius: 10, paddingHorizontal: 12, height: 40 },
  searchIcon: { marginRight: 8 },
  searchInput: { flex: 1, fontSize: 14, color: '#1e293b' },
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
  contentContainer: { padding: 5, paddingBottom: 40 }
});