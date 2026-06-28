import { Tabs, router } from 'expo-router';
import React, { useState, useRef, useEffect } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { StyleSheet, Text, View, TextInput, TouchableOpacity, Alert, PanResponder, ActivityIndicator, AppState, AppStateStatus } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import * as SecureStore from 'expo-secure-store'
import { tab2ApiService } from '../../services/Tab2apiservice'
import LoginView from '@/components/LoginView';
import { tab2Toast } from '@/utils/tab2Toast';
import { Ionicons } from '@expo/vector-icons';

// Fungsi global agar bisa dipanggil dari file index.tsx (Dashboard) untuk logout
export let triggerLogoutGlobal = () => {};

const TIMEOUT_IDLE = 30 * 60 * 1000; // 30 Menit Otomatis Logout
const BACKGROUND_TIMESTAMP_KEY = 'last_background_at';

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
        if (!token) return;

        // Jika app sempat di-kill saat background, timestamp ini masih tersimpan.
        // Cek dulu sebelum login otomatis, jangan sampai sesi lama (>30 menit) lolos.
        const storedTimestamp = await SecureStore.getItemAsync(BACKGROUND_TIMESTAMP_KEY);
        if (storedTimestamp) {
          const elapsed = Date.now() - parseInt(storedTimestamp, 10);
          await SecureStore.deleteItemAsync(BACKGROUND_TIMESTAMP_KEY);

          if (elapsed >= TIMEOUT_IDLE) {
            // Tidak perlu tampilkan alert di cold start, cukup bersihkan token diam-diam
            try {
              await tab2ApiService.postNonMessage(
                `${process.env.EXPO_PUBLIC_API_URL}/auth/logout`,
                {},
                'logout'
              );
            } catch (error) {
              console.log('Logout API gagal saat cold-start check, lanjut cleanup lokal:', error);
            }
            await SecureStore.deleteItemAsync('access_token');
            await SecureStore.deleteItemAsync('user_info');
            return;
          }
        }

        setIsLoggedIn(true);
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

  // Logic inti logout: hapus token lokal + clear query cache.
  // Dipakai bersama oleh auto-logout (idle/background) dan logout manual.
  const clearSessionLocally = async () => {
    await SecureStore.deleteItemAsync('access_token');
    await SecureStore.deleteItemAsync('user_info');
    await SecureStore.deleteItemAsync(BACKGROUND_TIMESTAMP_KEY);
    queryClient.clear();
    setIsLoggedIn(false);
    setUsername('');
    setPassword('');
  };

  const handleAutoLogout = async (reason: 'idle' | 'background' = 'idle') => {
    try {
      // Beri tahu server agar token ini di-invalidate juga (mis. is_use / blacklist)
      await tab2ApiService.postNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/auth/logout`,
        {},
        'logout'
      );
    } catch (error) {
      console.log('Logout API gagal saat auto-logout, lanjut cleanup lokal:', error);
    }

    await clearSessionLocally();

    Alert.alert(
      "Sesi Berakhir",
      reason === 'background'
        ? "Anda telah otomatis keluar karena aplikasi tidak aktif terlalu lama demi keamanan data."
        : "Anda telah otomatis keluar karena tidak ada aktivitas selama beberapa saat demi keamanan data.",
      [{ text: "Mengerti" }]
    );
  };

  triggerLogoutGlobal = async () => {
    try {
      await tab2ApiService.postNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/auth/logout`,
        {},
        'logout'
      );
    } catch (error) {
      console.log('Logout API gagal, lanjut cleanup lokal:', error);
    }
  
    tab2Toast.info(
      'Sampai Jumpa!',
      'Anda berhasil keluar dari aplikasi.',
      {
        duration: 2000,
        onHide: () => {
          clearSessionLocally();
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

  // Deteksi idle saat app di background (mis. diminimize / layar terkunci).
  // JS timer (setTimeout) tidak reliable saat app disuspend, jadi kita catat
  // timestamp saat masuk background lalu hitung selisihnya saat kembali foreground.
  useEffect(() => {
    const handleAppStateChange = async (nextState: AppStateStatus) => {
      if (!isLoggedIn) return;

      if (nextState === 'background' || nextState === 'inactive') {
        try {
          await SecureStore.setItemAsync(BACKGROUND_TIMESTAMP_KEY, Date.now().toString());
        } catch (error) {
          console.error('Gagal menyimpan timestamp background:', error);
        }
        // Hentikan timer foreground, biar tidak dobel dengan pengecekan background
        if (timerRef.current) clearTimeout(timerRef.current);
      }

      if (nextState === 'active') {
        try {
          const storedTimestamp = await SecureStore.getItemAsync(BACKGROUND_TIMESTAMP_KEY);
          if (storedTimestamp) {
            const elapsed = Date.now() - parseInt(storedTimestamp, 10);
            await SecureStore.deleteItemAsync(BACKGROUND_TIMESTAMP_KEY);

            if (elapsed >= TIMEOUT_IDLE) {
              await handleAutoLogout('background');
              return; // sudah logout, tidak perlu reset timer lagi
            }
          }
        } catch (error) {
          console.error('Gagal memeriksa timestamp background:', error);
        }
        // Kembali aktif & belum kena timeout -> lanjutkan idle timer foreground
        resetIdleTimer();
      }
    };

    const subscription = AppState.addEventListener('change', handleAppStateChange);

    return () => {
      subscription.remove();
    };
  }, [isLoggedIn]);

  const panResponder = useRef(
    PanResponder.create({
      onStartShouldSetPanResponder: () => {
        resetIdleTimer();
        return false; 
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
          headerShown: false,
          tabBarShowLabel: true,
          tabBarActiveTintColor: '#2563eb',
          tabBarInactiveTintColor: '#94a3b8',
          freezeOnBlur: true,
          lazy: true,
          tabBarStyle: {
              position: 'absolute',
              left: 16,
              right: 16,
              bottom: 16,
              height: 72,
              borderRadius: 22,
              backgroundColor: '#ffffff',
              borderTopWidth: 0,
              elevation: 15,
              shadowColor: '#2563eb',
              shadowOpacity: 0.12,
              shadowRadius: 15,
              shadowOffset: {
                  width: 0,
                  height: 8,
              },
          },
      
          tabBarLabelStyle: {
              fontSize: 11,
              fontWeight: '600',
              marginBottom: 6,
          },
      
          tabBarIconStyle: {
              marginTop: 6,
          },
      }}>
        
        {/* Tab 1: Beranda */}
        <Tabs.Screen
            name="index"
            options={{
                title: "Home",
                headerShown:false,
                tabBarIcon: ({color,size})=>(
                    <Ionicons
                        name="home"
                        color={color}
                        size={24}
                    />
                ),
            }}
        />

        {/* Tab 2: Transaksi */}
        <Tabs.Screen
            name="transaksi"
            options={{
                title:"Transaksi",
                headerTitle:"Input Transaksi",
                tabBarIcon:({color})=>(
                    <Ionicons
                        name="swap-horizontal"
                        size={25}
                        color={color}
                    />
                )
            }}
        />

        {/* Tab 3: Camera */}
        <Tabs.Screen
            name="camera"
            options={{
                title:"",
                tabBarIcon:({focused})=>(
                    <View
                        style={{
                            width:60,
                            height:60,
                            borderRadius:30,
                            backgroundColor:"#2563eb",

                            justifyContent:"center",
                            alignItems:"center",

                            marginTop:-25,

                            shadowColor:"#2563eb",
                            shadowOpacity:0.35,
                            shadowRadius:10,
                            elevation:10,
                        }}
                    >
                        <Ionicons
                            name="camera"
                            size={28}
                            color="#FFF"
                        />
                    </View>
                )
            }}
        />

        {/* Tab 4: Riwayat */}
        <Tabs.Screen
            name="riwayat"
            options={{
                title:"Riwayat",
                headerTitle:"Riwayat Transaksi",
                tabBarIcon:({color})=>(
                    <Ionicons
                        name="receipt-outline"
                        size={23}
                        color={color}
                    />
                )
            }}
        />

        {/* Tab 5: Akun */}
        <Tabs.Screen
            name="akun"
            options={{
                title:"Akun",
                tabBarIcon:({color})=>(
                    <Ionicons
                        name="person-circle"
                        size={26}
                        color={color}
                    />
                )
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