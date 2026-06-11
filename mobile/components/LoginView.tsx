import React, { useState, useEffect, useRef } from 'react';
import { View, Text, TextInput, TouchableOpacity, ActivityIndicator, StyleSheet, Linking, Modal, ScrollView, Animated, Easing, Dimensions } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import { AppToast } from '@/components/ToastProvider';

const { height: SCREEN_HEIGHT } = Dimensions.get('window');

interface LoginViewProps {
  username: string;
  setUsername: (text: string) => void;
  password: string;
  setPassword: (text: string) => void;
  handleLogin: () => void;
  isSubmitting: boolean;
  styles: any;
}

export default function LoginView({
  username,
  setUsername,
  password,
  setPassword,
  handleLogin,
  isSubmitting,
  styles,
}: LoginViewProps) {
  const [modalType, setModalType] = useState<'none' | 'panduan' | 'syarat'>('none');
  const [secureText, setSecureText] = useState(true); // 👈 State untuk memanipulasi hide/show password
  
  // Nilai animasi untuk posisi Y dan transparansi overlay
  const slideAnim = useRef(new Animated.Value(SCREEN_HEIGHT)).current;
  const fadeAnim = useRef(new Animated.Value(0)).current;

  // Efek trigger ketika modal dibuka atau ditutup
  useEffect(() => {
    if (modalType !== 'none') {
      // Jalankan animasi Buka (Slide Up & Fade In) secara bersamaan
      Animated.parallel([
        Animated.timing(slideAnim, {
          toValue: 0,
          duration: 450, // 👈 Durasi meluncur (lebih lambat & anggun)
          easing: Easing.out(Easing.cubic), // 👈 Efek melambat di akhir sliding
          useNativeDriver: true,
        }),
        Animated.timing(fadeAnim, {
          toValue: 1,
          duration: 350,
          useNativeDriver: true,
        })
      ]).start();
    }
  }, [modalType]);

  // Fungsi menutup modal secara halus sebelum mengubah state modalType
  const closeModalSmoothly = () => {
    Animated.parallel([
      Animated.timing(slideAnim, {
        toValue: SCREEN_HEIGHT,
        duration: 400, // Durasi menutup turun ke bawah
        easing: Easing.in(Easing.cubic),
        useNativeDriver: true,
      }),
      Animated.timing(fadeAnim, {
        toValue: 0,
        duration: 300,
        useNativeDriver: true,
      })
    ]).start(() => {
      setModalType('none'); // Ubah state setelah animasi tutup selesai berjalan
    });
  };

  const openWebsiteDaftar = () => {
    Linking.openURL('https://e-tabungan-anda.com/register').catch((err) => 
      console.error("Gagal membuka URL", err)
    );
  };

  return (
    <View style={styles.loginContainer}>
      <View style={styles.loginCard}>
        <View style={styles.loginIconWrapper}>
          <FontAwesome name="bank" size={40} color="#0284c7" />
        </View>
        
        <Text style={styles.loginTitle}>E-Tabungan Siswa</Text>
        <Text style={styles.loginSubTitle}>Silakan masuk ke akun petugas Anda</Text>

        {/* Input Form Username */}
        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Username</Text>
          <View style={styles.inputWrapper}>
            <FontAwesome name="user" size={18} color="#94a3b8" style={styles.inputIcon} />
            <TextInput 
              style={styles.textInput}
              placeholder="Masukkan username"
              placeholderTextColor="#94a3b8"
              value={username}
              onChangeText={setUsername}
              autoCapitalize="none"
            />
          </View>
        </View>

        {/* Input Form Password dengan Tombol Mata */}
        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Password</Text>
          <View style={styles.inputWrapper}>
            <FontAwesome name="lock" size={18} color="#94a3b8" style={styles.inputIcon} />
            <TextInput 
              style={[styles.textInput, { flex: 1 }]} // Pastikan textInput mengambil sisa ruang agar tombol mata pas di kanan
              placeholder="Masukkan password"
              placeholderTextColor="#94a3b8"
              secureTextEntry={secureText} // 👈 Dinamis mengikuti state secureText
              value={password}
              onChangeText={setPassword}
              autoCapitalize="none"
            />
            {/* 🎯 Tombol Mata Toggle Hide/Show */}
            <TouchableOpacity 
              style={localStyles.eyeIconWrapper} 
              onPress={() => setSecureText(!secureText)}
              activeOpacity={0.6}
            >
              <FontAwesome 
                name={secureText ? "eye-slash" : "eye"} 
                size={18} 
                color="#94a3b8" 
              />
            </TouchableOpacity>
          </View>
        </View>

        <TouchableOpacity 
          style={styles.loginButton} 
          onPress={handleLogin} 
          activeOpacity={0.8}
          disabled={isSubmitting}
        >
          <View style={{ flexDirection: 'row', alignItems: 'center' }}>
            {isSubmitting ? (
              <View style={styles.loadingContainer}>
                <ActivityIndicator size="small" color="#ffffff" />
                <Text style={styles.loginButtonText}>Loading...</Text>
              </View>
            ) : (
              <Text style={styles.loginButtonText}>Masuk Ke Sistem</Text>
            )}
            {!isSubmitting && <FontAwesome name="sign-in" size={18} color="#fff" style={{ marginLeft: 8 }} />}
          </View>
        </TouchableOpacity>

        <View style={localStyles.infoFooter}>
          <TouchableOpacity onPress={() => setModalType('panduan')}>
            <Text style={localStyles.infoLink}>Cara Penggunaan</Text>
          </TouchableOpacity>
          <Text style={localStyles.divider}>•</Text>
          <TouchableOpacity onPress={() => setModalType('syarat')}>
            <Text style={localStyles.infoLink}>Syarat & Ketentuan</Text>
          </TouchableOpacity>
        </View>
      </View>

      {/* ================= MODAL DENGAN CUSTOM TIMING ANIMATION ================= */}
      <Modal
        visible={modalType !== 'none'}
        animationType="none" 
        transparent={true}
        statusBarTranslucent={true}
        onRequestClose={closeModalSmoothly}
      >
        <Animated.View style={[localStyles.modalOverlay, { opacity: fadeAnim }]}>
          <Animated.View 
            style={[
              localStyles.modalContainer, 
              { transform: [{ translateY: slideAnim }] }
            ]}
          >
            <View style={localStyles.modalHeader}>
              <Text style={localStyles.modalTitle}>
                {modalType === 'panduan' ? '📖 Panduan Penggunaan' : '⚖️ Syarat & Ketentuan'}
              </Text>
              <TouchableOpacity onPress={closeModalSmoothly}>
                <FontAwesome name="close" size={20} color="#64748b" />
              </TouchableOpacity>
            </View>

            <ScrollView style={localStyles.modalContent}>
              {modalType === 'panduan' ? (
                <View>
                  <Text style={localStyles.stepTitle}>Bagi Pihak Sekolah (Admin/Pembeli):</Text>
                  <Text style={localStyles.stepText}>1. Sekolah wajib didaftarkan terlebih dahulu oleh Kepala Sekolah/Bendahara melalui website resmi kami.</Text>
                  
                  <TouchableOpacity style={localStyles.webButton} onPress={openWebsiteDaftar}>
                    <Text style={localStyles.webButtonText}>Buka Website Pendaftaran Admin</Text>
                    <FontAwesome name="external-link" size={14} color="#fff" style={{ marginLeft: 6 }} />
                  </TouchableOpacity>

                  <Text style={localStyles.stepText}>2. Setelah mendaftar di web, Admin Sekolah masuk ke Dashboard Web untuk mengatur Kelas dan membuatkan akun Username & Password untuk para Guru (Wali Kelas).</Text>

                  <Text style={[localStyles.stepTitle, { marginTop: 15 }]}>Bagi Petugas/Guru Lapangan:</Text>
                  <Text style={localStyles.stepText}>1. Anda tidak perlu melakukan pendaftaran di aplikasi ini.</Text>
                  <Text style={localStyles.stepText}>2. Silakan hubungi Admin/Bendahara Sekolah Anda untuk mendapatkan akun login (Username & Password).</Text>
                  <Text style={localStyles.stepText}>3. Gunakan akun tersebut untuk masuk dan mulai menginput data tabungan siswa langsung dari ruang kelas.</Text>
                </View>
              ) : (
                <View>
                  <Text style={localStyles.skText}>1. Aplikasi E-Tabungan ini bertindak sebagai software pencatatan administrasi keuangan tabungan sekolah secara digital.</Text>
                  <Text style={localStyles.skText}>2. Keamanan uang fisik dan kesesuaian kas di dunia nyata merupakan tanggung jawab penuh pihak managemen internal sekolah masing-masing.</Text>
                  <Text style={localStyles.skText}>3. Akun Guru dikontrol sepenuhnya oleh Admin Sekolah. Penyalahgunaan akun petugas di luar tanggung jawab pengembang aplikasi.</Text>
                  <Text style={localStyles.skText}>4. Layanan ini menggunakan sistem berlangganan (Premium SaaS). Jika masa aktif sekolah berakhir, akses ke aplikasi HP akan dibekukan sementara hingga perpanjangan dilakukan di web admin.</Text>
                </View>
              )}
            </ScrollView>

            <TouchableOpacity style={localStyles.closeButton} onPress={closeModalSmoothly}>
              <Text style={localStyles.closeButtonText}>Saya Mengerti</Text>
            </TouchableOpacity>
          </Animated.View>
        </Animated.View>
      </Modal>

      <AppToast topOffset={100} />
    </View>
  );
}

