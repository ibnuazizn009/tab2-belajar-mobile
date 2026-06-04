import React, { useState, useEffect } from 'react';
import * as SecureStore from 'expo-secure-store'
import { ScrollView, StyleSheet, Text, View, TextInput, FlatList, TouchableOpacity, Alert, RefreshControl } from 'react-native';
import Toast from 'react-native-toast-message'
import Modal from 'react-native-modal'
import { Stack } from 'expo-router';
import { FontAwesome } from '@expo/vector-icons';
import {tab2ApiService} from '../../services/Tab2apiservice'
import { SkeletonHome, SkeletonBox } from '../../components/SkeletonLoader';
import { exportSiswaListPdf } from '../../utils/exportPdf';
import { AppToast } from '@/components/ToastProvider'

export default function SiswaScreen() {
  const [refreshing, setRefreshing] = useState(false)
  const [search, setSearch] = useState('');
  const [dataSiswa, setDataSiswa] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [namaKelas, setNamaKelas] = useState('');
  const [isExporting, setIsExporting] = useState(false);
  const [kelasId, setKelasId] = useState('');

  const [modalVisible, setModalVisible] = useState(false);
  const [newNis, setNewNis] = useState('');
  const [newNama, setNewNama] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null)
  const [aktifmenabung, setAktifMenabung] = useState<number>(1) // default Ya

  const onRefresh = async () => {
    setRefreshing(true)
    await loadDataSiswa(kelasId)
    setRefreshing(false)
  }

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
          setKelasId(user.kelas_id);
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

  const handleTambahSiswa = async () => {
    if (!newNis.trim() || !newNama.trim()) {
      Alert.alert('Error', 'NIS dan Nama Siswa harus diisi!');
      return;
    }
  
    try {
      setErrorMessage(null)
      setIsSubmitting(true);
      
      const response = await tab2ApiService.post(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/tambah-siswa`, 
        {
          nis: newNis,
          nama: newNama,
          kelas_id: kelasId,
          saldo: 0,
          aktif_menabung: aktifmenabung 
        },
        'siswa',
        false 
      );
      
      if (response.success == true) {
        setModalVisible(false);
        setNewNis('');
        setNewNama('');
        setAktifMenabung(1);
        await loadDataSiswa(kelasId); 
      }else{
        setErrorMessage(response.message)
      }
    } catch (error) {
      console.error('Gagal menambah siswa:', error);
    } finally {
      setIsSubmitting(false);
    }
  };
  
  useEffect(() => {
    if (!errorMessage) return
    
    const timer = setTimeout(() => {
      setErrorMessage(null)
    }, 3500) 
  
    return () => clearTimeout(timer)
  }, [errorMessage])

  // return (
  //   <>
  //     <Stack.Screen options={{ title: 'Cari Siswa' }} />

  //     <View style={styles.container}>
  //       {/* Kolom Pencarian */}
  //       <View style={styles.searchSection}>
  //         <View style={styles.searchWrapper}>
  //           <FontAwesome name="search" size={16} color="#94a3b8" style={styles.searchIcon} />
  //           <TextInput
  //             style={styles.searchInput}
  //             placeholder="Cari nama atau NIS siswa..."
  //             value={search}
  //             onChangeText={setSearch}
  //           />
  //         </View>
  //       </View>

  //       {/* Baris Tombol Aksi */}
  //       <View style={styles.actionRow}>
  //         <TouchableOpacity
  //           style={[styles.actionButton, styles.exportButton]}
  //           onPress={handleExport}
  //           disabled={isExporting || dataSiswa.length === 0}
  //         >
  //           <FontAwesome name="file-pdf-o" size={14} color="#fff" />
  //           <Text style={styles.buttonText}>
  //             {isExporting ? 'Mengexport...' : 'Export PDF'}
  //           </Text>
  //         </TouchableOpacity>

  //         <TouchableOpacity
  //           style={[styles.actionButton, styles.addButton]}
  //           onPress={() => setModalVisible(true)}
  //         >
  //           <FontAwesome name="user-plus" size={14} color="#fff" />
  //           <Text style={styles.buttonText}>Tambah Siswa</Text>
  //         </TouchableOpacity>
  //       </View>

  //       {/* List Daftar Siswa */}
  //       {isLoading ? (
  //         <View style={{ padding: 20 }}>
  //           {[0, 1, 2, 3, 4].map(i => (
  //             <View key={i} style={[styles.siswaCard, { marginBottom: 12 }]}>
  //               <SkeletonBox width={36} height={36} style={{ borderRadius: 18, marginRight: 12 }} />
  //               <View style={{ flex: 1 }}>
  //                 <SkeletonBox width={140} height={13} style={{ marginBottom: 8 }} />
  //                 <SkeletonBox width={100} height={11} />
  //               </View>
  //               <View style={{ alignItems: 'flex-end' }}>
  //                 <SkeletonBox width={30} height={11} style={{ marginBottom: 6 }} />
  //                 <SkeletonBox width={80} height={13} />
  //               </View>
  //             </View>
  //           ))}
  //         </View>
  //       ) : (
  //         <FlatList
  //           data={filteredSiswa}
  //           keyExtractor={(item) => item.nis}
  //           contentContainerStyle={{ padding: 20, paddingBottom: 40 }}
  //           refreshControl={ // ✅
  //             <RefreshControl
  //               refreshing={refreshing}
  //               onRefresh={onRefresh}
  //               colors={['#0284c7']}
  //               tintColor="#0284c7"
  //             />
  //           }
  //           ListEmptyComponent={
  //             <View style={styles.emptyState}>
  //               <FontAwesome name="folder-open" size={40} color="#cbd5e1" />
  //               <Text style={styles.emptyText}>Siswa tidak ditemukan</Text>
  //             </View>
  //           }
  //           renderItem={({ item }) => (
  //             <View style={styles.siswaCard}>
  //               <View style={styles.profileBadge}>
  //                 <FontAwesome name="user-circle" size={36} color="#0284c7" />
  //               </View>
  //               <View style={styles.infoSection}>
  //                 <Text style={styles.siswaNama}>{item.nama}</Text>
  //                 <Text style={styles.siswaDetail}>NIS: {item.nis} • Kelas {item.nama_kelas}</Text>
  //               </View>
  //               <View style={styles.saldoSection}>
  //                 <Text style={styles.saldoLabel}>Saldo</Text>
  //                 <Text style={styles.saldoValue}>{formatRupiah(item.saldo)}</Text>
  //               </View>
  //             </View>
  //           )}
  //         />
  //       )}
  //     </View>

  //     {/* Pop-up Modal Tambah Siswa */}
  //     <Modal
  //         isVisible={modalVisible}
  //         onBackdropPress={() => setModalVisible(false)}
  //         onBackButtonPress={() => setModalVisible(false)}
  //         animationIn="fadeIn"
  //         animationOut="fadeOut"
  //         backdropTransitionOutTiming={0}
  //         useNativeDriverForBackdrop
  //         backdropOpacity={0.2}
  //         style={{ margin: 0 }} 
  //       >
  //       <View style={styles.modalOverlay}>
  //         <View style={styles.modalContent}>
  //           <Text style={styles.modalTitle}>Tambah Siswa Baru</Text>
            
  //           <Text style={styles.inputLabel}>NIS Siswa</Text>
  //           <TextInput
  //             style={styles.modalInput}
  //             placeholder="Masukkan NIS..."
  //             value={newNis}
  //             onChangeText={setNewNis}
  //             keyboardType="numeric"
  //           />

  //           <Text style={styles.inputLabel}>Nama Lengkap</Text>
  //           <TextInput
  //             style={styles.modalInput}
  //             placeholder="Masukkan nama siswa..."
  //             value={newNama}
  //             onChangeText={setNewNama}
  //           />
  //           <Text style={styles.inputLabel}>Aktif Menabung</Text>
  //           <View style={{ flexDirection: 'row', gap: 10, marginBottom: 12 }}>
  //             <TouchableOpacity
  //               onPress={() => setAktifMenabung(1)}
  //               style={{
  //                 flex: 1,
  //                 paddingVertical: 10,
  //                 borderRadius: 8,
  //                 borderWidth: 1.5,
  //                 borderColor: aktifmenabung === 1 ? '#0284c7' : '#cbd5e1',
  //                 backgroundColor: aktifmenabung === 1 ? '#e0f2fe' : '#f8fafc',
  //                 alignItems: 'center',
  //               }}
  //             >
  //               <Text style={{ color: aktifmenabung === 1 ? '#0284c7' : '#94a3b8', fontWeight: 'bold' }}>
  //                 Ya
  //               </Text>
  //             </TouchableOpacity>

  //             <TouchableOpacity
  //               onPress={() => setAktifMenabung(0)}
  //               style={{
  //                 flex: 1,
  //                 paddingVertical: 10,
  //                 borderRadius: 8,
  //                 borderWidth: 1.5,
  //                 borderColor: aktifmenabung === 0 ? '#dc2626' : '#cbd5e1',
  //                 backgroundColor: aktifmenabung === 0 ? '#fee2e2' : '#f8fafc',
  //                 alignItems: 'center',
  //               }}
  //             >
  //               <Text style={{ color: aktifmenabung === 0 ? '#dc2626' : '#94a3b8', fontWeight: 'bold' }}>
  //                 Tidak
  //               </Text>
  //             </TouchableOpacity>
  //           </View>
  //           {errorMessage && (
  //             <Text style={{ color: '#dc2626', fontSize: 15, marginTop: 4, marginLeft: 4 }}>
  //               {errorMessage}
  //             </Text>
  //           )}

  //           <View style={styles.modalActionRow}>
  //             <TouchableOpacity 
  //               style={[styles.modalButton, styles.cancelButton]} 
  //               onPress={() => {
  //                 setModalVisible(false);
  //                 setNewNis('');
  //                 setNewNama('');
  //               }}
  //               disabled={isSubmitting}
  //             >
  //               <Text style={styles.cancelButtonText}>Batal</Text>
  //             </TouchableOpacity>

  //             <TouchableOpacity 
  //               style={[styles.modalButton, styles.submitButton]} 
  //               onPress={handleTambahSiswa}
  //               disabled={isSubmitting}
  //             >
  //               <Text style={styles.submitButtonText}>
  //                 {isSubmitting ? 'Menyimpan...' : 'Simpan'}
  //               </Text>
  //             </TouchableOpacity>
  //           </View>
  //         </View>
  //       </View>
  //     </Modal>
  //     <AppToast topOffset={10} />
  //   </>
  // );

  return (
    <>
      <Stack.Screen options={{ title: 'Cari Siswa' }} />
  
        <ScrollView 
          style={styles.container}
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
        <View style={styles.actionRow}>
          <TouchableOpacity
            style={[styles.actionButton, styles.exportButton]}
            onPress={handleExport}
            disabled={isExporting || dataSiswa.length === 0}
          >
            <FontAwesome name="file-pdf-o" size={14} color="#fff" />
            <Text style={styles.buttonText}>
              {isExporting ? 'Mengexport...' : 'Export PDF'}
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
                    <Text style={styles.siswaNama}>{item.nama}</Text>
                    <Text style={styles.siswaDetail}>NIS: {item.nis} • Kelas {item.nama_kelas}</Text>
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
              <Text style={{ color: '#dc2626', fontSize: 15, marginTop: 4, marginLeft: 4 }}>
                {errorMessage}
              </Text>
            )}

            <View style={styles.modalActionRow}>
              <TouchableOpacity 
                style={[styles.modalButton, styles.cancelButton]} 
                onPress={() => {
                  setModalVisible(false);
                  setNewNis('');
                  setNewNama('');
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
      <AppToast topOffset={10} />
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
  
  // Style Modifikasi untuk Tombol Aksi Bersandingan
  actionRow: { flexDirection: 'row', paddingHorizontal: 8, paddingVertical: 10, gap: 10 },
  actionButton: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', padding: 12, borderRadius: 10, gap: 6 },
  exportButton: { backgroundColor: '#dc2626' },
  addButton: { backgroundColor: '#0284c7' },
  buttonText: { color: '#fff', fontWeight: '600', fontSize: 13 },

  // Style untuk Pop-up / Modal
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', alignItems: 'center', padding: 20 },
  modalContent: { width: '90%', backgroundColor: '#fff', borderRadius: 16, padding: 20, elevation: 5, alignSelf: 'center', },
  modalTitle: { fontSize: 18, fontWeight: 'bold', color: '#1e293b', marginBottom: 16 },
  inputLabel: { fontSize: 13, fontWeight: '600', color: '#64748b', marginBottom: 6 },
  modalInput: { backgroundColor: '#f1f5f9', borderRadius: 8, paddingHorizontal: 12, height: 44, fontSize: 14, color: '#1e293b', marginBottom: 16, borderWidth: 1, borderColor: '#e2e8f0' },
  modalActionRow: { flexDirection: 'row', justifyContent: 'flex-end', gap: 10, marginTop: 8 },
  modalButton: { paddingVertical: 10, paddingHorizontal: 16, borderRadius: 8, minWidth: 80, alignItems: 'center' },
  cancelButton: { backgroundColor: '#f1f5f9' },
  cancelButtonText: { color: '#64748b', fontWeight: '600' },
  submitButton: { backgroundColor: '#0284c7' },
  submitButtonText: { color: '#fff', fontWeight: '600' }
});