import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, ActivityIndicator, StyleSheet, Alert, ViewStyle, TextStyle } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import { Dropdown } from 'react-native-element-dropdown';
import { AppToast } from '@/components/ToastProvider'
import {tab2ApiService} from '@/services/Tab2apiservice'

interface RegisterViewProps {
  onSwitchToLogin: () => void;
  styles: {
    loginCard: ViewStyle; 
    inputGroup: ViewStyle;
    inputWrapper: ViewStyle;
    inputIcon: ViewStyle;
    textInput: ViewStyle;
    loginButton: ViewStyle;
    loadingContainer: ViewStyle;
    loginButtonText: any;     
    loginTitle: any;
    loginSubTitle: any;
  }; 
}

interface DropdownItem {
    label: string;
    value: string;
  }
  
export default function RegisterView({ onSwitchToLogin, styles }: RegisterViewProps) {
    
  const [currentStep, setCurrentStep] = useState(1);
  const [namaPetugas, setNamaPetugas] = useState('');
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  
  const [isLoadingKelas, setIsLoadingKelas] = useState(false);
  const [daftarKelas, setDaftarKelas] = useState<DropdownItem[]>([]);
  const [kelasIdSelected, setKelasIdSelected] = useState<string | null>(null);

  const [daftarKota, setDaftarKota] = useState<DropdownItem[]>([]);
  const [kotaIdSelected, setKotaIdSelected] = useState<string | null>(null);
  const [isLoadingKota, setIsLoadingKota] = useState(false);

  const [daftarSekolah, setDaftarSekolah] = useState<DropdownItem[]>([]);
  const [sekolahIdSelected, setSekolahIdSelected] = useState<string | null>(null);
  const [isLoadingSekolah, setIsLoadingSekolah] = useState(false);

  const loadDataKelas = async () => {
    setIsLoadingKelas(true);
    try {
      const responseData = await tab2ApiService.getNonMessageNoAuth(
        `${process.env.EXPO_PUBLIC_API_URL}/master/kelas`,
        'master'
      );
  
      if (responseData && responseData.data) {
        
        const formattedKelas = responseData.data.map((kelas: any) => ({
          label: `Kelas ${kelas.nama_kelas}`,
          value: kelas.id.toString(),
        }));
  
        setDaftarKelas(formattedKelas);
      }
    } catch (error: any) {
      const message = error?.data?.message || 'Gagal memuat data kelas';
      console.error('Load kelas error:', message);
    } finally {
      setIsLoadingKelas(false);
    }
  };

  const loadDataKota = async () => {
    setIsLoadingKota(true);
    try {
      const responseData = await tab2ApiService.getNonMessageNoAuth(`${process.env.EXPO_PUBLIC_API_URL}/master/kota`, 'master');
      if (responseData && responseData.data) {
        const formatted = responseData.data.map((kota: any) => ({
          label: kota.nama_kota,
          value: kota.id.toString(),
        }));
        setDaftarKota(formatted);
      }
    } catch (error) {
      console.error('Load kota error:', error);
    } finally {
      setIsLoadingKota(false);
    }
  };

  const loadDataSekolah = async (kotaId: string) => {
    setIsLoadingSekolah(true);
    try {
      // Mengirimkan parameter query string ?kota_id=X
      const responseData = await tab2ApiService.getNonMessageNoAuth(
        `${process.env.EXPO_PUBLIC_API_URL}/master/sekolah-by-kota?kota_id=${kotaId}`, 
        'master'
      );
      if (responseData && responseData.data) {
        const formatted = responseData.data.map((sekolah: any) => ({
          label: sekolah.nama_sekolah,
          value: sekolah.id.toString(),
        }));
        setDaftarSekolah(formatted);
      }
    } catch (error) {
      console.error('Load sekolah error:', error);
    } finally {
      setIsLoadingSekolah(false);
    }
  };

  const handleNextStep = () => {
    if (!kotaIdSelected || !sekolahIdSelected || !kelasIdSelected) {
      Alert.alert('Peringatan', 'Silakan lengkapi data wilayah, sekolah, dan kelas terlebih dahulu!');
      return;
    }
    setCurrentStep(2);
  };

  const handleRegister = async () => {
    if (!namaPetugas || !username || !password) {
      Alert.alert('Peringatan', 'Silakan lengkapi data nama, username, dan password!');
      return;
    }
  
    if (!sekolahIdSelected || !kelasIdSelected) {
      Alert.alert('Peringatan', 'Data sekolah atau kelas tidak valid, silakan ulangi langkah 1.');
      setCurrentStep(1);
      return;
    }
  
    setIsSubmitting(true);
  
    try {
      const payload = {
        nama_petugas: namaPetugas,
        username: username.trim(),
        password: password,
        sekolah_id: parseInt(sekolahIdSelected), 
        kelas_id: parseInt(kelasIdSelected),   
      };
  
      const responseData = await tab2ApiService.postPublic(
        `${process.env.EXPO_PUBLIC_API_URL}/auth/register`,
        payload,
        'auth'
      );
      
      console.log(responseData);
  
      if (responseData && responseData.success) {
        Alert.alert(
          'Sukses', 
          'Registrasi petugas berhasil! Silakan masuk.',
          [
            { 
              text: 'OK', 
              onPress: () => {
                setNamaPetugas('');
                setUsername('');
                setPassword('');
                setCurrentStep(1);
                onSwitchToLogin(); 
              } 
            }
          ]
        );
      } else {
        Alert.alert(
          'Gagal Pendaftaran', 
          responseData?.message || 'Gagal mendaftarkan akun. Silakan coba lagi.'
        );
      }
  
    } catch (error: any) {
      console.error('Register error:', error);
      const errorMessage = error?.response?.data?.message || error?.message || 'Terjadi kesalahan pada server';
      Alert.alert('Pendaftaran Gagal', errorMessage);
    } finally {
      setIsSubmitting(false);
    }
  };

  useEffect(() => {
    loadDataKelas();
    loadDataKota();
  }, []);

  useEffect(() => {
    if (kotaIdSelected) {
      loadDataSekolah(kotaIdSelected);
    } else {
      setDaftarSekolah([]);
      setSekolahIdSelected(null);
    }
  }, [kotaIdSelected]);

  return (
    <View style={localStyles.mainContainer}>
      <View style={[styles.loginCard as ViewStyle, localStyles.fixedCard]}>
        
        <View style={localStyles.stepBadgeWrapper}>
          <Text style={localStyles.stepIndicator}>Langkah {currentStep}/2</Text>
        </View>

        <View style={localStyles.centerHeaderContainer}>
            <View style={localStyles.smallIconWrapper}>
                <FontAwesome name="bank" size={26} color="#0284c7" />
            </View>
            <Text style={styles.loginTitle}>E-Tabungan Siswa</Text>
            <Text style={styles.loginSubTitle}>Buat akun petugas baru Anda</Text>
        </View>

        {currentStep === 1 && (
          <View style={localStyles.stepContainer}>
            <View style={localStyles.inputGroup}>
              <Text style={localStyles.label}>Kota / Kabupaten</Text>
              <View style={styles.inputWrapper as ViewStyle}>
                <FontAwesome name="map-marker" size={18} color="#94a3b8" style={[styles.inputIcon as TextStyle, { marginLeft: 4 }]} />
                <Dropdown
                  style={dropdownStyles.dropdown}
                  placeholderStyle={dropdownStyles.placeholderStyle}
                  selectedTextStyle={dropdownStyles.selectedTextStyle}
                  inputSearchStyle={dropdownStyles.inputSearchStyle}
                  data={daftarKota}
                  search
                  maxHeight={200}
                  labelField="label"
                  valueField="value"
                  placeholder={isLoadingKota ? 'Memuat kota...' : 'Pilih Kota'}
                  searchPlaceholder="Cari kota..."
                  value={kotaIdSelected}
                  onChange={item => {
                    setKotaIdSelected(item.value);
                    setSekolahIdSelected(null);
                  }}
                  renderRightIcon={() => isLoadingKota ? <ActivityIndicator size="small" color="#0284c7" /> : <FontAwesome name="chevron-down" size={14} color="#94a3b8" />}
                />
              </View>
            </View>

            {/* Input: Nama Sekolah */}
            <View style={localStyles.inputGroup}>
              <Text style={localStyles.label}>Nama Sekolah</Text>
              <View style={styles.inputWrapper as ViewStyle}>
                <FontAwesome name="building" size={16} color="#94a3b8" style={styles.inputIcon as TextStyle} />
                <Dropdown
                  style={dropdownStyles.dropdown}
                  placeholderStyle={dropdownStyles.placeholderStyle}
                  selectedTextStyle={dropdownStyles.selectedTextStyle}
                  inputSearchStyle={dropdownStyles.inputSearchStyle}
                  data={daftarSekolah}
                  search
                  maxHeight={200}
                  labelField="label"
                  valueField="value"
                  placeholder={!kotaIdSelected ? 'Pilih kota dulu' : isLoadingSekolah ? 'Memuat sekolah...' : 'Pilih Sekolah'}
                  searchPlaceholder="Cari sekolah..."
                  value={sekolahIdSelected}
                  disable={!kotaIdSelected}
                  onChange={item => setSekolahIdSelected(item.value)}
                  renderRightIcon={() => isLoadingSekolah ? <ActivityIndicator size="small" color="#0284c7" /> : <FontAwesome name="chevron-down" size={14} color="#94a3b8" />}
                />
              </View>
            </View>

            {/* Input: Dropdown Kelas */}
            <View style={localStyles.inputGroup}>
              <Text style={localStyles.label}>Tugas di Kelas</Text>
              <View style={styles.inputWrapper as ViewStyle}>
                <FontAwesome name="graduation-cap" size={16} color="#94a3b8" style={styles.inputIcon as TextStyle} />
                <Dropdown
                  style={dropdownStyles.dropdown}
                  placeholderStyle={dropdownStyles.placeholderStyle}
                  selectedTextStyle={dropdownStyles.selectedTextStyle}
                  inputSearchStyle={dropdownStyles.inputSearchStyle}
                  data={daftarKelas}
                  search
                  maxHeight={200}
                  labelField="label"
                  valueField="value"
                  placeholder={isLoadingKelas ? 'Memuat kelas...' : 'Pilih kelas tugas'}
                  searchPlaceholder="Cari kelas..."
                  value={kelasIdSelected}
                  onChange={item => setKelasIdSelected(item.value)}
                  renderRightIcon={() => isLoadingKelas ? <ActivityIndicator size="small" color="#0284c7" /> : <FontAwesome name="chevron-down" size={14} color="#94a3b8" />}
                />
              </View>
            </View>

            {/* Tombol Selanjutnya */}
            <TouchableOpacity style={[styles.loginButton as ViewStyle, localStyles.actionButton]} onPress={handleNextStep} activeOpacity={0.8}>
              <Text style={styles.loginButtonText}>Selanjutnya</Text>
              <FontAwesome name="arrow-right" size={14} color="#fff" style={{ marginLeft: 8 }} />
            </TouchableOpacity>
          </View>
        )}

        {currentStep === 2 && (
          <View style={localStyles.stepContainer}>
            {/* Input: Nama Lengkap */}
            <View style={localStyles.inputGroup}>
              <Text style={localStyles.label}>Nama Lengkap</Text>
              <View style={styles.inputWrapper as ViewStyle}>
                <FontAwesome name="vcard" size={16} color="#94a3b8" style={styles.inputIcon as TextStyle} />
                <TextInput style={styles.textInput as TextStyle} placeholder="Masukkan nama lengkap" placeholderTextColor="#94a3b8" value={namaPetugas} onChangeText={setNamaPetugas} />
              </View>
            </View>

            <View style={localStyles.inputGroup}>
              <Text style={localStyles.label}>Username</Text>
              <View style={styles.inputWrapper as ViewStyle}>
                <FontAwesome name="user" size={18} color="#94a3b8" style={styles.inputIcon as TextStyle} />
                <TextInput style={styles.textInput as TextStyle} placeholder="Buat username baru" placeholderTextColor="#94a3b8" value={username} onChangeText={setUsername} autoCapitalize="none" />
              </View>
            </View>

            <View style={localStyles.inputGroup}>
              <Text style={localStyles.label}>Password</Text>
              <View style={styles.inputWrapper as ViewStyle}>
                <FontAwesome name="lock" size={18} color="#94a3b8" style={styles.inputIcon as TextStyle} />
                <TextInput style={styles.textInput as TextStyle} placeholder="Buat password" placeholderTextColor="#94a3b8" secureTextEntry={true} value={password} onChangeText={setPassword} autoCapitalize="none" />
              </View>
            </View>

            <View style={localStyles.buttonRow}>
              <TouchableOpacity style={localStyles.backButton} onPress={() => setCurrentStep(1)} activeOpacity={0.7}>
                <FontAwesome name="arrow-left" size={14} color="#64748b" style={{ marginRight: 6 }} />
                <Text style={localStyles.backButtonText}>Kembali</Text>
              </TouchableOpacity>

              <TouchableOpacity style={[styles.loginButton as ViewStyle, localStyles.actionButton, { flex: 2, marginTop: 0 }]} onPress={handleRegister} activeOpacity={0.8} disabled={isSubmitting}>
                {isSubmitting ? (
                  <ActivityIndicator size="small" color="#ffffff" />
                ) : (
                  <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                    <Text style={styles.loginButtonText}>Daftar Akun</Text>
                    <FontAwesome name="user-plus" size={16} color="#fff" style={{ marginLeft: 8 }} />
                  </View>
                )}
              </TouchableOpacity>
            </View>
          </View>
        )}

        {/* Navigasi Balik ke Login */}
        <View style={localStyles.toggleContainer}>
          <Text style={localStyles.toggleText}>Sudah punya akun? </Text>
          <TouchableOpacity onPress={onSwitchToLogin}>
            <Text style={localStyles.toggleLink}>Masuk</Text>
          </TouchableOpacity>
        </View>

      </View>
      <AppToast topOffset={60} />
    </View>
  );
}

