import { useState, useEffect, useCallback, useRef } from 'react'
import { router, useFocusEffect, Stack } from 'expo-router';
import * as SecureStore from 'expo-secure-store'
import { useQuery } from '@tanstack/react-query';
import { useQueryClient } from '@tanstack/react-query';
import { StyleSheet, Text, View, ScrollView, TouchableOpacity, ActivityIndicator, RefreshControl, Animated, Easing } from 'react-native';
import { FontAwesome, MaterialIcons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { triggerLogoutGlobal } from './_layout'; // Tetap diimport agar tidak merusak dependensi layout
import { tab2ApiService } from '../../services/Tab2apiservice'
import { MinimalBlueBackground } from '@/components/BackgroundLinearGradient';
import { SkeletonAktivitas } from '../../components/SkeletonLoader';


export default function HomeScreen() {
  const insets = useSafeAreaInsets();
  const queryClient = useQueryClient();
  const [refreshing, setRefreshing] = useState(false);
  const [isBalanceVisible, setIsBalanceVisible] = useState(false);
  const pulseBalance = useRef(new Animated.Value(0.4)).current;
  const pulseTransaksi = useRef(new Animated.Value(0.4)).current;

  const getSapaan = () => {
    const jam = new Date().getHours();
    if (jam < 11) return "Selamat Pagi";
    if (jam < 15) return "Selamat Siang";
    if (jam < 19) return "Selamat Sore";
    return "Selamat Malam";
  };

  // Ambil waktu lokal wilayah barat Indonesia (WIB) secara presisi
  const tzoffset = (new Date()).getTimezoneOffset() * 60000;
  const localISOTime = (new Date(Date.now() - tzoffset)).toISOString().split('T')[0];
  
  const tglAwalFormat = `${localISOTime} 00:00:00`; 
  const tglAkhirFormat = `${localISOTime} 23:59:59`;

  const formatRupiah = (angka: number) => {
    return `Rp ${angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`;
  };
  
  const formatTanggalIndo = (tglISO: string) => {
    if (!tglISO) return '-';
    try {
      const date = new Date(tglISO);
      if (isNaN(date.getTime())) return tglISO;
      const bulanIndo = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      return `${date.getDate()} ${bulanIndo[date.getMonth()]} ${date.getFullYear()}`;
    } catch { return tglISO; }
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
    staleTime: 1000 * 60 * 5, // Cache 5 menit saja, agar data baru bisa masuk
  });

  const kelasIdQuery = userInfo?.kelas_id; 
  const namaPetugasQuery = userInfo?.nama_lengkap || userInfo?.username || '-';
  const isFreeTier = userInfo?.sekolah ? !userInfo.sekolah.is_premium : true;
  const sisaHari = hitungSisaHari(userInfo?.sekolah?.premium_expires_at);

  const { data: namaKelasQuery, isLoading: isKelasLoading } = useQuery({
    queryKey: ['namaKelas', kelasIdQuery],
    queryFn: async () => {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/kelas?kelasId=${kelasIdQuery}`,
        'kelas'
      );
      return responseData?.nama_kelas || '-';
    },
    enabled: !!kelasIdQuery, // Jalankan langsung jika kelasId sudah ada
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

  // 🔥 TAMBAHAN DATA: Ambil riwayat ringkas untuk mengisi kekosongan layar bawah
  const { data: riwayatSingkat = [], isFetching: isAktivitasLoading } = useQuery({
    queryKey: ['riwayatSingkat', kelasIdQuery],
    queryFn: async () => {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/transaksi-aktivitas-kelas?kelasId=${kelasIdQuery}`,
        'siswa'
      );

      // Ambil maksimal 3 transaksi terbaru saja untuk pajangan dashboard
      return responseData?.data || [];
    },
    enabled: !!kelasIdQuery,
  });

  const isGlobalLoading = isKelasLoading || isSiswaLoading || isTransaksiLoading || isTanggalLoading || isAktivitasLoading;

  const onRefresh = useCallback(async () => {
    setRefreshing(true);
    await queryClient.invalidateQueries({ queryKey: ['userInfo'] });
    await queryClient.invalidateQueries({ queryKey: ['namaKelas', kelasIdQuery] });
    await queryClient.invalidateQueries({ queryKey: ['dataSiswa', kelasIdQuery] });
    await queryClient.invalidateQueries({ queryKey: ['totalTabunganSiswa', kelasIdQuery] });
    await queryClient.invalidateQueries({ queryKey: ['transaksiHariIni', kelasIdQuery] });
    await queryClient.invalidateQueries({ queryKey: ['riwayatSingkat', kelasIdQuery] });
    setRefreshing(false);
  }, [kelasIdQuery]);

  useEffect(() => {
    // Fungsi untuk membuat efek pulsing
    const createPulse = (anim: Animated.Value) => Animated.loop(
      Animated.sequence([
        Animated.timing(anim, { toValue: 1, duration: 1000, easing: Easing.linear, useNativeDriver: true }),
        Animated.timing(anim, { toValue: 0.4, duration: 1000, easing: Easing.linear, useNativeDriver: true }),
      ])
    );

    createPulse(pulseBalance).start();
    createPulse(pulseTransaksi).start();
  }, []);

  return (
    <View style={[styles.mainContainer, { backgroundColor: '#f8fafc' }]}>
      <Stack.Screen options={{ headerShown: false }} />
      <MinimalBlueBackground />
      
      <View style={[styles.headerContainer, { paddingTop: (insets.top || 20) + 10 }]}>
        <View style={{ flex: 1, paddingRight: 10 }}>
          <Text style={styles.welcomeText}>{getSapaan()}</Text>
          <Text style={styles.adminName} numberOfLines={1}>{namaPetugasQuery}</Text>
          <View style={styles.badgeKelas}><Text style={styles.kelasName}>Wali Kelas: {namaKelasQuery}</Text></View>
        </View>
        
        <View style={{ flexDirection: 'row', alignItems: 'center', gap: 10 }}>
          <TouchableOpacity style={styles.notificationButton} onPress={() => router.navigate('/notifikasi')}>
            <FontAwesome name="bell-o" size={20} color="#1e293b" />
            <View style={styles.bellIndicator} />
          </TouchableOpacity>
          
          <TouchableOpacity style={{ alignItems: 'center' }} onPress={() => router.push('/pusat-bantuan' as any)}>
            <MaterialIcons name="headset-mic" size={20} color="#1e293b" />
            <Text style={{ fontSize: 8, color: '#475569', fontWeight: '700' }}>Bantuan</Text>
          </TouchableOpacity>
        </View>
      </View>

      <ScrollView 
            style={[styles.container, { marginBottom: 16 }]}
            contentContainerStyle={[
              styles.contentContainer, 
              { paddingTop: 16, paddingBottom: (insets.bottom || 20) + 80 } // Padding atas dikurangi karena sudah di handle header
            ]}
            showsVerticalScrollIndicator={false}
            refreshControl={
              <RefreshControl
                refreshing={refreshing}
                onRefresh={onRefresh}
                colors={['#2563eb']}
                tintColor="#2563eb"
                progressViewOffset={50}
              />
            }      
          >

          {/* FREE TIER INFO */}
          {isFreeTier && (
            <View style={styles.freeTierBadge}>
              <FontAwesome name="info-circle" size={14} color="#b45309" style={{ marginRight: 8 }} />
              <Text style={styles.freeTierDesc} numberOfLines={1}>
                {sisaHari !== null && sisaHari > 0 ? `Sisa aktif ${sisaHari} hari.` : "Masa aktif habis."} Fitur WhatsApp terbatas.
              </Text>
            </View>
          )}

          {/* RINGKASAN CARD (ATAS) */}
          <View style={styles.unifiedDashboardCard}>
            <View style={styles.mainBalanceSection}>
                <View style={{ flex: 1 }}>
                  <Text style={styles.cardTitle}>Total Tabungan Terkumpul</Text>
                  
                  {/* Baris ini membuat teks saldo dan ikon mata sejajar */}
                  <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 4 }}>
                  {isTransaksiLoading ? (
                    <ActivityIndicator size="small" color="#ffffff" />
                  ) : (
                    <Text style={styles.cardBalance}>
                      {isBalanceVisible ? formatRupiah(totalSetoranKelasQuery) : "Rp •••••••••"}
                    </Text>
                  )}
                    
                    <TouchableOpacity 
                      onPress={() => setIsBalanceVisible(!isBalanceVisible)}
                      style={{ marginLeft: 12 }} // Beri jarak antara nominal dan ikon
                    >
                      <FontAwesome 
                        name={isBalanceVisible ? "eye" : "eye-slash"} 
                        size={18} 
                        color="#eff6ff" 
                      />
                    </TouchableOpacity>
                  </View>
                </View>
                
                <View style={styles.cardIconCircle}>
                  <FontAwesome name="database" size={20} color="#2563eb" />
                </View>
              </View>

              <View style={styles.horizontalDivider} />

              <View style={styles.bottomMetricsSection}>
                <TouchableOpacity 
                  style={styles.metricColumn}
                  activeOpacity={0.7}
                  // onPress={() => router.navigate('/siswa')}
                >
                  <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                    {isSiswaLoading ? (
                      <ActivityIndicator size="small" color="#ffffff" />
                    ) : (
                      <Text style={styles.metricValue}>{dataSiswaQuery.length}</Text>
                    )}
                  </View>
                  <Text style={styles.metricLabel}>Siswa Aktif</Text>
                </TouchableOpacity>

                <View style={styles.verticalDivider} />

                <TouchableOpacity 
                  style={styles.metricColumn}
                  activeOpacity={0.7}
                  // onPress={() => router.navigate('/(tabs)/riwayat')}
                >
                  <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                    {isTanggalLoading ? (
                      <ActivityIndicator size="small" color="#ffffff" />
                    ) : (
                      <Text style={styles.metricValue}>{totalTransaksiQuery}</Text>
                    )}
                  </View>
                  <Text style={styles.metricLabel}>Transaksi Hari Ini</Text>
                </TouchableOpacity>
              </View>
          </View>

          {/* MENU PINTAS (TENGAH) */}
          <Text style={styles.sectionTitle}>Menu Layanan</Text>
          <View style={styles.menuRowContainer}>
            {/* Setor */}
            <TouchableOpacity style={styles.menuItemButton} activeOpacity={0.6} onPress={() => router.navigate({ pathname: '/(tabs)/transaksi', params: { defaultTipe: 'setor', hideOther: 'true' } })}>
              <View style={[styles.iconSquareWrapper, { backgroundColor: '#e0f2f7' }]}>
                <FontAwesome name="inbox" size={22} color="#0891b2" />
              </View>
              <Text style={styles.menuItemLabel}>Setor</Text>
            </TouchableOpacity>

            {/* Tarik */}
            <TouchableOpacity style={styles.menuItemButton} activeOpacity={0.6} onPress={() => router.navigate({ pathname: '/(tabs)/transaksi', params: { defaultTipe: 'tarik', hideOther: 'true' } })}>
              <View style={[styles.iconSquareWrapper, { backgroundColor: '#fef3c7' }]}>
                <FontAwesome name="external-link" size={20} color="#d97706" />
              </View>
              <Text style={styles.menuItemLabel}>Tarik</Text>
            </TouchableOpacity>

            {/* Cari Siswa */}
            <TouchableOpacity style={styles.menuItemButton} activeOpacity={0.6} onPress={() => router.navigate('/siswa')}>
              <View style={[styles.iconSquareWrapper, { backgroundColor: '#e0e7ff' }]}>
                <FontAwesome name="search" size={20} color="#4f46e5" />
              </View>
              <Text style={styles.menuItemLabel}>Cari Siswa</Text>
            </TouchableOpacity>

            {/* Laporan */}
            <TouchableOpacity style={styles.menuItemButton} activeOpacity={0.6} onPress={() => router.navigate('/laporan')}>
              <View style={[styles.iconSquareWrapper, { backgroundColor: '#fee2e2' }]}>
                <FontAwesome name="file-text" size={20} color="#e11d48" />
              </View>
              <Text style={styles.menuItemLabel}>Laporan</Text>
            </TouchableOpacity>
          </View>

          {/* 🔥 PERBAIKAN 2: MENGISI AREA KOSONG BAWAH DENGAN AKTIVITAS TERBARU */}
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12, marginTop: 25 }}>
            <Text style={styles.sectionTitleNoMargin}>Aktivitas Terbaru</Text>
            <TouchableOpacity onPress={() => router.navigate('/(tabs)/riwayat')}>
              <Text style={{ fontSize: 12, color: '#2563eb', fontWeight: '700' }}>Lihat Semua</Text>
            </TouchableOpacity>
          </View>

          <View style={[
              styles.activityCardContainer, 
              { height: isAktivitasLoading ? 330 : (riwayatSingkat.length === 0 ? 80 : undefined) }
            ]}>
            <ScrollView 
              nestedScrollEnabled={true}
              showsVerticalScrollIndicator={false}
              contentContainerStyle={{ paddingBottom: 10 }}
            >
              {isAktivitasLoading ? (
                [...Array(10)].map((_, i) => (
                  <SkeletonAktivitas key={i} />
                ))
              ) : riwayatSingkat.length === 0 ? (
                // Tambahkan style agar teks di tengah saat tinggi 80
                <View style={{ flex: 1, justifyContent: 'center', height: 80 }}>
                  <Text style={styles.emptyActivityText}>Belum ada transaksi di kelas ini.</Text>
                </View>
              ) : (
                riwayatSingkat.map((trx: any, idx: number) => (
                  <View 
                    key={trx.id || idx} 
                    style={[styles.activityRow, idx === riwayatSingkat.length - 1 && { borderBottomWidth: 0 }]}
                  >
                    {/* ... isi item transaksi ... */}
                    <View style={[styles.activityIconCircle, { backgroundColor: trx.tipe === 'setor' ? '#eafaf1' : '#fdedec' }]}>
                      <FontAwesome 
                        name={trx.tipe === 'setor' ? 'arrow-down' : 'arrow-up'} 
                        size={12} 
                        color={trx.tipe === 'setor' ? '#2ecc71' : '#e74c3c'} 
                      />
                    </View>
                    
                    <View style={{ flex: 1, marginLeft: 12 }}>
                      <Text style={styles.activityStudentName}>{trx.siswa?.nama_siswa || 'Siswa'}</Text>
                      <Text style={styles.activityDate}>{formatTanggalIndo(trx.created_at ? trx.created_at.split(' ')[0] : 'Hari ini')}</Text>
                    </View>
                    
                    <Text style={[styles.activityAmount, { color: trx.tipe === 'setor' ? '#16a34a' : '#dc2626' }]}>
                      {trx.tipe === 'setor' ? '+' : '-'}{formatRupiah(trx.nominal)}
                    </Text>
                  </View>
                ))
              )}
            </ScrollView>
          </View>
        </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  mainContainer: { flex: 1 },
  container: {flex: 1},
  contentContainer: { paddingHorizontal: 20 },
  headerSection: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 20 },  
  welcomeText: { fontSize: 13, color: '#64748b', fontWeight: '500' },
  adminName: { fontSize: 18, fontWeight: '800', color: '#0f172a' },
  badgeKelas: { backgroundColor: '#e2e8f0', alignSelf: 'flex-start', paddingHorizontal: 8, paddingVertical: 3, borderRadius: 6, marginTop: 6 },
  kelasName: { fontSize: 12, fontWeight: '600', color: '#475569' },
  headerContainer: { 
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'flex-start', 
    paddingHorizontal: 20, 
    paddingBottom: 15,
    backgroundColor: 'rgba(239, 246, 255, 0.95)',
    zIndex: 10, 
    borderBottomWidth: 0,
  },
  notificationButton: { 
    width: 42, 
    height: 42, 
    borderRadius: 12, 
    backgroundColor: '#ffffff', 
    justifyContent: 'center', 
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    position: 'relative',
  },
  bellIndicator: {
    position: 'absolute',
    top: 11,
    right: 12,
    width: 7,
    height: 7,
    borderRadius: 3.5,
    backgroundColor: '#ef4444',
    borderWidth: 1,
    borderColor: '#ffffff'
  },
  
  freeTierBadge: {
    flexDirection: 'row',
    backgroundColor: '#fffbeb',
    borderWidth: 1,
    borderColor: '#fde68a',
    borderRadius: 10,
    paddingVertical: 6,
    paddingHorizontal: 12,
    marginBottom: 20,
    alignItems: 'center',
  },
  freeTierDesc: { fontSize: 12, color: '#92400e', flex: 1, fontWeight: '500' },

  loadingContainer: {
    paddingVertical: 60,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 12,
    fontSize: 14,
    color: '#64748b',
    fontWeight: '500',
  },

  // DASHBOARD CARD
  unifiedDashboardCard: {
    backgroundColor: '#2563eb', 
    borderRadius: 24,
    padding: 20,
    marginBottom: 24,
    shadowColor: '#2563eb',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.15,
    shadowRadius: 12,
    elevation: 5
  },
  mainBalanceSection: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  cardTitle: { color: '#93c5fd', fontSize: 12, fontWeight: '600', textTransform: 'uppercase', letterSpacing: 0.5 },
  cardBalance: { color: '#ffffff', fontSize: 26, fontWeight: '800', marginTop: 4, letterSpacing: -0.5 },
  cardIconCircle: { width: 42, height: 42, borderRadius: 21, backgroundColor: 'rgba(255, 255, 255, 0.95)', justifyContent: 'center', alignItems: 'center' },
  horizontalDivider: { height: 1, backgroundColor: 'rgba(255, 255, 255, 0.15)', marginVertical: 16 },
  bottomMetricsSection: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  metricColumn: { flex: 1, alignItems: 'center', justifyContent: 'center' },
  metricValue: { fontSize: 18, fontWeight: '800', color: '#ffffff' },
  metricLabel: { fontSize: 11, color: '#bfdbfe', fontWeight: '600', marginTop: 2 },
  verticalDivider: { width: 1, height: 24, backgroundColor: 'rgba(255, 255, 255, 0.2)' },

  // MENU LAYANAN
  sectionTitle: { fontSize: 13, fontWeight: '700', color: '#64748b', textTransform: 'uppercase', marginBottom: 12, letterSpacing: 0.5 },
  sectionTitleNoMargin: { fontSize: 13, fontWeight: '700', color: '#64748b', textTransform: 'uppercase', letterSpacing: 0.5 },
  menuRowContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingHorizontal: 5, // Mengatur jarak agar lebih pas
    marginBottom: 20,
  },
  menuItemButton: { 
    alignItems: 'center', 
    width: '22%', // Dibuat sedikit lebih ramping
  },
  iconSquareWrapper: { 
    width: 56, 
    height: 56, 
    borderRadius: 14, // <--- Kunci bentuk "squircle" di image_f4ed06.png
    justifyContent: 'center', 
    alignItems: 'center', 
    marginBottom: 8,
    // Menghapus shadow yang terlalu tebal agar terlihat flat seperti referensi
  },
  menuItemLabel: { 
    fontSize: 11, 
    fontWeight: '600', 
    color: '#334155',
    textAlign: 'center'
  },
  iconCircleWrapper: { width: 50, height: 50, borderRadius: 25, justifyContent: 'center', alignItems: 'center', marginBottom: 8 },

  // 🔥 STYLES BARU UNTUK AKTIVITAS TERBARU
  activityCardContainer: {
    backgroundColor: '#ffffff',
    borderRadius: 24,
    paddingHorizontal: 20,
    paddingVertical: 4,
    borderWidth: 1,
    borderColor: '#f1f5f9',
  },
  emptyActivityText: {
    textAlign: 'center',
    color: '#94a3b8',
    fontSize: 13,
    paddingVertical: 24,
  },
  activityRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 14,
    borderBottomWidth: 1,
    borderBottomColor: '#f1f5f9',
  },
  activityIconCircle: {
    width: 32,
    height: 32,
    borderRadius: 16,
    justifyContent: 'center',
    alignItems: 'center',
  },
  activityStudentName: {
    fontSize: 14,
    fontWeight: '700',
    color: '#1e293b',
  },
  activityDate: {
    fontSize: 11,
    color: '#94a3b8',
    marginTop: 1,
  },
  activityAmount: {
    fontSize: 14,
    fontWeight: '800',
  },
  // image_2.png style implementation for the subtle background
  backgroundImage: {
    flex: 1,
  },
  backgroundImageStyle: {
    position: 'absolute',
    top: -20, // Adjust to cover header subtle area
    width: '100%',
    height: '40%', // Subtle effect on top area, like image_2.png
    resizeMode: 'cover',
  },
});