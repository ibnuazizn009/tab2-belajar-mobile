import { Tabs } from 'expo-router';
import React, { useState, useRef, useEffect } from 'react';
import { StyleSheet, Text, View, TextInput, TouchableOpacity, Alert, PanResponder } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import * as SecureStore from 'expo-secure-store'
import {tab2ApiService} from '../../services/Tab2apiservice'
// Fungsi global agar bisa dipanggil dari file index.tsx (Dashboard) untuk logout
export let triggerLogoutGlobal = () => {};

const TIMEOUT_IDLE = 30 * 60 * 1000;

export default function TabLayout() {
  // State Keamanan Utama untuk Login Manual
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');

  const timerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

  const resetIdleTimer = () => {
    // Hapus timer lama yang sedang berjalan
    if (timerRef.current) {
      clearTimeout(timerRef.current);
    }

    // Hanya jalankan timer otomatis jika user sudah berstatus login
    if (isLoggedIn) {
      timerRef.current = setTimeout(() => {
        handleAutoLogout();
      }, TIMEOUT_IDLE);
    }
  };

  // Fungsi eksekusi ketika waktu idle habis
  const handleAutoLogout = () => {
    setIsLoggedIn(false);
    setUsername('');
    setPassword('');
    Alert.alert(
      "Sesi Berakhir",
      "Anda telah otomatis keluar karena tidak ada aktivitas selama 30 menit demi keamanan data.",
      [{ text: "Mengerti" }]
    );
  };

  // Menghubungkan pemicu fungsi logout global ke state lokal layout
  triggerLogoutGlobal = () => {
    setIsLoggedIn(false);
    setUsername('');
    setPassword('');
  };

  useEffect(() => {
    if (isLoggedIn) {
      resetIdleTimer();
    } else {
      if (timerRef.current) clearTimeout(timerRef.current);
    }

    return () => {
      if (timerRef.current) clearTimeout(timerRef.current);
    };
  }, [isLoggedIn]);

  // Inisialisasi PanResponder untuk mendeteksi SEGALA jenis sentuhan di area aplikasi
  const panResponder = useRef(
    PanResponder.create({
      onStartShouldSetPanResponder: () => {
        resetIdleTimer(); // Sentuhan pertama terdeteksi -> reset timer
        return false;     // Tetap teruskan sentuhan ke komponen di bawahnya (tombol/input tetap berfungsi)
      },
      onMoveShouldSetPanResponder: () => {
        resetIdleTimer(); // Gerakan geser (scroll) terdeteksi -> reset timer
        return false;
      },
    })
  ).current;

  const handleLogin = async () => {
    if (!username.trim() || !password) {
      Alert.alert('Login Gagal', 'Username dan password wajib diisi.')
      return
    }
  
    try {
      const responseData = await tab2ApiService.postPublic(
        `${process.env.EXPO_PUBLIC_API_URL}/auth/login`,
        { username: username.trim(), password }
      )
  
      await SecureStore.setItemAsync('access_token', responseData.access_token)
      await SecureStore.setItemAsync('user_info', JSON.stringify(responseData.user))
      setIsLoggedIn(true)
    } catch (error: any) {
      const message = error?.data?.message || 'Username atau password salah.'
      Alert.alert('Login Gagal', message, [{ text: 'Coba Lagi' }])
      console.error('Login error:', error)
    }
  }
  
  if (!isLoggedIn) {
    return (
      <View style={styles.loginContainer}>
        <View style={styles.loginCard}>
          {/* Ikon Header Sistem */}
          <View style={styles.loginIconWrapper}>
            <FontAwesome name="bank" size={40} color="#0284c7" />
          </View>
          
          <Text style={styles.loginTitle}>E-Tabungan Siswa</Text>
          <Text style={styles.loginSubTitle}>Silakan masuk ke akun petugas Anda</Text>

          {/* Form Input Username */}
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

          {/* Form Input Password */}
          <View style={styles.inputGroup}>
            <Text style={styles.inputLabel}>Password</Text>
            <View style={styles.inputWrapper}>
              <FontAwesome name="lock" size={18} color="#94a3b8" style={styles.inputIcon} />
              <TextInput 
                style={styles.textInput}
                placeholder="Masukkan password"
                placeholderTextColor="#94a3b8"
                secureTextEntry={true}
                value={password}
                onChangeText={setPassword}
                autoCapitalize="none"
              />
            </View>
          </View>

          {/* Tombol Submit Login */}
          <TouchableOpacity style={styles.loginButton} onPress={handleLogin} activeOpacity={0.8}>
            <Text style={styles.loginButtonText}>Masuk Ke Sistem</Text>
            <FontAwesome name="sign-in" size={18} color="#fff" style={{ marginLeft: 8 }} />
          </TouchableOpacity >
        </View>
      </View>
    );
  }

  // ========================================================
  // KONDISI 2: JIKA BERHASIL LOGIN (Tampilkan 3 Tab Utama)
  // ========================================================
  return (
    <Tabs
      screenOptions={{
        tabBarActiveTintColor: '#0284c7',   // Warna biru cerah saat tab aktif
        tabBarInactiveTintColor: '#94a3b8', // Warna abu-abu saat tab tidak aktif
        tabBarStyle: {
          paddingBottom: 5,
          height: 60,
        },
        tabBarLabelStyle: {
          fontSize: 12,
          fontWeight: '500',
        },
        headerShown: true,
      }}>
      
      {/* Tab 1: Beranda */}
      <Tabs.Screen
        name="index"
        options={{
          title: 'Home',
          tabBarIcon: ({ color }) => <FontAwesome name="home" size={24} color={color} />,
          headerShown: false, // Disembunyikan karena index.tsx memiliki header custom sendiri
        }}
      />

      {/* Tab 2: Transaksi */}
      <Tabs.Screen
        name="transaksi"
        options={{
          title: 'Transaksi',
          tabBarIcon: ({ color }) => <FontAwesome name="exchange" size={22} color={color} />,
          headerTitle: 'Input Transaksi',
        }}
      />

      {/* Tab 3: Data Siswa */}
      <Tabs.Screen
        name="siswa"
        options={{
          title: 'Siswa',
          tabBarIcon: ({ color }) => <FontAwesome name="users" size={22} color={color} />,
          headerTitle: 'Buku Induk Siswa',
        }}
      />
    </Tabs>
  );
}

