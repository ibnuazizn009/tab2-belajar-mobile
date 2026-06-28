import React, { useState, useEffect } from 'react';
import * as SecureStore from 'expo-secure-store';
import * as DocumentPicker from 'expo-document-picker';
import * as FileSystem from 'expo-file-system';
import { File, Paths } from 'expo-file-system/next';
import * as Sharing from 'expo-sharing';
import { ScrollView, StyleSheet, Text, View, TextInput, TouchableOpacity, Alert, RefreshControl, Linking } from 'react-native';
import Modal from 'react-native-modal';
import { Stack, router } from 'expo-router';
import { FontAwesome } from '@expo/vector-icons';
import { tab2ApiService } from '../../services/Tab2apiservice';
import { SkeletonBox } from '../../components/SkeletonLoader';
import { exportSiswaListPdf } from '../../utils/exportPdf';
import { CHECK_FEATURE } from '@/constants/Features'; // 🎯 1. Import helper fitur bisnis
import { tab2Toast } from '@/utils/tab2Toast';
import { downloadAndSaveToDocuments } from '@/utils/exportPdf';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { MinimalBlueBackground } from "@/components/BackgroundLinearGradient";

export default function SiswaScreen() {
  const insets = useSafeAreaInsets();

  const [refreshing, setRefreshing] = useState(false);
  const [search, setSearch] = useState('');
  const [dataSiswa, setDataSiswa] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [namaKelas, setNamaKelas] = useState('');
  const [isExporting, setIsExporting] = useState(false);
  const [kelasId, setKelasId] = useState('');
  const [noWaOrangTua, setNoWaOrangTua] = useState('');

  // 🎯 2. State baru untuk menampung info user & paket layanan
  const [userInfo, setUserInfo] = useState<any>(null);

  const [modalVisible, setModalVisible] = useState(false);
  const [newNis, setNewNis] = useState('');
  const [newNama, setNewNama] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [aktifmenabung, setAktifMenabung] = useState<number>(1); // default Ya

  const onRefresh = async () => {
    setRefreshing(true);
    await loadDataSiswa(kelasId);
    setRefreshing(false);
  };

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
      );
      if (responseData && responseData.data) {
        setDataSiswa(responseData.data || []);
        setNamaKelas(responseData.data[0]?.nama_kelas || 'Kelas');  
      }
    } catch (error: any) {
      console.error('Load siswa error:', error);
    } finally {
      setIsLoading(false);
    }
  };
  
  const filteredSiswa = dataSiswa.filter(item => 
    item.nama_siswa.toLowerCase().includes(search.toLowerCase()) || item.nis.includes(search)
  );

  useEffect(() => {
    const initializeData = async () => {
      try {
        const raw = await SecureStore.getItemAsync('user_info');
        if (raw) {
          const user = JSON.parse(raw);    
          setUserInfo(user); // 🎯 Simpan ke state agar bisa dibaca paket_layanannya
          await loadDataSiswa(user.kelas_id);
          setKelasId(user.kelas_id);
        }
      } catch (error) {
        console.error('Gagal inisialisasi data:', error);
      }
    };
  
    initializeData();
  }, []);

  const handleExport = async () => {
    const canDownload = CHECK_FEATURE(userInfo?.paket_layanan, 'DOWNLOAD_REPORT');
    
    if (!canDownload) {
      Alert.alert(
        '🔒 Fitur Terkunci',
        'Fitur cetak PDF hanya tersedia pada paket Golden All Akses. Silakan hubungi Admin Utama untuk upgrade paket sekolah Anda.'
      );
      return;
    }

    try {
      setIsExporting(true);
      await exportSiswaListPdf(dataSiswa, namaKelas);
    } catch (e) {
      console.error('Export error:', e);
    } finally {
      setIsExporting(false);
    }
  };

  const handleTambahSiswa = async () => {
    if (!newNis.trim() || !newNama.trim()) {
      tab2Toast.error('Form Tidak Lengkap', 'NIS dan Nama Siswa harus diisi.');
      return;
    }
  
    try {
      setErrorMessage(null);
      setIsSubmitting(true);
      
      const response = await tab2ApiService.post(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/tambah-siswa`, 
        {
          nis: newNis,
          nama_siswa: newNama,
          kelas_id: kelasId,
          aktif_menabung: aktifmenabung,
          no_wa_orang_tua: noWaOrangTua.trim() === '' ? null : noWaOrangTua
        },
        'siswa',
        true
      );
      
      if (response?.success === true) {
        setModalVisible(false);
        setNewNis('');
        setNewNama('');
        setNoWaOrangTua('');
        setAktifMenabung(1);
        await loadDataSiswa(kelasId);
  
        tab2Toast.success('Siswa Ditambahkan', `${newNama} berhasil ditambahkan.`);
  
      } else {
        setErrorMessage(response?.message || 'Gagal menyimpan data.');
        tab2Toast.error('Gagal Menyimpan', response?.message || 'Gagal menyimpan data.');
      }
    } catch (error: any) {
      const errorMsg = error?.response?.data?.message || 'Terjadi kesalahan.';
      setErrorMessage(errorMsg);
      tab2Toast.error('Terjadi Kesalahan', errorMsg);
    } finally {
      setIsSubmitting(false);
    }
  };
  
  const handleDownloadTemplate = async () => {
    const token = await SecureStore.getItemAsync('access_token');
  
    tab2Toast.info('Mengunduh', 'Sedang menyiapkan template...');
  
    await downloadAndSaveToDocuments(
      `${process.env.EXPO_PUBLIC_API_URL}/siswa/template`,
      'Template_Siswa.xlsx',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      { 'Authorization': `Bearer ${token}` }
    );
  };
  

  const handleImportExcel = async () => 
  {
    // Cek fitur hanya untuk paket Golden
    const canImport = CHECK_FEATURE(userInfo?.paket_layanan, 'IMPORT_EXCEL');
    
    if (!canImport) {
      Alert.alert(
        '🔒 Fitur Terkunci',
        'Fitur Import Excel hanya tersedia pada paket Golden All Akses. Silakan hubungi Admin Utama untuk upgrade paket sekolah Anda.'
      );
      return;
    }

    try {
      const result = await DocumentPicker.getDocumentAsync({ type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
      
      if (!result.canceled) {
        const file = result.assets[0];
        const formData = new FormData();
        formData.append('file', { uri: file.uri, name: file.name, type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' } as any);
        formData.append('kelas_id', kelasId);

        const response = await tab2ApiService.postMultipart(
          `${process.env.EXPO_PUBLIC_API_URL}/siswa/import-excel`,
          formData
        );

        if (response?.success) {
          tab2Toast.success('Berhasil', 'Data siswa berhasil diimport.');
          await loadDataSiswa(kelasId);
        }
      }
    } catch (e) {
      tab2Toast.error('Gagal', 'Terjadi kesalahan saat import.');
    }
  };

  useEffect(() => {
    if (!errorMessage) return;
    
    const timer = setTimeout(() => {
      setErrorMessage(null);
    }, 3500); 
  
    return () => clearTimeout(timer);
  }, [errorMessage]);

  // Cek kecocokan warna tombol export berdasarkan status lisen
  const isGoldenTier = CHECK_FEATURE(userInfo?.paket_layanan, 'DOWNLOAD_REPORT');

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

          <Text style={styles.headerTitle}>Cari Siswa</Text>

          <View style={{ width: 40 }} />
        </View>
      </View>
      <ScrollView 
        style={[styles.container, { backgroundColor: 'transparent' }]}
        contentContainerStyle={{ flexGrow: 1 }} 
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
  
        {/* Baris Tombol Aksi */}
        <View style={styles.actionContainer}>
  
          {/* Baris 1: Export & Tambah (Horizontal) */}
          <View style={styles.actionRow}>
            <TouchableOpacity
              style={[
                styles.actionButton, 
                styles.exportButton, 
                !isGoldenTier && { backgroundColor: '#94a3b8' }
              ]}
              onPress={handleExport}
              disabled={isExporting || dataSiswa.length === 0}
            >
              <FontAwesome name={isGoldenTier ? "file-pdf-o" : "lock"} size={14} color="#fff" />
              <Text style={styles.buttonText}>
                {isExporting ? 'Mengexport...' : isGoldenTier ? 'Export PDF' : 'PDF (Golden Only)'}
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.actionButton, styles.addButton]}
              onPress={() => setModalVisible(true)}
            >
              <FontAwesome name="user-plus" size={14} color="#fff" />
              <Text style={styles.buttonText}>Tambah Siswa</Text>
            </TouchableOpacity>
          </View>

          {/* Baris 2: Download Template & Import (Vertikal/Bawahnya) */}
          <View style={styles.importSection}>
            <TouchableOpacity 
              style={[styles.actionButton, styles.templateButton]} 
              onPress={handleDownloadTemplate}
            >
              <FontAwesome name="download" size={14} color="#fff" />
              <Text style={styles.buttonText}>Download Template</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[
                styles.actionButton, 
                styles.importButton,
                !isGoldenTier && { backgroundColor: '#94a3b8' }
              ]}
              onPress={handleImportExcel}
            >
              <FontAwesome name={isGoldenTier ? "file-excel-o" : "lock"} size={14} color="#fff" />
              <Text style={styles.buttonText}>
                {isGoldenTier ? 'Import Excel' : 'Import (Golden Only)'}
              </Text>
            </TouchableOpacity>
          </View>

        </View>
  
        {/* List Daftar Siswa */}
        {isLoading ? (
          <View style={{ padding: 8 }}>
            {[0, 1, 2, 3, 4].map(i => (
              <View key={i} style={[styles.siswaCard, { marginBottom: 12 }]}>
                <SkeletonBox width={36} height={36} style={{ borderRadius: 18, marginRight: 12 }} />
                <View style={{ flex: 1 }}>
                  <SkeletonBox width={140} height={13} style={{ marginBottom: 8 }} />
                  <SkeletonBox width={100} height={11} />
                </View>
                <View style={{ alignItems: 'flex-end' }}>
                  <SkeletonBox width={30} height={11} style={{ marginBottom: 6 }} />
                  <SkeletonBox width={80} height={13} />
                </View>
              </View>
            ))}
          </View>
        ) : (
          <View style={{ padding: 8, paddingBottom: 40 }}>
            {filteredSiswa.length === 0 ? (
              <View style={styles.emptyState}>
                <FontAwesome name="folder-open" size={40} color="#cbd5e1" />
                <Text style={styles.emptyText}>Siswa tidak ditemukan</Text>
              </View>
            ) : (
              filteredSiswa.map((item) => (
                <View key={item.nis} style={styles.siswaCard}>
                  <View style={styles.profileBadge}>
                    <FontAwesome name="user-circle" size={36} color="#0284c7" />
                  </View>
                  <View style={styles.infoSection}>
                    <Text style={styles.siswaNama}>{item.nama_siswa}</Text>
                    <Text style={styles.siswaDetail}>NIS: {item.nis} • Kelas {namaKelas}</Text>
                  </View>
                  <View style={styles.saldoSection}>
                    <Text style={styles.saldoLabel}>Saldo</Text>
                    <Text style={styles.saldoValue}>{formatRupiah(item.saldo)}</Text>
                  </View>
                </View>
              ))
            )}
          </View>
        )}
      </ScrollView>
  
      {/* Pop-up Modal Tambah Siswa */}
      <Modal
        isVisible={modalVisible}
        onBackdropPress={() => setModalVisible(false)}
        onBackButtonPress={() => setModalVisible(false)}
        animationIn="slideInUp"
        animationOut="slideOutDown"
        animationInTiming={350}  
        animationOutTiming={300}
        useNativeDriver={true}
        useNativeDriverForBackdrop={true}
        backdropTransitionInTiming={350}
        backdropTransitionOutTiming={0}
        backdropOpacity={0.2}
        style={{ margin: 0 }} 
      >
        <View style={styles.modalContent}>
          <Text style={styles.modalTitle}>Tambah Siswa Baru</Text>
          
          {/* 🎯 5. BANNER PRIVILEGE: Jika paket sekolah adalah FREE, ingatkan kuota limit 10 siswa */}
          {userInfo?.paket_layanan === 'free' && (
            <View style={styles.premiumBanner}>
              <FontAwesome name="info-circle" size={14} color="#b45309" style={{ marginRight: 6 }} />
              <Text style={styles.premiumBannerText}>
                Mode Free: Kuota maksimal dibatasi 10 siswa per sekolah.
              </Text>
            </View>
          )}
          
          <Text style={styles.inputLabel}>NIS Siswa</Text>
          <TextInput
            style={styles.modalInput}
            placeholder="Masukkan NIS..."
            value={newNis}
            onChangeText={setNewNis}
            keyboardType="numeric"
          />

          <Text style={styles.inputLabel}>Nama Lengkap</Text>
          <TextInput
            style={styles.modalInput}
            placeholder="Masukkan nama siswa..."
            value={newNama}
            onChangeText={setNewNama}
          />

          <Text style={styles.inputLabel}>No. WA Orang Tua (Opsional)</Text>
          <TextInput
            style={styles.modalInput}
            placeholder="Contoh: 0812xxxxxx"
            value={noWaOrangTua}
            onChangeText={setNoWaOrangTua}
            keyboardType="phone-pad"
          />
          
          <Text style={styles.inputLabel}>Aktif Menabung</Text>
          <View style={{ flexDirection: 'row', gap: 10, marginBottom: 12 }}>
            <TouchableOpacity
              onPress={() => setAktifMenabung(1)}
              style={{
                flex: 1,
                paddingVertical: 10,
                borderRadius: 8,
                borderWidth: 1.5,
                borderColor: aktifmenabung === 1 ? '#0284c7' : '#cbd5e1',
                backgroundColor: aktifmenabung === 1 ? '#e0f2fe' : '#f8fafc',
                alignItems: 'center',
              }}
            >
              <Text style={{ color: aktifmenabung === 1 ? '#0284c7' : '#94a3b8', fontWeight: 'bold' }}>
                Ya
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              onPress={() => setAktifMenabung(0)}
              style={{
                flex: 1,
                paddingVertical: 10,
                borderRadius: 8,
                borderWidth: 1.5,
                borderColor: aktifmenabung === 0 ? '#dc2626' : '#cbd5e1',
                backgroundColor: aktifmenabung === 0 ? '#fee2e2' : '#f8fafc',
                alignItems: 'center',
              }}
            >
              <Text style={{ color: aktifmenabung === 0 ? '#dc2626' : '#94a3b8', fontWeight: 'bold' }}>
                Tidak
              </Text>
            </TouchableOpacity>
          </View>
          
          {errorMessage && (
            <Text style={{ color: '#dc2626', fontSize: 14, marginTop: 4, marginBottom: 8, marginLeft: 4, fontWeight: '500' }}>
              ⚠️ {errorMessage}
            </Text>
          )}

          <View style={styles.modalActionRow}>
            <TouchableOpacity 
              style={[styles.modalButton, styles.cancelButton]} 
              onPress={() => {
                setModalVisible(false);
                setNewNis('');
                setNewNama('');
                setErrorMessage(null);
              }}
              disabled={isSubmitting}
            >
              <Text style={styles.cancelButtonText}>Batal</Text>
            </TouchableOpacity>

            <TouchableOpacity 
              style={[styles.modalButton, styles.submitButton]} 
              onPress={handleTambahSiswa}
              disabled={isSubmitting}
            >
              <Text style={styles.submitButtonText}>
                {isSubmitting ? 'Menyimpan...' : 'Simpan'}
              </Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  searchSection: { backgroundColor: '#fff', paddingHorizontal: 8, paddingVertical: 12, borderBottomWidth: 1, borderColor: '#e2e8f0' },
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
  
  actionRow: { flexDirection: 'row', paddingHorizontal: 8, paddingVertical: 10, gap: 10 },
  actionButton: { 
    flex: 1, 
    flexDirection: 'row', 
    alignItems: 'center', 
    justifyContent: 'center', 
    padding: 12, 
    borderRadius: 10, 
    gap: 6 
  },
  exportButton: { backgroundColor: '#ea580c' },
  addButton: { backgroundColor: '#2563eb' },
  buttonText: { 
    color: '#fff', 
    fontWeight: '600', 
    fontSize: 13 
  },

  modalContent: { width: '90%', backgroundColor: '#fff', borderRadius: 16, padding: 20, elevation: 5, alignSelf: 'center' },
  modalTitle: { fontSize: 18, fontWeight: 'bold', color: '#1e293b', marginBottom: 16 },
  inputLabel: { fontSize: 13, fontWeight: '600', color: '#64748b', marginBottom: 6 },
  modalInput: { backgroundColor: '#f1f5f9', borderRadius: 8, paddingHorizontal: 12, height: 44, fontSize: 14, color: '#1e293b', marginBottom: 16, borderWidth: 1, borderColor: '#e2e8f0' },
  modalActionRow: { flexDirection: 'row', justifyContent: 'flex-end', gap: 10, marginTop: 8 },
  modalButton: { paddingVertical: 10, paddingHorizontal: 16, borderRadius: 8, minWidth: 80, alignItems: 'center' },
  cancelButton: { backgroundColor: '#f1f5f9' },
  cancelButtonText: { color: '#64748b', fontWeight: '600' },
  submitButton: { backgroundColor: '#0284c7' },
  submitButtonText: { color: '#fff', fontWeight: '600' },
  
  // Tambahan Styling Baru untuk Banner Lisensi Bisnis
  premiumBanner: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    backgroundColor: '#fffbeb', 
    padding: 10, 
    borderRadius: 8, 
    marginBottom: 14, 
    borderWidth: 1, 
    borderColor: '#fef3c7' 
  },
  premiumBannerText: { 
    color: '#b45309', 
    fontSize: 12, 
    fontWeight: '500', 
    flex: 1 
  },
  actionContainer: {
    gap: 10, // Memberi jarak antara baris atas dan bawah
    marginVertical: 10,
  },
  importSection: { flexDirection: 'row', paddingHorizontal: 8, gap: 10 },
  templateButton: { 
    backgroundColor: '#7c3aed',
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 12,
    borderRadius: 10,
    gap: 6
  },
  importButton: { backgroundColor: '#059669' },
  lockedButton: { backgroundColor: '#94a3b8' },
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
});