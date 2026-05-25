import { useState, useEffect } from 'react'
import * as SecureStore from 'expo-secure-store'
import { StyleSheet, Text, View, ScrollView, TouchableOpacity } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
// Menggunakan standar baru Expo Router untuk area aman layar
import { useSafeAreaInsets } from 'react-native-safe-area-context';
// Import pemicu logout global dari file layout tetangga
import { triggerLogoutGlobal } from './_layout';
import {tab2ApiService} from '../../services/Tab2apiservice'

export default function HomeScreen() {
  // Mengambil data padding notch/status bar secara dinamis & akurat
  const insets = useSafeAreaInsets();

  const [namaPetugas, setNamaPetugas] = useState('')
  const [kelasId, setKelasId] = useState('')
  const [namaKelas, setNamaKelas] = useState('')
  const [dataSiswa, setDataSiswa] = useState<any[]>([]);

  const loadKelas = async () => {
    try {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/kelas?kelasId=${kelasId}`,
        'kelas'
      )
      setNamaKelas(responseData.nama_kelas);
    } catch (error: any) {
      const message = error?.data?.message || 'Tidak ada data kelas'
      console.error('Load kelas error:', error)
    }
  }

  const loadDataSiswa = async () => {
    try {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/siswa?kelasId=${kelasId}`,
        'siswa'
      )
      setDataSiswa(responseData.data || []);

    } catch (error: any) {
      const message = error?.data?.message || 'Tidak ada data siswa'
      console.error('Load siswa error:', error)
    }
  }

  const loadDataTransaksi = async () => {
    try {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/transaksi?kelasId=${kelasId}`,
        'siswa'
      )
      setDataSiswa(responseData.data || []);

    } catch (error: any) {
      const message = error?.data?.message || 'Tidak ada data siswa'
      console.error('Load siswa error:', error)
    }
  }
  

  useEffect(() => {
    loadDataSiswa()
    loadKelas()
    const loadUserInfo = async () => {
      const raw = await SecureStore.getItemAsync('user_info')
      if (raw) {
        const user = JSON.parse(raw)
        setNamaPetugas(user.nama_petugas)
        setKelasId(user.kelas_id)
      }
    }
    loadUserInfo()
  }, [])

  
  const dataRingkasan = {
    namaPetugas: namaPetugas || '-',
    namaKelas: namaKelas || '-',
    totalTabungan: "Rp 15.450.000",
    totalSiswa: dataSiswa.length || '-',
    transaksiHariIni: "12 Transaksi"
  };

  return (
    <View style={[styles.mainContainer, { backgroundColor: '#f8fafc' }]}>
      {/* ScrollView utama dengan padding atas dinamis + extra space agar lebih ke bawah */}
      <ScrollView 
        style={styles.container} 
        contentContainerStyle={[
          styles.contentContainer, 
          { 
            // insets.top mengambil tinggi status bar, kita tambah 25 agar posisi turun ideal
            paddingTop: (insets.top || 20) + 40 
          }
        ]}
      >
        
        {/* Bagian Atas / Selamat Datang + Tombol Pemicu Logout */}
        <View style={styles.headerSection}>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start' }}>
            <View>
              <Text style={styles.welcomeText}>Selamat Datang 👋</Text>
              <Text style={styles.adminName}>{dataRingkasan.namaPetugas}</Text>
              <Text style={styles.kelasName}>Walikelas : {dataRingkasan.namaKelas}</Text>
            </View>
            
            {/* Tombol Merah Keluar Sistem */}
            <TouchableOpacity 
              style={styles.logoutButton} 
              onPress={() => triggerLogoutGlobal()}
            >
              <FontAwesome name="power-off" size={16} color="#dc2626" />
              <Text style={styles.logoutText}>Keluar</Text>
            </TouchableOpacity>
          </View>
          <Text style={styles.subText}>Sistem Penginputan Tabungan Siswa</Text>
        </View>

        {/* Kartu Hero Informasi Saldo Total */}
        <View style={styles.balanceCard}>
          <View style={styles.cardInfo}>
            <Text style={styles.cardTitle}>Total Tabungan Semua Siswa</Text>
            <Text style={styles.cardBalance}>{dataRingkasan.totalTabungan}</Text>
          </View>
          <FontAwesome name="money" size={40} color="#fff" style={styles.cardIcon} />
        </View>

        {/* Baris Kotak Kecil Data Statistik */}
        <View style={styles.statsRow}>
          <View style={styles.statsBox}>
            <FontAwesome name="users" size={20} color="#0284c7" />
            <Text style={styles.statsValue}>{dataRingkasan.totalSiswa}</Text>
            <Text style={styles.statsLabel}>Aktif Menabung</Text>
          </View>
          
          <View style={styles.statsBox}>
            <FontAwesome name="exchange" size={20} color="#16a34a" />
            <Text style={styles.statsValue}>{dataRingkasan.transaksiHariIni}</Text>
            <Text style={styles.statsLabel}>Hari Ini</Text>
          </View>
        </View>

        {/* Grid Navigasi Menu Pintas Beranda */}
        <Text style={styles.sectionTitle}>Menu Pintas</Text>
        
        <View style={styles.menuGrid}>
          <TouchableOpacity style={styles.menuButton} activeOpacity={0.7}>
            <View style={[styles.iconWrapper, { backgroundColor: '#e0f2fe' }]}>
              <FontAwesome name="plus-circle" size={24} color="#0284c7" />
            </View>
            <Text style={styles.menuLabel}>Input Setoran</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.menuButton} activeOpacity={0.7}>
            <View style={[styles.iconWrapper, { backgroundColor: '#fee2e2' }]}>
              <FontAwesome name="minus-circle" size={24} color="#dc2626" />
            </View>
            <Text style={styles.menuLabel}>Tarik Tabungan</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.menuButton} activeOpacity={0.7}>
            <View style={[styles.iconWrapper, { backgroundColor: '#fef3c7' }]}>
              <FontAwesome name="search" size={24} color="#d97706" />
            </View>
            <Text style={styles.menuLabel}>Cari Siswa</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.menuButton} activeOpacity={0.7}>
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
    paddingHorizontal: 20,
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
});