import React, { useState, useEffect } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import * as SecureStore from 'expo-secure-store'
import { useLocalSearchParams } from 'expo-router';
import Toast from 'react-native-toast-message';
import { StyleSheet, Text, View, TextInput, TouchableOpacity, ScrollView, Alert, ActivityIndicator, RefreshControl } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import {tab2ApiService} from '../../services/Tab2apiservice'
import { AppToast } from '@/components/ToastProvider'
import { Dropdown } from 'react-native-element-dropdown';

export default function TransaksiScreen() {
  const { defaultTipe, hideOther } = useLocalSearchParams<{ 
    defaultTipe: 'setor' | 'tarik', 
    hideOther: string 
  }>();
  
  const queryClient = useQueryClient();
  const [nis, setNis] = useState('');
  const [nama, setNama] = useState('');
  const [tipe, setTipe] = useState<'setor' | 'tarik' | ''>(defaultTipe || '');
  const [nominal, setNominal] = useState('');
  const [kelasId, setKelasId] = useState('');
  const [listSiswa, setListSiswa] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [refreshing, setRefreshing] = useState(false);

  const onRefresh = React.useCallback(async () => {
    setRefreshing(true);
    try {
      // 💡 Masukkan fungsi penjemput data API kamu di sini, contoh:
      // await fetchListSiswa(); 
      
      // Opsional: Reset form jika ingin dikosongkan saat di-refresh
      setNis('');
      setNama('');
      setNominal('');
    } catch (error) {
      console.error("Gagal refresh data:", error);
    } finally {
      setRefreshing(false);
    }
  }, []);

  const formatInputNominal = (value: string) => {
    const nomorMurni = value.replace(/\D/g, '');
    return nomorMurni.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  };

  const postTransaksiData = async() => {
    if (!nis.trim()) return;
    if (!tipe) return;
    if (!nominal || Number(nominal) <= 0) return;
  
    try {
      setIsSubmitting(true)
      setLoading(true);

      const payload = {
        nis: nis,
        tipe: tipe,
        nominal: Number(nominal), // Konversi string input ke angka
      };

      const response = await tab2ApiService.post(
        `${process.env.EXPO_PUBLIC_API_URL}/transaksi/transaksi`,
        payload,
        'transaksi' 
      );

      if (response && response.success) {
        queryClient.invalidateQueries({ queryKey: ['totalTabunganSiswa'] });
        queryClient.invalidateQueries({ queryKey: ['transaksiHariIni'] });
        setNis('');
        setNama('');
        setTipe('');
        setNominal('');
      }
    } catch (error) {
      console.error('Submit transaksi error:', error);
    } finally {
      setIsSubmitting(false);
      setLoading(false);
    }
  }

  const loadDataSiswa = async (idYangDipilih: string) => {
    try {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/siswa-per-kelas?kelasId=${idYangDipilih}`,
        'siswa'
      )

      if (responseData && responseData.data) {
        const siswaAktif = responseData.data.filter((siswa: any) => siswa.isActive !== 0);
        setListSiswa(siswaAktif || []);
      }

    } catch (error: any) {
      const message = error?.data?.message || 'Tidak ada data siswa'
      console.error('Load siswa error:', error)
    }
  }

  useEffect(() => {
    const initializeData = async () => {
      try {
        const raw = await SecureStore.getItemAsync('user_info')
        
        if (raw) {
          const user = JSON.parse(raw)
          setKelasId(user.kelas_id) // Tetap set state untuk kebutuhan UI lain jika ada
          
          await loadDataSiswa(user.kelas_id)
        }
      } catch (error) {
        console.error('Gagal inisialisasi data:', error)
      }
    }
  
    initializeData()
  }, [])

  useEffect(() => {
    setTipe(defaultTipe || '');
  }, [defaultTipe]);

  const handleSimpan = () => {
    postTransaksiData()
  };

  // return (
  //   <ScrollView style={styles.container} contentContainerStyle={styles.contentContainer}>
  //     <View style={styles.formCard}>
  //       <Text style={styles.sectionTitle}>Pencatatan Tabungan</Text>

  //       {/* Input NIS */}
  //       <View style={styles.inputGroup}>
  //         <Text style={styles.label}>Nomor Induk Siswa (NIS)</Text>
  //         <View style={styles.inputWrapper}>
  //           <FontAwesome name="id-card" size={16} color="#64748b" style={styles.icon} />
  //           <TextInput
  //             style={styles.input}
  //             placeholder="Contoh: 202601001"
  //             keyboardType="number-pad"
  //             value={nis}
  //             onChangeText={(val) => {
  //               setNis(val);

  //               const siswaDitemukan = listSiswa.find((siswa) => siswa.nis === val);

  //               if (siswaDitemukan) {
  //                 setNama(siswaDitemukan.nama);
  //               } else {
  //                 setNama('');

  //                 if (val.length === 7) { 
  //                   Toast.show({
  //                     type: 'error',
  //                     text1: 'Siswa Tidak Ditemukan',
  //                     text2: 'Siswa bukan dari kelas ini, NIS salah, atau siswa sudah lulus.',
  //                     position: 'top',
  //                     visibilityTime: 4000,
  //                     props: {
  //                       style: {
  //                         alignSelf: 'flex-end', // Menggeser komponen ke kanan
  //                         marginRight: 20,
  //                         width: '70%', 
  //                       }
  //                     }
  //                   });
  //                 }
  //               }
  //             }}
  //             editable={!loading}
  //           />
  //         </View>
  //       </View>

  //       {/* Input Nama Siswa */}
  //       <View style={styles.inputGroup}>
  //         <Text style={styles.label}>Nama Siswa (Otomatis/Manual)</Text>
  //         <View style={styles.inputWrapper}>
  //           <FontAwesome name="user" size={16} color="#64748b" style={styles.icon} />
  //           <TextInput
  //             style={styles.input}
  //             placeholder="Nama akan muncul otomatis"
  //             value={nama}
  //             onChangeText={setNama}
  //             editable={false}
  //           />
  //         </View>
  //       </View>

  //       {/* Pilihan Tipe Transaksi */}
  //       <Text style={styles.label}>Jenis Transaksi</Text>
  //       <View style={styles.typeRow}>
  //         {!(hideOther === 'true' && tipe === 'tarik') && (
  //           <TouchableOpacity 
  //             style={[styles.typeButton, tipe === 'setor' && styles.activeSetor]} 
  //             onPress={() => setTipe('setor')}
  //           >
  //             <FontAwesome name="plus-circle" size={18} color={tipe === 'setor' ? '#fff' : '#16a34a'} />
  //             <Text style={[styles.typeText, tipe === 'setor' && styles.activeTypeText]}>Setor Tunai</Text>
  //           </TouchableOpacity>
  //         )}

  //         {!(hideOther === 'true' && tipe === 'setor') && (
  //             <TouchableOpacity 
  //               style={[styles.typeButton, tipe === 'tarik' && styles.activeTarik]} 
  //               onPress={() => setTipe('tarik')}
  //             >
  //               <FontAwesome name="minus-circle" size={18} color={tipe === 'tarik' ? '#fff' : '#dc2626'} />
  //               <Text style={[styles.typeText, tipe === 'tarik' && styles.activeTypeText]}>Tarik Tunai</Text>
  //             </TouchableOpacity>
  //           )}
  //       </View>

  //       {/* Input Nominal */}
  //       <View style={styles.inputGroup}>
  //         <Text style={styles.label}>Nominal Transaksi (Rp)</Text>
  //         <View style={styles.inputWrapper}>
  //           <Text style={styles.rpText}>Rp</Text>
  //           <TextInput
  //             style={styles.input}
  //             placeholder="Contoh: 50.000"
  //             keyboardType="number-pad"
  //             // TAMPILAN: Otomatis diubah ke format bertitik saat dirender
  //             value={formatInputNominal(nominal)} 
  //             onChangeText={(val) => {
  //               // Hapus titik sebelum disimpan ke state agar nilainya tetap angka murni (Contoh: "50000")
  //               const angkaMurni = val.replace(/\D/g, '');
  //               setNominal(angkaMurni);
  //             }}
  //             editable={!loading}
  //           />
  //         </View>
  //       </View>

  //       {/* Tombol Simpan */}
  //       <TouchableOpacity style={styles.submitButton} onPress={handleSimpan}>
  //         <FontAwesome name="save" size={18} color="#fff" />
  //         <Text style={styles.submitButtonText}>Simpan Transaksi</Text>
  //       </TouchableOpacity>
  //     </View>

  //     <AppToast topOffset={20} />
      
  //   </ScrollView>
  // );

  return (
    <ScrollView 
      style={styles.container} 
      contentContainerStyle={styles.contentContainer}
      refreshControl={
        <RefreshControl 
          refreshing={refreshing} 
          onRefresh={onRefresh} 
          colors={['#2563eb']} // Warna spinner di Android
          tintColor="#2563eb"  // Warna spinner di iOS
        />
      }
      >
      <View style={styles.formCard}>
        <Text style={styles.sectionTitle}>Pencatatan Tabungan</Text>
  
        {/* 💡 2. DROPDOWN NAMA SISWA (BISA DICARI) */}
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Pilih Nama Siswa</Text>
          <View style={styles.inputWrapperDropdown}>
            <FontAwesome name="user" size={16} color="#64748b" style={[styles.iconDropdown, { marginRight: 12 }]} />
            <Dropdown
              style={styles.dropdown}
              placeholderStyle={styles.placeholderStyle}
              selectedTextStyle={styles.selectedTextStyle}
              inputSearchStyle={styles.inputSearchStyle}
              data={listSiswa} // Data array siswa Anda
              search
              maxHeight={300}
              labelField="nama"
              valueField="nis" 
              placeholder="Cari atau pilih nama siswa..."
              searchPlaceholder="Ketik nama siswa..."
              value={nis}
              disable={loading}
              onChange={(item: any) => {
                setNis(item.nis);
                setNama(item.nama);
              }}
              containerStyle={styles.dropdownContainer}
              renderLeftIcon={() => null} 
            />
          </View>
        </View>
  
        {/* 💡 3. INPUT NIS (SEKARANG MENJADI OTOMATIS & READ-ONLY) */}
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Nomor Induk Siswa (NIS)</Text>
          <View style={styles.inputWrapper}>
            <FontAwesome name="id-card" size={16} color="#64748b" style={styles.icon} />
            <TextInput
              style={[styles.input, { color: '#64748b' }]} // Beri warna teks agak redup karena dibekukan
              placeholder="Akan terisi otomatis"
              value={nis}
              editable={false} // 💡 Dikunci agar tidak bisa diedit manual
            />
          </View>
        </View>
  
        {/* Pilihan Tipe Transaksi */}
        <Text style={styles.label}>Jenis Transaksi</Text>
        <View style={styles.typeRow}>
          {!(hideOther === 'true' && tipe === 'tarik') && (
            <TouchableOpacity 
              style={[styles.typeButton, tipe === 'setor' && styles.activeSetor]} 
              onPress={() => setTipe('setor')}
            >
              <FontAwesome name="plus-circle" size={18} color={tipe === 'setor' ? '#fff' : '#16a34a'} />
              <Text style={[styles.typeText, tipe === 'setor' && styles.activeTypeText]}>Setor Tunai</Text>
            </TouchableOpacity>
          )}
  
          {!(hideOther === 'true' && tipe === 'setor') && (
            <TouchableOpacity 
              style={[styles.typeButton, tipe === 'tarik' && styles.activeTarik]} 
              onPress={() => setTipe('tarik')}
            >
              <FontAwesome name="minus-circle" size={18} color={tipe === 'tarik' ? '#fff' : '#dc2626'} />
              <Text style={[styles.typeText, tipe === 'tarik' && styles.activeTypeText]}>Tarik Tunai</Text>
            </TouchableOpacity>
          )}
        </View>
  
        {/* Input Nominal */}
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Nominal Transaksi (Rp)</Text>
          <View style={styles.inputWrapper}>
            <Text style={styles.rpText}>Rp</Text>
            <TextInput
              style={styles.input}
              placeholder="Contoh: 50.000"
              keyboardType="number-pad"
              value={formatInputNominal(nominal)} 
              onChangeText={(val) => {
                const angkaMurni = val.replace(/\D/g, '');
                setNominal(angkaMurni);
              }}
              editable={!loading}
            />
          </View>
        </View>
  
        {/* Tombol Simpan */}
        <TouchableOpacity style={styles.submitButton} onPress={handleSimpan} disabled={isSubmitting}>
          <Text style={styles.submitButtonText}>
            {isSubmitting ? (
                <View style={styles.loadingContainer}>
                  <ActivityIndicator size="small" color="#ffffff" />
                  <Text style={styles.submitButtonText}>Loading...</Text>
                </View>
              ) : (
                <View style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
                  <FontAwesome name="save" size={18} color="#fff" />
                  <Text style={styles.submitButtonText}>Simpan Transaksi</Text>
                </View>
              )}
          </Text>
        </TouchableOpacity>
      </View>
  
      <AppToast topOffset={5} />
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  contentContainer: { padding: 15 },
  formCard: { backgroundColor: '#fff', borderRadius: 16, padding: 20, borderWidth: 1, borderColor: '#e2e8f0', elevation: 2 },
  sectionTitle: { fontSize: 18, fontWeight: 'bold', color: '#1e293b', marginBottom: 20 },
  inputGroup: { marginBottom: 16 },
  label: { fontSize: 14, fontWeight: '600', color: '#475569', marginBottom: 6 },
  inputWrapper: { flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: '#cbd5e1', borderRadius: 10, paddingHorizontal: 12, backgroundColor: '#f8fafc', height: 46 },
  icon: { marginRight: 10 },
  rpText: { fontSize: 15, fontWeight: 'bold', color: '#64748b', marginRight: 8 },
  input: { flex: 1, fontSize: 15, color: '#1e293b' },
  typeRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 20 },
  typeButton: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', height: 46, borderWidth: 1, borderColor: '#cbd5e1', borderRadius: 10, marginHorizontal: 4, backgroundColor: '#fff' },
  activeSetor: { backgroundColor: '#16a34a', borderColor: '#16a34a' },
  activeTarik: { backgroundColor: '#dc2626', borderColor: '#dc2626' },
  typeText: { fontSize: 14, fontWeight: '600', color: '#475569', marginLeft: 8 },
  activeTypeText: { color: '#fff' },
  submitButton: { backgroundColor: '#0284c7', height: 48, borderRadius: 10, flexDirection: 'row', justifyContent: 'center', alignItems: 'center', marginTop: 10 },
  submitButtonText: { color: '#fff', fontSize: 16, fontWeight: 'bold', marginLeft: 8 },
  inputWrapperDropdown: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#cbd5e1',
    borderRadius: 10,
    paddingLeft: 12, // Hanya padding kiri untuk menata icon
    backgroundColor: '#f8fafc',
    height: 46,
  },
  iconDropdown: {
    marginRight: 4,
  },
  dropdown: {
    flex: 1,
    height: '100%',
    paddingRight: 12,
  },
  placeholderStyle: {
    fontSize: 15,
    color: '#94a3b8',
  },
  selectedTextStyle: {
    fontSize: 15,
    color: '#1e293b',
  },
  inputSearchStyle: {
    height: 40,
    fontSize: 15,
    borderRadius: 8,
    color: '#1e293b',
  },
  loadingContainer: {
    flexDirection: 'row',     
    alignItems: 'center',    
    justifyContent: 'center',  
    gap: 12,                    
  },
  dropdownContainer: {
    borderRadius: 8,         
    marginTop: 4,            
    elevation: 3,            
    shadowColor: '#000',     
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
  },
});