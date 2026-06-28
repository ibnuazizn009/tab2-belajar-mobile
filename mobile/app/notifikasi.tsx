import React, { useState } from 'react';
import { StyleSheet, Text, View, ScrollView, TouchableOpacity, FlatList } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import { router, Stack } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

// 1. DATA DUMMY NOTIFIKASI PETUGAS
const DUMMY_NOTIFIKASI = [
  {
    id: '1',
    tipe: 'setoran', // Sukses input uang masuk
    judul: 'Setoran Berhasil Dicatat ✨',
    pesan: 'Setoran tabungan atas nama Ahmad Fauzi (Kelas 4A) sebesar Rp 50.000 telah sukses masuk ke sistem.',
    waktu: '10 Menit yang lalu',
    sudahDibaca: false,
  },
  {
    id: '2',
    tipe: 'peringatan', // Masalah akun / sisa premium
    judul: 'Masa Aktif Premium Menipis ⚠️',
    pesan: 'Sisa masa aktif Premium sekolah tinggal 5 hari lagi. Fitur kirim WhatsApp otomatis akan dibekukan jika terlambat diperpanjang.',
    waktu: '2 Jam yang lalu',
    sudahDibaca: false,
  },
  {
    id: '3',
    tipe: 'penarikan', // Sukses uang keluar
    judul: 'Penarikan Saldo Disetujui 💸',
    pesan: 'Penarikan dana siswa Siti Aminah sebesar Rp 25.000 berhasil divalidasi oleh sistem.',
    waktu: 'Kemarin, 14:20',
    sudahDibaca: true,
  },
  {
    id: '4',
    tipe: 'sistem', // Update fitur / info maintenance
    judul: 'Pembaruan Aplikasi Versi 1.2 🚀',
    pesan: 'Sistem aplikasi HP kini dioptimalkan! Loading pencarian nama siswa sekarang 2x lebih cepat dari versi sebelumnya.',
    waktu: '3 Hari yang lalu',
    sudahDibaca: true,
  },
];

