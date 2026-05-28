import React, { useState } from 'react';
import { StyleSheet, Text, View, TextInput, FlatList, TouchableOpacity } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';

// Dummy data master siswa aktif menabung
const DATA_SISWA_MOCK = [
  { id: '1', nis: '202601001', nama: 'Aditya Pratama', kelas: '7-A', saldo: 'Rp 450.000' },
  { id: '2', nis: '202601002', nama: 'Budi Santoso', kelas: '7-A', saldo: 'Rp 1.200.000' },
  { id: '3', nis: '202601003', nama: 'Citra Lestari', kelas: '7-B', saldo: 'Rp 320.000' },
  { id: '4', nis: '202601004', nama: 'Dinda Kirana', kelas: '8-C', saldo: 'Rp 2.550.000' },
  { id: '5', nis: '202601005', nama: 'Eko Prasetyo', kelas: '9-A', saldo: 'Rp 750.000' },
];

export default function RiwayatScreen() {
  const [search, setSearch] = useState('');
  
  // Fungsi filter pencarian berdasarkan nama atau NIS
  const filteredSiswa = DATA_SISWA_MOCK.filter(item => 
    item.nama.toLowerCase().includes(search.toLowerCase()) || item.nis.includes(search)
  );

  return (
    <View style={styles.container}>
      {/* Kolom Pencarian */}
      <View style={styles.searchSection}>
        <View style={styles.searchWrapper}>
          <FontAwesome name="search" size={16} color="#94a3b8" style={styles.searchIcon} />
          <TextInput
            style={styles.searchInput}
            placeholder="Cari nama atau NIS siswa..."
            value={search}
            onChangeText={setSearch}
          />
        </View>
      </View>

      {/* List Daftar Siswa */}
      <FlatList
        data={filteredSiswa}
        keyExtractor={(item) => item.id}
        contentContainerStyle={{ padding: 20, paddingBottom: 40 }}
        ListEmptyComponent={
          <View style={styles.emptyState}>
            <FontAwesome name="folder-open" size={40} color="#cbd5e1" />
            <Text style={styles.emptyText}>Siswa tidak ditemukan</Text>
          </View>
        }
        renderItem={({ item }) => (
          <View style={styles.siswaCard}>
            <View style={styles.profileBadge}>
              <FontAwesome name="user-circle" size={36} color="#0284c7" />
            </View>
            
            <View style={styles.infoSection}>
              <Text style={styles.siswaNama}>{item.nama}</Text>
              <Text style={styles.siswaDetail}>NIS: {item.nis} • Kelas {item.kelas}</Text>
            </View>

            <View style={styles.saldoSection}>
              <Text style={styles.saldoLabel}>Saldo</Text>
              <Text style={styles.saldoValue}>{item.saldo}</Text>
            </View>
          </View>
        )}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  searchSection: { backgroundColor: '#fff', paddingHorizontal: 20, paddingVertical: 12, borderBottomWidth: 1, borderColor: '#e2e8f0' },
  searchWrapper: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#f1f5f9', borderRadius: 10, paddingHorizontal: 12, height: 40 },
  searchIcon: { marginRight: 8 },
  searchInput: { flex: 1, fontSize: 14, color: '#1e293b' },
  siswaCard: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', padding: 16, borderRadius: 12, marginBottom: 12, borderWidth: 1, borderColor: '#f1f5f9', elevation: 1 },
  profileBadge: { marginRight: 12 },
  infoSection: { flex: 1 },
  siswaNama: { fontSize: 15, fontWeight: 'bold', color: '#1e293b' },
  siswaDetail: { fontSize: 12, color: '#64748b', marginTop: 2 },
  saldoSection: { alignItems: 'flex-end' },
  saldoLabel: { fontSize: 11, color: '#94a3b8', fontWeight: '500' },
  saldoValue: { fontSize: 14, fontWeight: 'bold', color: '#16a34a', marginTop: 2 },
  emptyState: { alignItems: 'center', marginTop: 40 },
  emptyText: { color: '#94a3b8', fontSize: 14, marginTop: 10 }
});