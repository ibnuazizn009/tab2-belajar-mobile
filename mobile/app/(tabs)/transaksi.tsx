import React, { useState, useEffect } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import * as SecureStore from 'expo-secure-store'
import { useLocalSearchParams } from 'expo-router';
import { StyleSheet, Text, View, TextInput, TouchableOpacity, ScrollView, Alert, ActivityIndicator, RefreshControl } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import { tab2ApiService } from '../../services/Tab2apiservice'
import { AppToast } from '@/components/ToastProvider'
import { Dropdown } from 'react-native-element-dropdown';
import { tab2Toast } from '@/utils/tab2Toast';

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
  const [listSiswa, setListSiswa] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [refreshing, setRefreshing] = useState(false);

  const formatInputNominal = (value: string) => {
    const nomorMurni = value.replace(/\D/g, '');
    return nomorMurni.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  };

  const loadDataSiswa = async (idYangDipilih: string) => {
    try {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/siswa-per-kelas?kelasId=${idYangDipilih}`,
        'siswa'
      );
      // Sinkronisasi data dari API ke dropdown
      if (responseData && responseData.data) {
        setListSiswa(responseData.data || []);
      }
    } catch (error) {
      console.error('Load siswa error:', error);
    }
  };

  const postTransaksiData = async() => {
    if (!nis.trim()) {
      tab2Toast.error('Siswa Belum Dipilih', 'Pilih siswa terlebih dahulu.');
      return;
    }
    if (!tipe) {
      tab2Toast.error('Tipe Belum Dipilih', 'Pilih jenis transaksi.');
      return;
    }
    if (!nominal || Number(nominal) <= 0) {
      tab2Toast.error('Nominal Tidak Valid', 'Masukkan nominal yang benar.');
      return;
    }
  
    try {
      setIsSubmitting(true);
      setLoading(true);
  
      const payload = {
        nis: nis,
        tipe: tipe,
        nominal: Number(nominal),
        nama_siswa: nama
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
  
        tab2Toast.success(
          tipe === 'setor' ? 'Setoran Berhasil' : 'Penarikan Berhasil',
          `${nama} — Rp ${Number(nominal).toLocaleString('id-ID')}`
        );
      } else {
        tab2Toast.error('Transaksi Gagal', response?.message || 'Gagal menyimpan transaksi.');
      }
  
    } catch (error: any) {
      console.error('Submit transaksi error:', error);
      tab2Toast.error('Terjadi Kesalahan', error?.response?.data?.message || 'Gagal menghubungi server.');
    } finally {
      setIsSubmitting(false);
      setLoading(false);
    }
  }

  useEffect(() => {
    const initializeData = async () => {
      const raw = await SecureStore.getItemAsync('user_info');
      if (raw) {
        const user = JSON.parse(raw);
        await loadDataSiswa(user.kelas_id);
      }
    };
    initializeData();
  }, []);

  useEffect(() => {
    setTipe(defaultTipe || '');
  }, [defaultTipe]);

  const handleSimpan = () => postTransaksiData();

  return (
    <ScrollView 
      style={styles.container} 
      contentContainerStyle={styles.contentContainer}
    >
      <View style={styles.formCard}>
        <Text style={styles.sectionTitle}>Pencatatan Tabungan</Text>
  
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Pilih Nama Siswa</Text>
          <View style={styles.inputWrapperDropdown}>
            <FontAwesome name="user" size={16} color="#64748b" style={[styles.iconDropdown, { marginRight: 12 }]} />
            <Dropdown
              style={styles.dropdown}
              placeholderStyle={styles.placeholderStyle}
              selectedTextStyle={styles.selectedTextStyle}
              inputSearchStyle={styles.inputSearchStyle}
              data={listSiswa}
              search
              maxHeight={300}
              labelField="nama_siswa" // Sesuai field database/API
              valueField="nis" 
              placeholder="Cari atau pilih nama siswa..."
              searchPlaceholder="Ketik nama siswa..."
              value={nis}
              onChange={(item: any) => {
                setNis(item.nis);
                setNama(item.nama_siswa);
              }}
            />
          </View>
        </View>
  
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Nomor Induk Siswa (NIS)</Text>
          <View style={styles.inputWrapper}>
            <FontAwesome name="id-card" size={16} color="#64748b" style={styles.icon} />
            <TextInput
              style={[styles.input, { color: '#64748b' }]}
              value={nis}
              editable={false}
              placeholder="Otomatis..."
            />
          </View>
        </View>
  
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
  
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Nominal Transaksi (Rp)</Text>
          <View style={styles.inputWrapper}>
            <Text style={styles.rpText}>Rp</Text>
            <TextInput
              style={styles.input}
              placeholder="Contoh: 50.000"
              keyboardType="number-pad"
              value={formatInputNominal(nominal)} 
              onChangeText={(val) => setNominal(val.replace(/\D/g, ''))}
            />
          </View>
        </View>
  
        <TouchableOpacity style={styles.submitButton} onPress={handleSimpan} disabled={isSubmitting}>
          {isSubmitting ? (
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 8 }}>
              <ActivityIndicator color="#fff" size="small" />
              <Text style={styles.submitButtonText}>Menyimpan Transaksi...</Text>
            </View>
          ) : (
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
              <FontAwesome name="save" size={18} color="#fff" />
              <Text style={styles.submitButtonText}>Simpan Transaksi</Text>
            </View>
          )}
        </TouchableOpacity>
      </View>  
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
    paddingLeft: 12,
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
});