export default function NotifikasiScreen() {
  const insets = useSafeAreaInsets();
  const [notifList, setNotifList] = useState(DUMMY_NOTIFIKASI);

  // Fungsi Dummy: Tandai Semua Telah Dibaca
  const tandaiSemuaDibaca = () => {
    const updated = notifList.map(item => ({ ...item, sudahDibaca: true }));
    setNotifList(updated);
  };

  // Fungsi pembantu untuk menentukan ikon & warna berdasarkan tipe notifikasi
  const getStyleByTipe = (tipe: string) => {
    switch (tipe) {
      case 'setoran':
        return { ikon: 'plus-circle', warna: '#16a34a', bg: '#f0fdf4' } as const; // 👈 Tambahkan as const
      case 'penarikan':
        return { ikon: 'minus-circle', warna: '#dc2626', bg: '#fef2f2' } as const; // 👈 Tambahkan as const
      case 'peringatan':
        return { ikon: 'exclamation-triangle', warna: '#d97706', bg: '#fffbeb' } as const; // 👈 Tambahkan as const
      default:
        return { ikon: 'info-circle', warna: '#2563eb', bg: '#eff6ff' } as const; // 👈 Tambahkan as const
    }
  };

  return (
    <View style={[styles.mainContainer, { backgroundColor: '#f8fafc' }]}>
      {/* HEADER SECTION BAR */}
      <Stack.Screen options={{ headerShown: false }} />
      <View style={[styles.headerContainer, { paddingTop: insets.top + 10 }]}>
        <View style={styles.headerContent}>
            <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
            <FontAwesome name="arrow-left" size={18} color="#1e293b" />
            </TouchableOpacity>
            
            <Text style={styles.headerTitle}>Notifikasi</Text>
            
            {/* Mengosongkan komentar di sini untuk menghindari error spasi literal */}
            <View style={{ width: 40 }} />
        </View>
      </View>

      {/* SUB-HEADER / FUNGSIONALITAS UTAMA */}
      <View style={styles.subHeader}>
        <Text style={styles.notifCount}>
          {notifList.filter(n => !n.sudahDibaca).length} Belum dibaca
        </Text>
        <TouchableOpacity onPress={tandaiSemuaDibaca} activeOpacity={0.6}>
          <Text style={styles.markReadText}>Tandai semua dibaca</Text>
        </TouchableOpacity>
      </View>

      {/* DAFTAR NOTIFIKASI LIST */}
      <FlatList
        data={notifList}
        keyExtractor={(item) => item.id}
        contentContainerStyle={styles.listContent}
        showsVerticalScrollIndicator={false}
        renderItem={({ item }) => {
            const config = getStyleByTipe(item.tipe);
            
            return (
              <TouchableOpacity 
                style={[
                  styles.notifCard, 
                  !item.sudahDibaca && styles.notifCardUnread
                ]}
                activeOpacity={0.8}
              >
                {!item.sudahDibaca && <View style={styles.leftAccentBar} />}

                {/* Bulatan Ikon Kiri */}
                <View style={[styles.iconContainer, { backgroundColor: config.bg }]}>
                  <FontAwesome name={config.ikon as any} size={20} color={config.warna} />
                </View>
          
                {/* Konten Teks Tengah */}
                <View style={styles.textContainer}>
                  <View style={styles.titleRow}>
                    <Text style={[
                      styles.notifTitle, 
                      item.sudahDibaca ? styles.textRead : styles.textUnread
                    ]}>
                      {item.judul}
                    </Text>
                    {/* Titik Biru Indikator Unread */}
                    {!item.sudahDibaca && <View style={styles.unreadDot} />}
                  </View>
                  
                  {/* Teks pesan otomatis lebih redup jika sudah dibaca */}
                  <Text 
                    style={[styles.notifPesan, { color: item.sudahDibaca ? '#94a3b8' : '#475569' }]} 
                    numberOfLines={3}
                  >
                    {item.pesan}
                  </Text>
                  <Text style={styles.notifWaktu}>{item.waktu}</Text>
                </View>
              </TouchableOpacity>
            );
        }}
        ListEmptyComponent={
          <View style={styles.emptyState}>
            <FontAwesome name="bell-slash-o" size={48} color="#cbd5e1" />
            <Text style={styles.emptyText}>Tidak ada notifikasi saat ini</Text>
          </View>
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  mainContainer: { flex: 1 },
  
  // Gaya Header Atas Modern
  headerContainer: {
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderColor: '#f1f5f9',
    paddingBottom: 14,
  },
  headerContent: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
  },
  backButton: {
    width: 40,
    height: 40,
    borderRadius: 10,
    backgroundColor: '#f8fafc',
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  headerTitle: { fontSize: 18, fontWeight: '700', color: '#0f172a' },

  // Gaya Sub Header Kontrol
  subHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 14,
  },
  notifCount: { fontSize: 13, fontWeight: '700', color: '#64748b' },
  markReadText: { fontSize: 13, fontWeight: '600', color: '#2563eb' },

  // List & Desain Kartu Notifikasi Premium
  listContent: { paddingHorizontal: 16, paddingBottom: 30 },
  notifCard: {
    flexDirection: 'row',
    backgroundColor: '#ffffff',
    borderRadius: 16,
    padding: 16,
    marginBottom: 12,
    borderWidth: 1,
    borderColor: '#e2e8f0',    
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.01,
    shadowRadius: 4,
    elevation: 1,
    position: 'relative', 
    overflow: 'hidden',
  },
  notifCardUnread: {
    backgroundColor: '#eff6ff', 
    borderColor: '#bfdbfe',     
  },
  leftAccentBar: {
    position: 'absolute',
    left: 0,
    top: 0,
    bottom: 0,
    width: 5,
    backgroundColor: '#2563eb', // Warna Royal Blue utama Anda
  },
  iconContainer: {
    width: 44,
    height: 44,
    borderRadius: 12,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 14,
  },
  textContainer: { flex: 1 },
  titleRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingRight: 4,
  },
  notifTitle: { fontSize: 14, flex: 1, paddingRight: 8 },
  textUnread: { color: '#0f172a', fontWeight: '700' }, 
  textRead: { color: '#64748b', fontWeight: '600' },
  textBold: { color: '#0f172a', fontWeight: '700' },
  unreadDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#2563eb', 
  },
  notifPesan: { fontSize: 13, color: '#475569', marginTop: 4, lineHeight: 18 },
  notifWaktu: { fontSize: 11, color: '#94a3b8', fontWeight: '500', marginTop: 8 },

  emptyState: { alignItems: 'center', justifyContent: 'center', marginTop: 100 },
  emptyText: { fontSize: 14, color: '#94a3b8', fontWeight: '600', marginTop: 12 },
});