const localStyles = StyleSheet.create({
  infoFooter: { flexDirection: 'row', justifyContent: 'center', alignItems: 'center', marginTop: 25 },
  infoLink: { fontSize: 13, color: '#0284c7', fontWeight: '500' },
  divider: { marginHorizontal: 10, color: '#cbd5e1' },
  
  // Styling tambahan khusus untuk menyeimbangkan posisi mata di sisi kanan wrapper input
  eyeIconWrapper: {
    paddingHorizontal: 10,
    justifyContent: 'center',
    alignItems: 'center',
    height: '100%',
  },

  modalOverlay: { 
    flex: 1, 
    backgroundColor: 'rgba(15, 23, 42, 0.4)', 
    justifyContent: 'flex-end' 
  },
  modalContainer: { 
    backgroundColor: '#fff', 
    borderTopLeftRadius: 20, 
    borderTopRightRadius: 20, 
    padding: 24, 
    maxHeight: '78%',
    elevation: 10,
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.08,
    shadowRadius: 10,
  },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 15, paddingBottom: 10, borderBottomWidth: 1, borderColor: '#f1f5f9' },
  modalTitle: { fontSize: 16, fontWeight: '700', color: '#1e293b' },
  modalContent: { marginBottom: 20 },
  stepTitle: { fontSize: 14, fontWeight: '700', color: '#0f172a', marginBottom: 5 },
  stepText: { fontSize: 13, color: '#475569', lineHeight: 20, marginBottom: 8, paddingLeft: 5 },
  skText: { fontSize: 13, color: '#475569', lineHeight: 22, marginBottom: 10, textAlign: 'justify' },
  webButton: { backgroundColor: '#0284c7', flexDirection: 'row', paddingVertical: 8, paddingHorizontal: 12, borderRadius: 6, alignSelf: 'flex-start', marginVertical: 8, marginLeft: 5, alignItems: 'center' },
  webButtonText: { color: '#fff', fontSize: 12, fontWeight: '600' },
  closeButton: { backgroundColor: '#f1f5f9', paddingVertical: 12, borderRadius: 8, alignItems: 'center' },
  closeButtonText: { color: '#334155', fontSize: 14, fontWeight: '600' }
});