import { useState, useEffect, useCallback } from 'react'
import { router, useFocusEffect } from 'expo-router';
import * as SecureStore from 'expo-secure-store'
import { useQuery } from '@tanstack/react-query';
import { useQueryClient } from '@tanstack/react-query';
import { StyleSheet, Text, View, ScrollView, TouchableOpacity, ActivityIndicator, RefreshControl } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { triggerLogoutGlobal } from './_layout';
import { tab2ApiService } from '../../services/Tab2apiservice'
import { SkeletonHome, SkeletonBox } from '../../components/SkeletonLoader';

export default function HomeScreen() {
  const insets = useSafeAreaInsets();
  const queryClient = useQueryClient();
  const [refreshing, setRefreshing] = useState(false);

  // Ambil waktu lokal wilayah barat Indonesia (WIB) secara presisi
  const tzoffset = (new Date()).getTimezoneOffset() * 60000;
  const localISOTime = (new Date(Date.now() - tzoffset)).toISOString().split('T')[0];
  
  const tglAwalFormat = `${localISOTime} 00:00:00`; 
  const tglAkhirFormat = `${localISOTime} 23:59:59`;

  const formatRupiah = (angka: number) => {
    return `Rp ${angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`;
  };
  
  const hitungSisaHari = (tanggalExpired: string | null) => {
  if (!tanggalExpired) return null;
  
  const sekarang = new Date();
  const tglExpired = new Date(tanggalExpired);
  
  const selisihMs = tglExpired.getTime() - sekarang.getTime();
  
  const selisihHari = Math.ceil(selisihMs / (1000 * 3600 * 24));
  
  return selisihHari;
};

  const { data: userInfo, isSuccess: isUserInfoReady } = useQuery({
    queryKey: ['userInfo'],
    queryFn: async () => {
      const raw = await SecureStore.getItemAsync('user_info');
      return raw ? JSON.parse(raw) : null;
    },
    staleTime: Infinity,
  });

  
  const kelasIdQuery = userInfo?.kelas_id; 
  const namaPetugasQuery = userInfo?.nama_lengkap || userInfo?.username || '-';

  const isFreeTier = userInfo?.sekolah ? !userInfo.sekolah.is_premium : true;

  const sisaHari = hitungSisaHari(userInfo?.sekolah?.premium_expires_at);

  const { data: namaKelasQuery, isLoading: isKelasLoading, status, fetchStatus } = useQuery({
    queryKey: ['namaKelas', kelasIdQuery],
    queryFn: async () => {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/kelas?kelasId=${kelasIdQuery}`,
        'kelas'
      );

      return responseData?.nama_kelas || '-';
    },
    enabled: isUserInfoReady && !!kelasIdQuery,
    staleTime: Infinity,
  });

  const { data: dataSiswaQuery = [], isFetching: isSiswaLoading } = useQuery({
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

  const { data: totalSetoranKelasQuery = 0, isFetching: isTransaksiLoading } = useQuery({
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

  const { data: totalTransaksiQuery = 0, isFetching: isTanggalLoading } = useQuery({
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

  const isGlobalLoading = isKelasLoading || isSiswaLoading || isTransaksiLoading || isTanggalLoading;

  const onRefresh = useCallback(async () => {
    setRefreshing(true);
    await queryClient.invalidateQueries({ queryKey: ['dataSiswa', kelasIdQuery] });
    await queryClient.invalidateQueries({ queryKey: ['totalTabunganSiswa', kelasIdQuery] });
    await queryClient.invalidateQueries({ queryKey: ['transaksiHariIni', kelasIdQuery] });
    setRefreshing(false);
  }, [kelasIdQuery]);

  return (
    <View style={[styles.mainContainer, { backgroundColor: '#f8fafc' }]}>
      <ScrollView 
        style={styles.container} 
        contentContainerStyle={[
          styles.contentContainer, 
          { paddingTop: (insets.top || 20) + 35 } 
        ]}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={onRefresh}
            colors={['#0284c7']}
            tintColor="#0284c7"
            progressViewOffset={60}
          />
        }      
      >
        {/* Header Section */}
        <View style={styles.headerSection}>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
            <View style={{ flex: 1, paddingRight: 10 }}>
              <Text style={styles.welcomeText}>Selamat Datang 👋</Text>
              <Text style={styles.adminName} numberOfLines={1}>{namaPetugasQuery}</Text>
              <Text style={styles.kelasName}>Wali Kelas: {namaKelasQuery}</Text>
            </View>
            <TouchableOpacity style={styles.logoutButton} onPress={() => triggerLogoutGlobal()}>
              <FontAwesome name="power-off" size={16} color="#dc2626" />
              <Text style={styles.logoutText}>Keluar</Text>
            </TouchableOpacity>
          </View>
          <Text style={styles.subText}>Sistem Penginputan Tabungan Siswa</Text>
        </View>

        {/* PENANDA FREE TIER */}
        {isFreeTier && (
          <View style={styles.freeTierBadge}>
            <View style={styles.freeTierIconWrapper}>
              <FontAwesome name="info-circle" size={18} color="#b45309" />
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.freeTierTitle}>
                Mode Layanan: <Text style={{ fontWeight: 'bold' }}>Gratis (Free Tier)</Text>
              </Text>
              
              <Text style={styles.freeTierDesc}>
                {sisaHari !== null && sisaHari > 0 ? (
                  <Text style={styles.sisaHariHighlight}>
                    Masa aktif sisa {sisaHari} hari. 
                  </Text>
                ) : (
                  <Text style={styles.expiredHighlight}>
                    Masa aktif telah berakhir. 
                  </Text>
                )}
                {" "}Fitur WhatsApp Notifikasi & multi-petugas dibatasi. Silakan hubungi Admin Sekolah untuk upgrade.
              </Text>
            </View>
          </View>
        )}
  
        {isGlobalLoading ? (
          <>
            {/* Skeleton Balance Card */}
            <View style={{ backgroundColor: '#0284c7', borderRadius: 16, padding: 22,
                           flexDirection: 'row', justifyContent: 'space-between',
                           alignItems: 'center', marginBottom: 20 }}>
              <View>
                <SkeletonBox width={150} height={14} style={{ marginBottom: 10, opacity: 0.4 }} />
                <SkeletonBox width={200} height={32} style={{ opacity: 0.4 }} />
              </View>
              <SkeletonBox width={48} height={48} style={{ borderRadius: 24, opacity: 0.3 }} />
            </View>
  
            {/* Skeleton Stats Row */}
            <View style={{ flexDirection: 'row', justifyContent: 'space-between', marginBottom: 25 }}>
              {[0, 1].map(i => (
                <View key={i} style={[styles.statsBox, { alignItems: 'center' }]}>
                  <SkeletonBox width={36} height={36} style={{ borderRadius: 18, marginBottom: 8 }} />
                  <SkeletonBox width={60} height={22} style={{ marginBottom: 6 }} />
                  <SkeletonBox width={90} height={14} />
                </View>
              ))}
            </View>
          </>
        ) : (
          <>
            {/* Balance Card */}
            <View style={styles.balanceCard}>
              <View style={styles.cardInfo}>
                <Text style={styles.cardTitle}>Total Tabungan Semua Siswa</Text>
                <Text style={styles.cardBalance}>{formatRupiah(totalSetoranKelasQuery)}</Text>
              </View>
              <FontAwesome name="money" size={44} color="#fff" style={styles.cardIcon} />
            </View>
  
            {/* Stats Row */}
            <View style={styles.statsRow}>
              <View style={styles.statsBox}>
                <FontAwesome name="users" size={22} color="#0284c7" />
                <Text style={styles.statsValue}>{dataSiswaQuery.length}</Text>
                <Text style={styles.statsLabel}>Aktif Menabung</Text>
              </View>
              <TouchableOpacity 
                style={styles.statsBox}
                onPress={() => router.navigate('/(tabs)/riwayat')}
              >
                <FontAwesome name="exchange" size={22} color="#16a34a" />
                <Text style={styles.statsValue}>{totalTransaksiQuery}</Text>
                <Text style={styles.statsLabel}>Transaksi Hari Ini</Text>
              </TouchableOpacity>
            </View>
          </>
        )}
  
        {/* Menu Pintas */}
        <Text style={styles.sectionTitle}>Menu Pintas</Text>
        <View style={styles.menuGrid}>
          <TouchableOpacity style={styles.menuButton} activeOpacity={0.7}
              onPress={() => router.navigate({ pathname: '/(tabs)/transaksi', params: { defaultTipe: 'setor', hideOther: 'true'  } })}
            >
            <View style={[styles.iconWrapper, { backgroundColor: '#e0f2fe' }]}>
              <FontAwesome name="plus-circle" size={26} color="#0284c7" />
            </View>
            <Text style={styles.menuLabel}>Input Setoran</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.menuButton} activeOpacity={0.7} 
            onPress={() => router.navigate({ pathname: '/(tabs)/transaksi', params: { defaultTipe: 'tarik', hideOther: 'true' } })}
            >
            <View style={[styles.iconWrapper, { backgroundColor: '#fee2e2' }]}>
              <FontAwesome name="minus-circle" size={26} color="#dc2626" />
            </View>
            <Text style={styles.menuLabel}>Tarik Tabungan</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.menuButton} activeOpacity={0.7} onPress={() => router.navigate('/siswa')}>
            <View style={[styles.iconWrapper, { backgroundColor: '#fef3c7' }]}>
              <FontAwesome name="search" size={26} color="#d97706" />
            </View>
            <Text style={styles.menuLabel}>Cari Siswa</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.menuButton} activeOpacity={0.7} onPress={() => router.navigate('/laporan')}>
            <View style={[styles.iconWrapper, { backgroundColor: '#e2e8f0' }]}>
              <FontAwesome name="file-text" size={26} color="#475569" />
            </View>
            <Text style={styles.menuLabel}>Laporan</Text>
          </TouchableOpacity>
        </View>
  
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  mainContainer: { flex: 1 },
  container: { flex: 1 },
  contentContainer: { paddingHorizontal: 16, paddingBottom: 35 },
  headerSection: { marginBottom: 22, marginTop: 5 },
  
  welcomeText: { fontSize: 16, color: '#475569', fontWeight: '500' },
  adminName: { fontSize: 20, fontWeight: 'bold', color: '#0f172a', marginTop: 4 },
  kelasName: { fontSize: 15, fontWeight: '700', color: '#334155', marginTop: 4 },
  subText: { fontSize: 14, color: '#64748b', marginTop: 8 },
  
  logoutButton: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fee2e2', paddingVertical: 8, paddingHorizontal: 12, borderRadius: 10, height: 38 },
  logoutText: { color: '#dc2626', fontWeight: '700', fontSize: 13, marginLeft: 6 },
  
  freeTierBadge: {
    flexDirection: 'row',
    backgroundColor: '#fffbeb',
    borderWidth: 1.5,
    borderColor: '#fde68a',
    borderRadius: 14,
    padding: 14,
    marginBottom: 22,
    alignItems: 'center',
    elevation: 1,
  },
  freeTierIconWrapper: {
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: '#fef3c7',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  freeTierTitle: { fontSize: 13, color: '#78350f', fontWeight: '700' },
  sisaHariHighlight: { 
    color: '#c2410c',
    fontWeight: '800',
    backgroundColor: '#ffedd5', 
    paddingHorizontal: 4,
    borderRadius: 4,
  },
  
  expiredHighlight: { 
    color: '#b91c1c', 
    fontWeight: '800',
    textTransform: 'uppercase',
  },
  freeTierDesc: { fontSize: 13, color: '#92400e', marginTop: 3, lineHeight: 18, fontWeight: '500' },

  balanceCard: { backgroundColor: '#0284c7', borderRadius: 18, padding: 22, flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 22, elevation: 4 },
  cardInfo: { flex: 1 },
  cardTitle: { color: '#f0f9ff', fontSize: 15, fontWeight: '600' },
  cardBalance: { color: '#ffffff', fontSize: 25, fontWeight: 'bold', marginTop: 8, letterSpacing: 0.5 },
  cardIcon: { opacity: 0.9, marginLeft: 10 },
  
  statsRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 8 },
  statsBox: { backgroundColor: '#ffffff', flex: 1, padding: 16, borderRadius: 14, marginHorizontal: 5, alignItems: 'center', borderWidth: 1.5, borderColor: '#e2e8f0', elevation: 1 },
  statsValue: { fontSize: 15, fontWeight: 'bold', color: '#0f172a', marginTop: 6 },
  statsLabel: { fontSize: 13, color: '#475569', fontWeight: '600', marginTop: 4 },
  
  sectionTitle: { fontSize: 18, fontWeight: 'bold', color: '#0f172a', marginBottom: 16, marginTop: 5 },
  menuGrid: { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between' },
  menuButton: { backgroundColor: '#ffffff', width: '48%', padding: 16, borderRadius: 14, alignItems: 'center', marginBottom: 16, borderWidth: 1.5, borderColor: '#e2e8f0', elevation: 1 },
  iconWrapper: { width: 54, height: 54, borderRadius: 27, justifyContent: 'center', alignItems: 'center', marginBottom: 10 },
  menuLabel: { fontSize: 13, fontWeight: '700', color: '#1e293b' },
});