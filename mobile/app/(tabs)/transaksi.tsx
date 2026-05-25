import React, { useState } from 'react';
import { StyleSheet, Text, View, TextInput, TouchableOpacity, ScrollView, Alert } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';

export default function TransaksiScreen() {
  const [nis, setNis] = useState('');
  const [nama, setNama] = useState('');
  const [tipe, setTipe] = useState<'setor' | 'tarik'>('setor');
  const [nominal, setNominal] = useState('');

  const handleSimpan = () => {
    if (!nis || !nominal) {
      Alert.alert("Data Belum Lengkap", "Silakan isi NIS dan Nominal transaksi terlebih dahulu.");
      return;
    }

    Alert.alert(
      "Transaksi Berhasil",
      `Berhasil menyimpan transaksi ${tipe === 'setor' ? 'SETORAN' : 'PENARIKAN'} sebesar Rp ${parseInt(nominal).toLocaleString('id-ID')} untuk siswa bernama ${nama || 'Siswa (Mochammad)'}`
    );
    
    // Reset Form setelah sukses
    setNis('');
    setNama('');
    setNominal('');
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.contentContainer}>
      <View style={styles.formCard}>
        <Text style={styles.sectionTitle}>Pencatatan Tabungan</Text>

        {/* Input NIS */}
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Nomor Induk Siswa (NIS)</Text>
          <View style={styles.inputWrapper}>
            <FontAwesome name="id-card" size={16} color="#64748b" style={styles.icon} />
            <TextInput
              style={styles.input}
              placeholder="Contoh: 202601001"
              keyboardType="number-pad"
              value={nis}
              onChangeText={(val) => {
                setNis(val);
                // Simulasi auto-fill nama ketika NIS diisi
                if (val === '123') setNama('Mochammad Ibnu');
              }}
            />
          </View>
        </View>

        {/* Input Nama Siswa */}
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Nama Siswa (Otomatis/Manual)</Text>
          <View style={styles.inputWrapper}>
            <FontAwesome name="user" size={16} color="#64748b" style={styles.icon} />
            <TextInput
              style={styles.input}
              placeholder="Nama akan muncul otomatis atau ketik manual"
              value={nama}
              onChangeText={setNama}
            />
          </View>
        </View>

        {/* Pilihan Tipe Transaksi */}
        <Text style={styles.label}>Jenis Transaksi</Text>
        <View style={styles.typeRow}>
          <TouchableOpacity 
            style={[styles.typeButton, tipe === 'setor' && styles.activeSetor]} 
            onPress={() => setTipe('setor')}
          >
            <FontAwesome name="plus-circle" size={18} color={tipe === 'setor' ? '#fff' : '#16a34a'} />
            <Text style={[styles.typeText, tipe === 'setor' && styles.activeTypeText]}>Setor Tunai</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={[styles.typeButton, tipe === 'tarik' && styles.activeTarik]} 
            onPress={() => setTipe('tarik')}
          >
            <FontAwesome name="minus-circle" size={18} color={tipe === 'tarik' ? '#fff' : '#dc2626'} />
            <Text style={[styles.typeText, tipe === 'tarik' && styles.activeTypeText]}>Tarik Tunai</Text>
          </TouchableOpacity>
        </View>

        {/* Input Nominal */}
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Nominal Transaksi (Rp)</Text>
          <View style={styles.inputWrapper}>
            <Text style={styles.rpText}>Rp</Text>
            <TextInput
              style={styles.input}
              placeholder="Contoh: 50000"
              keyboardType="number-pad"
              value={nominal}
              onChangeText={setNominal}
            />
          </View>
        </View>

        {/* Tombol Simpan */}
        <TouchableOpacity style={styles.submitButton} onPress={handleSimpan}>
          <FontAwesome name="save" size={18} color="#fff" />
          <Text style={styles.submitButtonText}>Simpan Transaksi</Text>
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  contentContainer: { padding: 20 },
  formCard: { backgroundColor: '#fff', borderRadius: 16, padding: 20, borderWidth: 1, borderColor: '#e2e8f0', elevation: 2 },
  sectionTitle: { fontSize: 18, fontWeight: 'bold', color: '#1e293b', marginBottom: 20 },
  inputGroup: { marginBottom: 16 },
  label: { fontSize: 14, fontWeight: '600', color: '#475569', marginBottom: 6 },
  inputWrapper: { flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: '#cbd5e1', borderRadius: 10, paddingHorizontal: 12, backgroundColor: '#f8fafc', height: 46 },
  icon: { marginRight: 10 },
  rpText: { fontSize: 15, fontWeight: 'bold', color: '#64748b', marginRight: 8 },
  input: { flex: 1, fontSize: 15, color: '#1e293b' },
  typeRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 20 },
  typeButton: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', height: 46, borderWidth: 1, borderColor: '#cbd5e1', borderRadius: 10, marginHorizontal: 4, backgroundColor: '#fff' },
  activeSetor: { backgroundColor: '#16a34a', borderColor: '#16a34a' },
  activeTarik: { backgroundColor: '#dc2626', borderColor: '#dc2626' },
  typeText: { fontSize: 14, fontWeight: '600', color: '#475569', marginLeft: 8 },
  activeTypeText: { color: '#fff' },
  submitButton: { backgroundColor: '#0284c7', height: 48, borderRadius: 10, flexDirection: 'row', justifyContent: 'center', alignItems: 'center', marginTop: 10 },
  submitButtonText: { color: '#fff', fontSize: 16, fontWeight: 'bold', marginLeft: 8 }
});