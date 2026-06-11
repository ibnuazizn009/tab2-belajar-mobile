import { Tabs, router } from 'expo-router';
import React, { useState, useRef, useEffect } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { StyleSheet, Text, View, TextInput, TouchableOpacity, Alert, PanResponder, ActivityIndicator } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import * as SecureStore from 'expo-secure-store'
import { tab2ApiService } from '../../services/Tab2apiservice'
import LoginView from '@/components/LoginView';
import { tab2Toast } from '@/utils/tab2Toast';

// Fungsi global agar bisa dipanggil dari file index.tsx (Dashboard) untuk logout
export let triggerLogoutGlobal = () => {};

const TIMEOUT_IDLE = 30 * 60 * 1000; // 30 Menit Otomatis Logout

export default function TabLayout() {
  const queryClient = useQueryClient();
  
  // State Keamanan Utama
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);

  const timerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

  useEffect(() => {
    const checkExistingSession = async () => {
      try {
        const token = await SecureStore.getItemAsync('access_token');
        if (token) {
          setIsLoggedIn(true);
        }
      } catch (error) {
        console.error("Gagal membaca session:", error);
      }
    };
    checkExistingSession();
  }, []); 

  const resetIdleTimer = () => {
    if (timerRef.current) {
      clearTimeout(timerRef.current);
    }

    if (isLoggedIn) {
      timerRef.current = setTimeout(() => {
        handleAutoLogout();
      }, TIMEOUT_IDLE);
    }
  };

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

  triggerLogoutGlobal = () => {
    tab2Toast.info(
      'Sampai Jumpa!',
      'Anda berhasil keluar dari aplikasi.',
      {
        duration: 2000,
        onHide: () => {
          SecureStore.deleteItemAsync('access_token');
          SecureStore.deleteItemAsync('user_info');
          queryClient.clear();
          setIsLoggedIn(false);
          setUsername('');
          setPassword('');
        }
      }
    );
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

  const panResponder = useRef(
    PanResponder.create({
      onStartShouldSetPanResponder: () => {
        resetIdleTimer();
        return false; // Tetap lewatkan event touch ke komponen child (tombol/input)
      },
      onMoveShouldSetPanResponder: () => {
        resetIdleTimer();
        return false;
      },
    })
  ).current;

  const handleLogin = async () => 
  {
    if (!username.trim() || !password) {
      tab2Toast.error('Login Gagal', 'Username dan password wajib diisi.');
      return;
    }
  
    const payload = {
      username: username.trim(),
      password: password
    };
  
    try {
      setIsSubmitting(true);
      
      const responseData = await tab2ApiService.postPublic(
        `${process.env.EXPO_PUBLIC_API_URL}/auth/login`,
        payload, 
        'auth'
      );
          
      if (responseData && responseData.success && responseData.data) {
        
        const apiData = responseData.data;
  
        await Promise.all([
          SecureStore.setItemAsync('access_token', apiData.access_token),
          SecureStore.setItemAsync('user_info', JSON.stringify(apiData.user || {}))
        ]);
        
        queryClient.setQueryData(['userInfo'], apiData.user);
        
        queryClient.removeQueries({ queryKey: ['namaKelas'] });
        queryClient.removeQueries({ queryKey: ['dataSiswa'] });
        queryClient.removeQueries({ queryKey: ['totalTabunganSiswa'] });
        queryClient.removeQueries({ queryKey: ['transaksiHariIni'] });
  
        tab2Toast.loginSuccess(
          apiData.user?.nama_lengkap || 'Pengguna',
          () => setIsLoggedIn(true)  // masuk setelah toast hilang
        );
        
      } else if (responseData && responseData.status === 'frozen') {
        tab2Toast.warning(
          'Akses Dibekukan',
          responseData.message || 'Masa aktif layanan sekolah berakhir.'
        );
      } else {
        tab2Toast.error(
          'Login Gagal',
          responseData.message || 'Username atau password salah.'
        );
      }
    
    } catch (error: any) {
      console.error('Login error:', error);
      const errorMsg = error.response?.data?.message || 'Terjadi kesalahan sistem atau jaringan.';
      tab2Toast.error('Login Gagal', errorMsg);
    } finally {
      setIsSubmitting(false);
    }
  };
  
  
  if (!isLoggedIn) {
    return (
      <LoginView
        username={username}
        setUsername={setUsername}
        password={password}
        setPassword={setPassword}
        handleLogin={handleLogin}
        isSubmitting={isSubmitting}
        styles={styles}
      />
    );
  }

  return (
    // Memasang panResponder di sini agar mendeteksi aktivitas di semua Tab Menu
    <View style={{ flex: 1 }} {...panResponder.panHandlers}>
      <Tabs
        screenOptions={{
          tabBarActiveTintColor: '#0284c7',   
          tabBarInactiveTintColor: '#94a3b8', 
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
            headerShown: false,
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

        {/* Tab 3: Riwayat */}
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
    </View>
  );
}

const styles = StyleSheet.create({
  loginContainer: { flex: 1, backgroundColor: '#f1f5f9', justifyContent: 'center', padding: 20 },
  loginCard: { backgroundColor: '#ffffff', borderRadius: 20, padding: 25, borderWidth: 1, borderColor: '#e2e8f0', elevation: 3 },
  loginIconWrapper: { width: 70, height: 70, borderRadius: 35, backgroundColor: '#e0f2fe', justifyContent: 'center', alignItems: 'center', alignSelf: 'center', marginBottom: 15 },
  loginTitle: { fontSize: 22, fontWeight: 'bold', textAlign: 'center', color: '#0f172a' },
  loginSubTitle: { fontSize: 14, color: '#64748b', textAlign: 'center', marginTop: 4, marginBottom: 25 },
  inputGroup: { marginBottom: 16 },
  inputLabel: { fontSize: 14, fontWeight: '600', color: '#334155', marginBottom: 6 },
  inputWrapper: { flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: '#cbd5e1', borderRadius: 10, paddingHorizontal: 12, backgroundColor: '#f8fafc', height: 46 },
  inputIcon: { marginRight: 10 },
  textInput: { flex: 1, fontSize: 15, color: '#1e293b' },
  loginButton: { backgroundColor: '#0284c7', height: 48, borderRadius: 10, flexDirection: 'row', justifyContent: 'center', alignItems: 'center', marginTop: 15 },
  loginButtonText: { color: '#ffffff', fontSize: 16, fontWeight: 'bold' },
  loadingContainer: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 12 },
});