const styles = StyleSheet.create({
  loginContainer: { 
    flex: 1, 
    backgroundColor: '#f1f5f9', 
    justifyContent: 'center', 
    padding: 20 
  },
  loginCard: { 
    backgroundColor: '#ffffff', 
    borderRadius: 20, 
    padding: 25, 
    borderWidth: 1, 
    borderColor: '#e2e8f0', 
    elevation: 3 
  },
  loginIconWrapper: { 
    width: 70, 
    height: 70, 
    borderRadius: 35, 
    backgroundColor: '#e0f2fe', 
    justifyContent: 'center', 
    alignItems: 'center', 
    alignSelf: 'center', 
    marginBottom: 15 
  },
  loginTitle: { 
    fontSize: 22, 
    fontWeight: 'bold', 
    textAlign: 'center', 
    color: '#0f172a' 
  },
  loginSubTitle: { 
    fontSize: 14, 
    color: '#64748b', 
    textAlign: 'center', 
    marginTop: 4, 
    marginBottom: 25 
  },
  inputGroup: { 
    marginBottom: 16 
  },
  inputLabel: { 
    fontSize: 14, 
    fontWeight: '600', 
    color: '#334155', 
    marginBottom: 6 
  },
  inputWrapper: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    borderWidth: 1, 
    borderColor: '#cbd5e1', 
    borderRadius: 10, 
    paddingHorizontal: 12, 
    backgroundColor: '#f8fafc', 
    height: 46 
  },
  inputIcon: { 
    marginRight: 10 
  },
  textInput: { 
    flex: 1, 
    fontSize: 15, 
    color: '#1e293b' 
  },
  loginButton: { 
    backgroundColor: '#0284c7', 
    height: 48, 
    borderRadius: 10, 
    flexDirection: 'row', 
    justifyContent: 'center', 
    alignItems: 'center', 
    marginTop: 15 
  },
  loginButtonText: { 
    color: '#ffffff', 
    fontSize: 16, 
    fontWeight: 'bold' 
  },
});