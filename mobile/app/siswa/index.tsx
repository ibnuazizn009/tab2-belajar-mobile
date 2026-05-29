import React, { useState, useEffect } from 'react';
import * as SecureStore from 'expo-secure-store'
import { StyleSheet, Text, View, TextInput, FlatList, TouchableOpacity } from 'react-native';
import { Stack } from 'expo-router';
import { FontAwesome } from '@expo/vector-icons';
import {tab2ApiService} from '../../services/Tab2apiservice'
import { SkeletonHome, SkeletonBox } from '../../components/SkeletonLoader';
import { exportSiswaListPdf } from '../../utils/exportPdf';

export default function SiswaScreen() {
  const [search, setSearch] = useState('');
  const [dataSiswa, setDataSiswa] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [namaKelas, setNamaKelas] = useState('');
  const [isExporting, setIsExporting] = useState(false);

  const formatRupiah = (angka: number | string) => {
    if (!angka) return 'Rp 0';
    const format = Number(angka).toLocaleString('id-ID');
    return `Rp ${format}`;
  };

  const loadDataSiswa = async (idYangDipilih: string) => {
    try {
      setIsLoading(true);
      const responseData = await tab2ApiService.get(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/siswa-per-kelas?kelasId=${idYangDipilih}`,
        'siswa'
      )
      if (responseData && responseData.data) {
        setDataSiswa(responseData.data || []);
        setNamaKelas(responseData.data[0].nama_kelas);  
      }
    } catch (error: any) {
      console.error('Load siswa error:', error)
    } finally {
      setIsLoading(false);
    }
  }
  
  
  // Fungsi filter pencarian berdasarkan nama atau NIS
  const filteredSiswa = dataSiswa.filter(item => 
    item.nama.toLowerCase().includes(search.toLowerCase()) || item.nis.includes(search)
  );

  useEffect(() => {
    const initializeData = async () => {
      try {
        const raw = await SecureStore.getItemAsync('user_info')
        
        if (raw) {
          const user = JSON.parse(raw)       
          await loadDataSiswa(user.kelas_id)
        }
      } catch (error) {
        console.error('Gagal inisialisasi data:', error)
      }
    }
  
    initializeData()
  }, [])

    // Fungsi export
    const handleExport = async () => {
        try {
        setIsExporting(true);
        await exportSiswaListPdf(dataSiswa, namaKelas);
        } catch (e) {
        console.error('Export error:', e);
        } finally {
        setIsExporting(false);
        }
    };
  

  return (
    <>
        <Stack.Screen options={{ title: 'Cari Siswa' }} />
        
        <View style={styles.container}>
        {/* Kolom Pencarian */}
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
        <TouchableOpacity
            style={styles.exportButton}
            onPress={handleExport}
            disabled={isExporting || dataSiswa.length === 0}
            >
            <FontAwesome name="file-pdf-o" size={15} color="#fff" />
            <Text style={styles.exportText}>
                {isExporting ? 'Mengexport...' : 'Export PDF'}
            </Text>
        </TouchableOpacity>
        {/* List Daftar Siswa */}
        {isLoading ? (
            <View style={{ padding: 20 }}>
                {[0, 1, 2, 3, 4].map(i => (
                <View key={i} style={[styles.siswaCard, { marginBottom: 12 }]}>
                    {/* Avatar skeleton */}
                    <SkeletonBox width={36} height={36} style={{ borderRadius: 18, marginRight: 12 }} />
                    
                    {/* Info skeleton */}
                    <View style={{ flex: 1 }}>
                    <SkeletonBox width={140} height={13} style={{ marginBottom: 8 }} />
                    <SkeletonBox width={100} height={11} />
                    </View>

                    {/* Saldo skeleton */}
                    <View style={{ alignItems: 'flex-end' }}>
                    <SkeletonBox width={30} height={11} style={{ marginBottom: 6 }} />
                    <SkeletonBox width={80} height={13} />
                    </View>
                </View>
                ))}
            </View>
            ) : (
            <FlatList
                data={filteredSiswa}
                keyExtractor={(item) => item.nis}
                contentContainerStyle={{ padding: 20, paddingBottom: 40 }}
                ListEmptyComponent={
                <View style={styles.emptyState}>
                    <FontAwesome name="folder-open" size={40} color="#cbd5e1" />
                    <Text style={styles.emptyText}>Siswa tidak ditemukan</Text>
                </View>
                }
                renderItem={({ item }) => (
                <View style={styles.siswaCard}>
                    <View style={styles.profileBadge}>
                    <FontAwesome name="user-circle" size={36} color="#0284c7" />
                    </View>
                    <View style={styles.infoSection}>
                    <Text style={styles.siswaNama}>{item.nama}</Text>
                    <Text style={styles.siswaDetail}>NIS: {item.nis} • Kelas {item.nama_kelas}</Text>
                    </View>
                    <View style={styles.saldoSection}>
                    <Text style={styles.saldoLabel}>Saldo</Text>
                    <Text style={styles.saldoValue}>{formatRupiah(item.saldo)}</Text>
                    </View>
                </View>
                )}
            />
            )}
        </View>
    </>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  searchSection: { backgroundColor: '#fff', paddingHorizontal: 20, paddingVertical: 12, borderBottomWidth: 1, borderColor: '#e2e8f0' },
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
  exportButton: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', backgroundColor: '#dc2626', marginHorizontal: 20, marginVertical: 10, padding: 10, borderRadius: 10, gap: 8 },
  exportText: { color: '#fff', fontWeight: '600', fontSize: 14 },

});