const dropdownStyles = StyleSheet.create({
    dropdown: {
      flex: 1,
      height: 40,
      backgroundColor: 'transparent',
    },
    placeholderStyle: {
      fontSize: 14,
      color: '#94a3b8',
    },
    selectedTextStyle: {
      fontSize: 14,
      color: '#0f172a', // Warna teks saat item dipilih
    },
    inputSearchStyle: {
      height: 40,
      fontSize: 14,
      borderRadius: 8,
    },
});

const localStyles = StyleSheet.create({
    mainContainer: {
      flex: 1,
      backgroundColor: '#f8fafc', 
      justifyContent: 'center',
      alignItems: 'center',
      paddingHorizontal: 16,
    },
    fixedCard: {
      width: '100%',     
      maxWidth: 420, 
      paddingTop: 24,
      paddingBottom: 18,       
      paddingHorizontal: 20,
      position: 'relative',
    },
    stepBadgeWrapper: {
      position: 'absolute',
      top: 14,
      right: 14,
      zIndex: 10,
    },
    centerHeaderContainer: {
      alignItems: 'center',
      justifyContent: 'center',
      marginBottom: 14,
      width: '100%',
    },
    smallIconWrapper: {
      width: 52,
      height: 52,
      borderRadius: 26,
      marginBottom: 8,
      alignItems: 'center',
      justifyContent: 'center',
    },
    stepIndicator: { 
      fontSize: 12, 
      fontWeight: '600', 
      color: '#0284c7', 
      backgroundColor: '#e0f2fe', 
      paddingHorizontal: 8, 
      paddingVertical: 3, 
      borderRadius: 10 
    },
    stepContainer: { width: '100%' },
    inputGroup: { marginBottom: 10, width: '100%' },
    label: { fontSize: 13, fontWeight: '600', color: '#475569', marginBottom: 5 },
    actionButton: { height: 44, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', marginTop: 8 },
    buttonRow: { flexDirection: 'row', alignItems: 'center', width: '100%', marginTop: 8 },
    backButton: { flex: 1, height: 44, backgroundColor: '#f1f5f9', borderRadius: 8, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', marginRight: 10 },
    backButtonText: { color: '#64748b', fontWeight: '600', fontSize: 14 },
    toggleContainer: { flexDirection: 'row', justifyContent: 'center', alignItems: 'center', marginTop: 14 },
    toggleText: { fontSize: 13, color: '#64748b' },
    toggleLink: { fontSize: 13, color: '#0284c7', fontWeight: '600' },
  });