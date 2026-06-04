import { useState, useEffect, useCallback } from 'react'
import { router, useFocusEffect } from 'expo-router';
import * as SecureStore from 'expo-secure-store'
import { useQuery } from '@tanstack/react-query';
import { StyleSheet, Text, View, ScrollView, TouchableOpacity, ActivityIndicator } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
// Menggunakan standar baru Expo Router untuk area aman layar
import { useSafeAreaInsets } from 'react-native-safe-area-context';
// Import pemicu logout global dari file layout tetangga
import { triggerLogoutGlobal } from './_layout';
import {tab2ApiService} from '../../services/Tab2apiservice'
import { SkeletonHome, SkeletonBox } from '../../components/SkeletonLoader';

export default function HomeScreen() {
  // Mengambil data padding notch/status bar secara dinamis & akurat
  const insets = useSafeAreaInsets();

  const hariIni = new Date().toISOString().split('T')[0]; 

  const tglAwalFormat = `${hariIni} 00:00:00`; 
  const tglAkhirFormat = `${hariIni} 23:59:59`;

  const formatRupiah = (angka: number) => {
    const nominalString = angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    
    return `Rp ${nominalString}`;
  };
  
  // ==================== QUERY 1: Ambil data User dari SecureStore ====================
  const { data: userInfo } = useQuery({
    queryKey: ['userInfo'],
    queryFn: async () => {
      const raw = await SecureStore.getItemAsync('user_info');
      return raw ? JSON.parse(raw) : null;
    },
    staleTime: Infinity,
  });

  const kelasIdQuery = userInfo?.kelas_id;
  const namaPetugasQuery = userInfo?.nama_petugas || '-';

  // ==================== QUERY 2: Load Nama Kelas ====================
  const { data: namaKelasQuery, isLoading: isKelasLoading } = useQuery({
    queryKey: ['namaKelas', kelasIdQuery],
    queryFn: async () => {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/kelas?kelasId=${kelasIdQuery}`,
        'kelas'
      );
      return responseData?.nama_kelas || '-';
    },
    enabled: !!kelasIdQuery,
    staleTime: Infinity,
  });

  // ==================== QUERY 3: Load Data Siswa (Menghitung Total Siswa) ====================
  const { data: dataSiswaQuery = [], isLoading: isSiswaLoading } = useQuery({
    queryKey: ['dataSiswa', kelasIdQuery],
    queryFn: async () => {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/siswa-per-kelas?kelasId=${kelasIdQuery}`,
        'siswa'
      );
      return responseData?.data || [];
    },
    enabled: !!kelasIdQuery,
    staleTime: 0,
  });

  // ==================== QUERY 4: Load Total Transaksi Siswa (Saldo) ====================
  const { data: totalSetoranKelasQuery = 0, isLoading: isTransaksiLoading } = useQuery({
    queryKey: ['totalTabunganSiswa', kelasIdQuery],
    queryFn: async () => {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/transaksi-per-kelas?kelasId=${kelasIdQuery}`,
        'siswa'
      );
      return responseData?.data?.totalTabungan ?? 0;
    },
    enabled: !!kelasIdQuery,
    staleTime: 0, 
  });

  // ==================== QUERY 5: Load Transaksi Tanggal (Hari ini) ====================
  const { data: totalTransaksiQuery = 0, isLoading: isTanggalLoading } = useQuery({
    queryKey: ['transaksiHariIni', kelasIdQuery],
    queryFn: async () => {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/transaksi-tanggal?kelasId=${kelasIdQuery}&tgl_awal=${tglAwalFormat}&tgl_akhir=${tglAkhirFormat}`,
        'siswa'
      );
      return responseData?.total_transaksi ?? 0;
    },
    enabled: !!kelasIdQuery,
    staleTime: 0,
  });

  // const loadKelas = async (idYangDipilih: string) => {
  //   try {
  //     const responseData = await tab2ApiService.getNonMessage(
  //       `${process.env.EXPO_PUBLIC_API_URL}/siswa/kelas?kelasId=${idYangDipilih}`,
  //       'kelas'
  //     )
  //     setNamaKelas(responseData.nama_kelas);
  //   } catch (error: any) {
  //     const message = error?.data?.message || 'Tidak ada data kelas'
  //     console.error('Load kelas error:', error)
  //   }
  // }

  // const loadDataSiswa = async (idYangDipilih: string) => {
  //   try {
  //     const responseData = await tab2ApiService.getNonMessage(
  //       `${process.env.EXPO_PUBLIC_API_URL}/siswa/siswa-per-kelas?kelasId=${idYangDipilih}`,
  //       'siswa'
  //     )
  //     setDataSiswa(responseData.data || []);

  //   } catch (error: any) {
  //     const message = error?.data?.message || 'Tidak ada data siswa'
  //     console.error('Load siswa error:', error)
  //   }
  // }

  // const loadTransaksiSiswa = async (idYangDipilih: string) => {
  //   try {
  //     const responseData = await tab2ApiService.getNonMessage(
  //       `${process.env.EXPO_PUBLIC_API_URL}/siswa/transaksi-per-kelas?kelasId=${idYangDipilih}`,
  //       'siswa'
  //     );
  
  //     if (responseData && responseData.data) {
  //       const dataSiswa = responseData.data;
  //       // setListSiswa(dataSiswa);
  
  //       setTotalSetoranKelas(dataSiswa.totalTabungan);
  //     }
  //   } catch (error) {
  //     console.error('Load transaksi siswa error:', error);
  //   }
  // };

  // const loadTransaksiTanggal = async (idYangDipilih: string) => {
  //   try {
  //     const responseData = await tab2ApiService.getNonMessage(
  //       `${process.env.EXPO_PUBLIC_API_URL}/siswa/transaksi-tanggal?kelasId=${idYangDipilih}&tgl_awal=${tglAwalFormat}&tgl_akhir=${tglAkhirFormat}`,
  //       'siswa'
  //     );
  
  //     if (responseData && responseData.data) {
  //       const totalTransaksi = responseData.total_transaksi ?? 0;
  //       // setListSiswa(dataSiswa);
  
  //       setTotalTransaksi(totalTransaksi);
  //     }
  //   } catch (error) {
  //     console.error('Load transaksi siswa error:', error);
  //   }
  // };
  

  // useFocusEffect(
  //   useCallback(() => {
  //     const initializeData = async () => {
  //       setIsLoading(true);
  //       try {
  //         const raw = await SecureStore.getItemAsync('user_info')
          
  //         if (raw) {
  //           const user = JSON.parse(raw)
  //           setNamaPetugas(user.nama_petugas)
  //           setKelasId(user.kelas_id)
            
  //           await loadKelas(user.kelas_id)
  //           await loadDataSiswa(user.kelas_id)
  //           await loadTransaksiSiswa(user.kelas_id)
  //           await loadTransaksiTanggal(user.kelas_id)
  //         }
  //       } catch (error) {
  //         console.error('Gagal inisialisasi data:', error)
  //       }finally {
  //         setIsLoading(false); // selesai loading (berhasil maupun gagal)
  //       }
  
  //     }
    
  //     initializeData()
  //   }, []) // [] = tidak ada dependency, jadi hanya re-run saat tab difokus
  // );

  
  // const dataRingkasan = {
  //   namaPetugas: namaPetugas || '-',
  //   namaKelas: namaKelas || '-',
  //   totalTabungan: formatRupiah(totalSetoranKelas) || 0,
  //   totalSiswa: dataSiswa.length || '-',
  //   transaksiHariIni: totalTransaksi || '-',
  // };

  const isGlobalLoading = isKelasLoading || isSiswaLoading || isTransaksiLoading || isTanggalLoading;

  return (
    <View style={[styles.mainContainer, { backgroundColor: '#f8fafc' }]}>
      <ScrollView 
        style={styles.container} 
        contentContainerStyle={[
          styles.contentContainer, 
          { paddingTop: (insets.top || 20) + 40 }
        ]}
      >
        {/* Header - SELALU TAMPIL, tidak skeleton */}
        <View style={styles.headerSection}>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start' }}>
            <View>
              <Text style={styles.welcomeText}>Selamat Datang 👋</Text>
              <Text style={styles.adminName}>{namaPetugasQuery}</Text>
              <Text style={styles.kelasName}>Walikelas : {namaKelasQuery}</Text>
            </View>
            <TouchableOpacity style={styles.logoutButton} onPress={() => triggerLogoutGlobal()}>
              <FontAwesome name="power-off" size={16} color="#dc2626" />
              <Text style={styles.logoutText}>Keluar</Text>
            </TouchableOpacity>
          </View>
          <Text style={styles.subText}>Sistem Penginputan Tabungan Siswa</Text>
        </View>
  
        {isGlobalLoading ? (
          <>
            {/* Skeleton hanya untuk bagian yang fetch data */}
  
            {/* Skeleton Balance Card */}
            <View style={{ backgroundColor: '#0284c7', borderRadius: 16, padding: 20,
                           flexDirection: 'row', justifyContent: 'space-between',
                           alignItems: 'center', marginBottom: 20 }}>
              <View>
                <SkeletonBox width={140} height={12} style={{ marginBottom: 10, opacity: 0.4 }} />
                <SkeletonBox width={180} height={28} style={{ opacity: 0.4 }} />
              </View>
              <SkeletonBox width={44} height={44} style={{ borderRadius: 22, opacity: 0.3 }} />
            </View>
  
            {/* Skeleton Stats Row */}
            <View style={{ flexDirection: 'row', justifyContent: 'space-between', marginBottom: 25 }}>
              {[0, 1].map(i => (
                <View key={i} style={[styles.statsBox, { alignItems: 'center' }]}>
                  <SkeletonBox width={32} height={32} style={{ borderRadius: 16, marginBottom: 8 }} />
                  <SkeletonBox width={50} height={18} style={{ marginBottom: 6 }} />
                  <SkeletonBox width={80} height={11} />
                </View>
              ))}
            </View>
          </>
        ) : (
          <>
            {/* Balance Card - data asli */}
            <View style={styles.balanceCard}>
              <View style={styles.cardInfo}>
                <Text style={styles.cardTitle}>Total Tabungan Semua Siswa</Text>
                <Text style={styles.cardBalance}>{formatRupiah(totalSetoranKelasQuery)}</Text>
              </View>
              <FontAwesome name="money" size={40} color="#fff" style={styles.cardIcon} />
            </View>
  
            {/* Stats Row - data asli */}
            <View style={styles.statsRow}>
              <View style={styles.statsBox}>
                <FontAwesome name="users" size={20} color="#0284c7" />
                <Text style={styles.statsValue}>{dataSiswaQuery.length}</Text>
                <Text style={styles.statsLabel}>Aktif Menabung</Text>
              </View>
              <View style={styles.statsBox}>
                <FontAwesome name="exchange" size={20} color="#16a34a" />
                <Text style={styles.statsValue}>{totalTransaksiQuery}</Text>
                <Text style={styles.statsLabel}>Transaksi Hari Ini</Text>
              </View>
            </View>
          </>
        )}
  
        {/* Menu Pintas - SELALU TAMPIL, tidak ada data dari API */}
        <Text style={styles.sectionTitle}>Menu Pintas</Text>
        <View style={styles.menuGrid}>
          <TouchableOpacity style={styles.menuButton} activeOpacity={0.7}
              onPress={() => router.navigate({ pathname: '/(tabs)/transaksi', params: { defaultTipe: 'setor', hideOther: 'true'  } })}
            >
            <View style={[styles.iconWrapper, { backgroundColor: '#e0f2fe' }]}>
              <FontAwesome name="plus-circle" size={24} color="#0284c7" />
            </View>
            <Text style={styles.menuLabel}>Input Setoran</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.menuButton} activeOpacity={0.7} 
            onPress={() => router.navigate({ pathname: '/(tabs)/transaksi', params: { defaultTipe: 'tarik', hideOther: 'true' } })}
            >
            <View style={[styles.iconWrapper, { backgroundColor: '#fee2e2' }]}>
              <FontAwesome name="minus-circle" size={24} color="#dc2626" />
            </View>
            <Text style={styles.menuLabel}>Tarik Tabungan</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.menuButton} activeOpacity={0.7} onPress={() => router.navigate('/siswa')}>
            <View style={[styles.iconWrapper, { backgroundColor: '#fef3c7' }]}>
              <FontAwesome name="search" size={24} color="#d97706" />
            </View>
            <Text style={styles.menuLabel}>Cari Siswa</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.menuButton} activeOpacity={0.7} onPress={() => router.navigate('/laporan')}>
            <View style={[styles.iconWrapper, { backgroundColor: '#e2e8f0' }]}>
              <FontAwesome name="file-text" size={24} color="#475569" />
            </View>
            <Text style={styles.menuLabel}>Laporan</Text>
          </TouchableOpacity>
        </View>
  
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  mainContainer: {
    flex: 1,
  },
  container: { 
    flex: 1, 
  },
  contentContainer: { 
    paddingHorizontal: 15,
    paddingBottom: 30,
  },
  headerSection: { 
    marginBottom: 25, 
    marginTop: 5 
  },
  welcomeText: { 
    fontSize: 16, 
    color: '#64748b' 
  },
  adminName: { 
    fontSize: 24, 
    fontWeight: 'bold', 
    color: '#1e293b', 
    marginTop: 2 
  },
  kelasName: { 
    fontSize: 20, 
    fontWeight: 'bold', 
    color: '#1e293b', 
    marginTop: 1 
  },
  subText: { 
    fontSize: 14, 
    color: '#94a3b8', 
    marginTop: 4 
  },
  logoutButton: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    backgroundColor: '#fee2e2', 
    paddingVertical: 6, 
    paddingHorizontal: 12, 
    borderRadius: 8 
  },
  logoutText: { 
    color: '#dc2626', 
    fontWeight: '600', 
    fontSize: 13, 
    marginLeft: 6 
  },
  balanceCard: { 
    backgroundColor: '#0284c7', 
    borderRadius: 16, 
    padding: 20, 
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'center', 
    marginBottom: 20, 
    elevation: 4 
  },
  cardInfo: { 
    flex: 1 
  },
  cardTitle: { 
    color: '#e0f2fe', 
    fontSize: 14, 
    fontWeight: '500' 
  },
  cardBalance: { 
    color: '#ffffff', 
    fontSize: 28, 
    fontWeight: 'bold', 
    marginTop: 8 
  },
  cardIcon: { 
    opacity: 0.8, 
    marginLeft: 10 
  },
  statsRow: { 
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    marginBottom: 25 
  },
  statsBox: { 
    backgroundColor: '#ffffff', 
    flex: 1, 
    padding: 15, 
    borderRadius: 12, 
    marginHorizontal: 5, 
    alignItems: 'center', 
    borderWidth: 1, 
    borderColor: '#f1f5f9', 
    elevation: 1 
  },
  statsValue: { 
    fontSize: 18, 
    fontWeight: 'bold', 
    color: '#1e293b', 
    marginTop: 8 
  },
  statsLabel: { 
    fontSize: 12, 
    color: '#64748b', 
    marginTop: 2 
  },
  sectionTitle: { 
    fontSize: 18, 
    fontWeight: 'bold', 
    color: '#1e293b', 
    marginBottom: 15 
  },
  menuGrid: { 
    flexDirection: 'row', 
    flexWrap: 'wrap', 
    justifyContent: 'space-between' 
  },
  menuButton: { 
    backgroundColor: '#ffffff', 
    width: '48%', 
    padding: 15, 
    borderRadius: 12, 
    alignItems: 'center', 
    marginBottom: 15, 
    borderWidth: 1, 
    borderColor: '#f1f5f9', 
    elevation: 1 
  },
  iconWrapper: { 
    width: 50, height: 50, 
    borderRadius: 25, 
    justifyContent: 'center', 
    alignItems: 'center', 
    marginBottom: 10 
  },
  menuLabel: { 
    fontSize: 14, 
    fontWeight: '600', 
    color: '#334155' 
  },
  loadingOverlay: {
    position: 'absolute',  // menimpa semua konten di bawahnya
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: 'rgba(255, 255, 255, 0.7)', // putih transparan
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: 999,
  },
  loadingBox: {
    backgroundColor: '#ffffff',
    paddingVertical: 24,
    paddingHorizontal: 32,
    borderRadius: 16,
    alignItems: 'center',
    elevation: 6,
    shadowColor: '#000',
    shadowOpacity: 0.1,
    shadowRadius: 10,
  },
  loadingText: {
    marginTop: 12,
    fontSize: 14,
    color: '#64748b',
  }  
});