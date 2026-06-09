import { Tabs, router } from 'expo-router';
import React, { useState, useRef, useEffect } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { StyleSheet, Text, View, TextInput, TouchableOpacity, Alert, PanResponder, ActivityIndicator } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import * as SecureStore from 'expo-secure-store'
import {tab2ApiService} from '../../services/Tab2apiservice'
import { AppToast } from '@/components/ToastProvider'
import LoginView from '@/components/LoginView';
import RegisterView from '@/components/RegisterView';
// Fungsi global agar bisa dipanggil dari file index.tsx (Dashboard) untuk logout
export let triggerLogoutGlobal = () => {};

const TIMEOUT_IDLE = 30 * 60 * 1000;

export default function TabLayout() {
  const queryClient = useQueryClient();
  // State Keamanan Utama untuk Login Manual
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [authScreen, setAuthScreen] = useState<'login' | 'register'>('login');

  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);

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
  const handleAutoLogout = async () => {
    try {
      await SecureStore.deleteItemAsync('access_token');
      await SecureStore.deleteItemAsync('user_info');
      
      setIsLoggedIn(false);

      Alert.alert(
        "Sesi Berakhir",
        "Anda telah otomatis keluar karena tidak ada aktivitas selama beberapa saat demi keamanan data.",
        [{ text: "Mengerti" }]
      );
    } catch (error) {
      console.error("Gagal menghapus sesi saat auto logout:", error);
      setIsLoggedIn(false);
    }
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
      setIsSubmitting(true);
      const responseData = await tab2ApiService.postPublic(
        `${process.env.EXPO_PUBLIC_API_URL}/auth/login`,
        { username: username.trim(), password }, 
        'auth'
      )
    
      if (responseData && responseData.success) {
        
        const loginData = responseData.data; 
  
        if (loginData && loginData.access_token) {
          await SecureStore.setItemAsync('access_token', loginData.access_token)
          await SecureStore.setItemAsync('user_info', JSON.stringify(loginData.user || loginData.petugas || {}))
          
          await queryClient.invalidateQueries({ queryKey: ['userInfo'] });
          setIsLoggedIn(true)
        } else {
          Alert.alert('Login Gagal', 'Token tidak ditemukan dari server.')
        }
  
      } else {
        Alert.alert('Login Gagal', responseData.message || 'Username atau password salah.')
      }
  
    } catch (error: any) {
      console.error('Login error:', error)
      Alert.alert('Login Gagal', 'Terjadi kesalahan sistem atau jaringan.')
    } finally {
      setIsSubmitting(false);
    }
  }
  
  if (!isLoggedIn) {
    if (authScreen === 'register') {
      return (
        <RegisterView 
          onSwitchToLogin={() => setAuthScreen('login')} 
          styles={styles} 
        />
      );
    }

    return (
      <LoginView
        username={username}
        setUsername={setUsername}
        password={password}
        setPassword={setPassword}
        handleLogin={handleLogin}
        isSubmitting={isSubmitting}
        styles={styles}
        onSwitchToRegister={() => setAuthScreen('register')}
      />
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
        listeners={{
          tabPress: (e) => {
            e.preventDefault();
            router.replace({
              pathname: '/transaksi',
              params: { defaultTipe: '', hideOther: '' }
            });
          },
        }}
        options={{
          title: 'Transaksi',
          tabBarIcon: ({ color }) => <FontAwesome name="exchange" size={22} color={color} />,
          headerTitle: 'Input Transaksi',
          
        }}
      />

      {/* Tab 3: Data Siswa */}
      <Tabs.Screen
        name="riwayat"
        listeners={{
          tabPress: (e) => {
            e.preventDefault();
            router.replace({
              pathname: '/riwayat',
              params: { defaultTipe: '', hideOther: '' }
            });
          },
        }}
        options={{
          title: 'Riwayat',
          tabBarIcon: ({ color }) => <FontAwesome name="history" size={22} color={color} />,
          headerTitle: 'Riwayat Transaksi Siswa',
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
  loadingContainer: {
    flexDirection: 'row',     
    alignItems: 'center',    
    justifyContent: 'center',  
    gap: 12,                    
  },
});