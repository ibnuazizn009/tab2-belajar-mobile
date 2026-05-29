import React, { useState } from 'react';
import { StyleSheet, Text, View, TextInput, FlatList, TouchableOpacity } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import { Stack, router } from 'expo-router';

const DATA_SISWA = [
  { id: '1', nis: '202601001', nama_keals: '1A', nama: 'Aditya Pratama', saldo: 450000 },
  { id: '2', nis: '202601002', nama_keals: '1A', nama: 'Budi Santoso', saldo: 1200000 },
  { id: '3', nis: '202601003', nama_keals: '1A', nama: 'Citra Lestari', saldo: 320000 },
];

export default function LaporanScreen() {
  const [search, setSearch] = useState('');

  const formatRupiah = (angka: number | string) => {
    if (!angka) return 'Rp 0';
    const format = Number(angka).toLocaleString('id-ID');
    return `Rp ${format}`;
  };

  const filtered = DATA_SISWA.filter(item =>
    item.nama.toLowerCase().includes(search.toLowerCase()) || item.nis.includes(search)
  );

  return (
    <>
      <Stack.Screen options={{ title: 'Laporan' }} />
      <View style={styles.container}>
        <View style={styles.searchSection}>
          <View style={styles.searchWrapper}>
            <FontAwesome name="search" size={16} color="#94a3b8" style={{ marginRight: 8 }} />
            <TextInput
              style={styles.searchInput}
              placeholder="Cari nama atau NIS..."
              value={search}
              onChangeText={setSearch}
            />
          </View>
        </View>

        <FlatList
          data={filtered}
          keyExtractor={(item) => item.id}
          contentContainerStyle={{ padding: 20, paddingBottom: 40 }}
          ListEmptyComponent={
            <View style={styles.emptyState}>
              <FontAwesome name="folder-open" size={40} color="#cbd5e1" />
              <Text style={styles.emptyText}>Siswa tidak ditemukan</Text>
            </View>
          }
          renderItem={({ item }) => (
            <TouchableOpacity
              style={styles.siswaCard}
              onPress={() => router.navigate({ pathname: '/laporan/[nis]', params: { nis: item.nis, nama: item.nama, nama_kelas:item.nama_keals, saldo:item.saldo } })}
            >
              <View style={styles.profileBadge}>
                <FontAwesome name="user-circle" size={36} color="#0284c7" />
              </View>
              <View style={styles.infoSection}>
                <Text style={styles.siswaNama}>{item.nama}</Text>
                <Text style={styles.siswaDetail}>NIS: {item.nis}</Text>
              </View>
              <View style={styles.saldoSection}>
                <Text style={styles.saldoLabel}>Saldo</Text>
                <Text style={styles.saldoValue}>{formatRupiah(item.saldo)}</Text>
              </View>
              <FontAwesome name="chevron-right" size={14} color="#94a3b8" style={{ marginLeft: 8 }} />
            </TouchableOpacity>
          )}
        />
      </View>
    </>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  searchSection: { backgroundColor: '#fff', paddingHorizontal: 20, paddingVertical: 12, borderBottomWidth: 1, borderColor: '#e2e8f0' },
  searchWrapper: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#f1f5f9', borderRadius: 10, paddingHorizontal: 12, height: 40 },
  searchInput: { flex: 1, fontSize: 14, color: '#1e293b' },
  siswaCard: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', padding: 16, borderRadius: 12, marginBottom: 12, borderWidth: 1, borderColor: '#f1f5f9', elevation: 1 },
  profileBadge: { marginRight: 12 },
  infoSection: { flex: 1 },
  siswaNama: { fontSize: 15, fontWeight: 'bold', color: '#1e293b' },
  siswaDetail: { fontSize: 12, color: '#64748b', marginTop: 2 },
  saldoSection: { alignItems: 'flex-end' },
  saldoLabel: { fontSize: 11, color: '#94a3b8' },
  saldoValue: { fontSize: 14, fontWeight: 'bold', color: '#16a34a' },
  emptyState: { alignItems: 'center', marginTop: 40 },
  emptyText: { color: '#94a3b8', fontSize: 14, marginTop: 10 },
});