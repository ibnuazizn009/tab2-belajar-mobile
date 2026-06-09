import React from 'react';
import { View, Text, TextInput, TouchableOpacity, ActivityIndicator, StyleSheet } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import { AppToast } from '@/components/ToastProvider'

interface LoginViewProps {
  username: string;
  setUsername: (text: string) => void;
  password: string;
  setPassword: (text: string) => void;
  handleLogin: () => void;
  isSubmitting: boolean;
  styles: any;
  onSwitchToRegister: () => void; // 👈 Tambahkan ini
}

export default function LoginView({
  username,
  setUsername,
  password,
  setPassword,
  handleLogin,
  isSubmitting,
  styles,
  onSwitchToRegister, // 👈 Destructure di sini
}: LoginViewProps) {
  return (
    <View style={styles.loginContainer}>
      <View style={styles.loginCard}>
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

        {/* Tautan ke Register */}
        <View style={localStyles.toggleContainer}>
          <Text style={localStyles.toggleText}>Belum punya akun? </Text>
          <TouchableOpacity onPress={onSwitchToRegister}>
            <Text style={localStyles.toggleLink}>Daftar sekarang</Text>
          </TouchableOpacity>
        </View>

      </View>
      <AppToast topOffset={100} />
    </View>
  );
}

const localStyles = StyleSheet.create({
  toggleContainer: { flexDirection: 'row', justifyContent: 'center', alignItems: 'center', marginTop: 20 },
  toggleText: { fontSize: 14, color: '#64748b' },
  toggleLink: { fontSize: 14, color: '#0284c7', fontWeight: '600' },
});