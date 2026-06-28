import React, { useState, useEffect } from "react";
import { useQueryClient } from "@tanstack/react-query";
import { useCallback } from 'react';
import * as SecureStore from "expo-secure-store";
import { useLocalSearchParams, useNavigation } from "expo-router";
import {
  StyleSheet,
  Text,
  View,
  TextInput,
  TouchableOpacity,
  ScrollView,
  ActivityIndicator,
} from "react-native";
import { FontAwesome } from "@expo/vector-icons";
import { tab2ApiService } from "../../services/Tab2apiservice";
import { tab2Toast } from "@/utils/tab2Toast";
import PageHeader from "@/components/PageHeader";
import { MinimalBlueBackground } from "@/components/BackgroundLinearGradient";
import { Dropdown } from "react-native-element-dropdown";

export default function TransaksiScreen() {
  const { defaultTipe, hideOther } = useLocalSearchParams<{
    defaultTipe?: "setor" | "tarik";
    hideOther?: string;
  }>();

  const queryClient = useQueryClient();
  const [nis, setNis] = useState("");
  const [nama, setNama] = useState("");
  const navigation = useNavigation<any>();
  const [tipe, setTipe] = useState<"setor" | "tarik" | "">(defaultTipe || "");
  const [nominal, setNominal] = useState("");
  const [listSiswa, setListSiswa] = useState<any[]>([]);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const formatInputNominal = (value: string) => {
    const nomorMurni = value.replace(/\D/g, "");
    return nomorMurni.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  };

  const loadDataSiswa = async (idYangDipilih: string) => {
    try {
      const responseData = await tab2ApiService.getNonMessage(
        `${process.env.EXPO_PUBLIC_API_URL}/siswa/siswa-per-kelas?kelasId=${idYangDipilih}`,
        "siswa"
      );
      if (responseData?.data) setListSiswa(responseData.data || []);
    } catch (error) { console.error("Load siswa error:", error); }
  };

  const postTransaksiData = async () => {
    if (!nis.trim() || !tipe || !nominal || Number(nominal) <= 0) {
      tab2Toast.error("Oops!", "Lengkapi data transaksi terlebih dahulu.");
      return;
    }

    try {
      setIsSubmitting(true);
      const payload = { nis, tipe, nominal: Number(nominal), nama_siswa: nama };
      const response = await tab2ApiService.post(
        `${process.env.EXPO_PUBLIC_API_URL}/transaksi/transaksi`,
        payload,
        "transaksi"
      );

      if (response?.success) {
        queryClient.invalidateQueries({ queryKey: ["totalTabunganSiswa"] });
        queryClient.invalidateQueries({ queryKey: ["transaksiHariIni"] });
        queryClient.invalidateQueries({ queryKey: ["riwayatSingkat"] });
        setNis(""); setNama(""); setTipe(""); setNominal("");
        tab2Toast.success(tipe === "setor" ? "Setoran Berhasil" : "Penarikan Berhasil", `${nama} — Rp ${Number(nominal).toLocaleString("id-ID")}`);
      } else {
        tab2Toast.error("Transaksi Gagal", response?.message || "Gagal menyimpan.");
      }
    } catch (error: any) {
      tab2Toast.error("Kesalahan", "Gagal menghubungi server.");
    } finally {
      setIsSubmitting(false);
    }
  };

  useEffect(() => {
    const initializeData = async () => {
      const raw = await SecureStore.getItemAsync("user_info");
      if (raw) {
        const user = JSON.parse(raw);
        await loadDataSiswa(user.kelas_id);
      }
    };
    initializeData();
  }, []);

  useEffect(() => {
    if (defaultTipe) {
      setTipe(defaultTipe);
    }
  }, [defaultTipe]);

  useEffect(() => {
    const unsubscribe = navigation.addListener('tabPress', (e: any) => {
      setTipe(""); 
    });

    return unsubscribe;
  }, [navigation]);

  return (
    <View style={styles.container}>
      <MinimalBlueBackground />
      <PageHeader title="Transaksi" subtitle="Setor & Tarik Tabungan" />
      
      {/* ScrollView dengan marginTop negatif agar overlap ke header seperti di camera.tsx[cite: 3] */}
      <ScrollView 
        style={styles.body} 
        contentContainerStyle={{ paddingBottom: 50 }}
        showsVerticalScrollIndicator={false}
      >
        <View style={styles.formCard}>
          <Text style={styles.sectionTitle}>Pencatatan Tabungan</Text>

          <Text style={styles.label}>Pilih Siswa</Text>
          <Dropdown
            style={styles.dropdownContainer}
            placeholderStyle={styles.placeholderStyle}
            selectedTextStyle={styles.selectedTextStyle}
            inputSearchStyle={styles.inputSearchStyle}
            iconStyle={styles.iconStyle}
            data={listSiswa}
            search
            labelField="nama_siswa"
            valueField="nis"
            placeholder="Cari nama siswa..."
            searchPlaceholder="Ketik nama atau NIS..."
            value={nis}
            renderLeftIcon={() => (
              <FontAwesome 
                name="search" 
                size={16} 
                color="#64748b" 
                style={{ marginRight: 10 }} 
              />
            )}
            onChange={(item: any) => { 
              setNis(item.nis); 
              setNama(item.nama_siswa); 
            }}
          />

          {nis !== "" && (
            <View style={styles.studentBadge}>
              <View style={styles.avatar}>
                 <FontAwesome name="user" size={20} color="#2563eb" />
              </View>
              <View>
                <Text style={styles.studentName}>{nama}</Text>
                <Text style={styles.studentNis}>NIS: {nis}</Text>
              </View>
            </View>
          )}

          <Text style={styles.label}>Jenis Transaksi</Text>
          <View style={styles.typeRow}>
              {!(hideOther === "true" && tipe === "tarik") && (
                <TouchableOpacity style={[styles.typeBox, tipe === "setor" && styles.activeSetor]} onPress={() => setTipe("setor")}>
                  <FontAwesome name="arrow-down" size={18} color={tipe === "setor" ? "#fff" : "#16a34a"} />
                  <Text style={[styles.typeText, tipe === "setor" && styles.activeText]}>Setor</Text>
                </TouchableOpacity>
              )}
              {!(hideOther === "true" && tipe === "setor") && (
                <TouchableOpacity style={[styles.typeBox, tipe === "tarik" && styles.activeTarik]} onPress={() => setTipe("tarik")}>
                  <FontAwesome name="arrow-up" size={18} color={tipe === "tarik" ? "#fff" : "#dc2626"} />
                  <Text style={[styles.typeText, tipe === "tarik" && styles.activeText]}>Tarik</Text>
                </TouchableOpacity>
              )}
          </View>

          <Text style={styles.label}>Nominal (Rp)</Text>
          <View style={styles.nominalContainer}>
              <TextInput
                style={styles.nominalInput}
                keyboardType="number-pad"
                placeholder="0"
                value={formatInputNominal(nominal)}
                onChangeText={(val) => setNominal(val.replace(/\D/g, ""))}
              />
          </View>

          <TouchableOpacity 
            style={styles.submitButton} 
            onPress={postTransaksiData} 
            disabled={isSubmitting}
          >
            {isSubmitting ? (
              <ActivityIndicator color="#FFF" style={{ marginRight: 8 }} />
            ) : (
              <FontAwesome name="check-circle" size={20} color="#FFF" style={{ marginRight: 8 }} />
            )}
            
            <Text style={styles.submitButtonText}>
              {isSubmitting ? "Memproses..." : "Konfirmasi Transaksi"}
            </Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: "#f8fafc" },
  body: { flex: 1, marginTop: -18, paddingHorizontal: 18 }, // Margin negatif dari camera.tsx[cite: 3]
  formCard: { 
    backgroundColor: "#fff", 
    borderRadius: 20, 
    padding: 24, 
    elevation: 5, 
    shadowColor: "#2563eb", 
    shadowOffset: { width: 0, height: 6 }, 
    shadowOpacity: 0.08, 
    shadowRadius: 12 
  },
  sectionTitle: { fontSize: 22, fontWeight: "700", color: "#0f172a", marginBottom: 20 },
  label: { fontSize: 13, fontWeight: "600", color: "#64748b", marginBottom: 8, marginTop: 12 },
  inputWrapper: { flexDirection: 'row', alignItems: 'center', height: 50, borderRadius: 16, borderWidth: 1, borderColor: '#e2e8f0', backgroundColor: '#f1f5f9', paddingHorizontal: 12 },
  icon: { marginRight: 10 },
  dropdown: { flex: 1, height: "100%" },
  studentBadge: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#f0f9ff', padding: 12, borderRadius: 16, marginTop: 10, borderWidth: 1, borderColor: '#e0f2fe' },
  avatar: { width: 40, height: 40, borderRadius: 20, backgroundColor: '#dbeafe', justifyContent: 'center', alignItems: 'center', marginRight: 12 },
  studentName: { fontSize: 15, fontWeight: '700', color: '#1e293b' },
  studentNis: { fontSize: 12, color: '#64748b' },
  typeRow: { flexDirection: 'row', gap: 12 },
  typeBox: { flex: 1, paddingVertical: 14, borderRadius: 16, borderWidth: 1, borderColor: '#e2e8f0', alignItems: 'center', justifyContent: 'center', flexDirection: 'row', gap: 8 },
  activeSetor: { backgroundColor: '#16a34a', borderColor: '#16a34a' },
  activeTarik: { backgroundColor: '#dc2626', borderColor: '#dc2626' },
  typeText: { fontWeight: '600', color: '#475569' },
  activeText: { color: '#fff' },
  nominalContainer: { backgroundColor: '#f1f5f9', borderRadius: 16, padding: 16 },
  nominalInput: { fontSize: 28, fontWeight: '800', color: '#0f172a', textAlign: 'center' },
  submitButton: {
    marginTop: 24,
    backgroundColor: '#2563eb',
    paddingVertical: 16,
    borderRadius: 16,
    // Kunci utama agar ikon dan text sejajar samping:
    flexDirection: 'row',     
    alignItems: 'center',      // Membuat ikon dan text sejajar secara vertikal
    justifyContent: 'center',  // Membuat konten berada tepat di tengah tombol
  },
  submitButtonText: { color: '#fff', fontWeight: '700', fontSize: 15 },
  dropdownContainer: {
    height: 52,
    backgroundColor: '#fff',
    borderRadius: 16,
    borderWidth: 1,
    borderColor: '#e2e8f0', // Border halus
    paddingHorizontal: 16,
    marginBottom: 8,
  },
  placeholderStyle: {
    fontSize: 14,
    color: '#94a3b8',
  },
  selectedTextStyle: {
    fontSize: 14,
    color: '#1e293b',
    fontWeight: '500',
  },
  inputSearchStyle: {
    height: 45,
    fontSize: 14,
    borderRadius: 12,
  },
  iconStyle: {
    width: 20,
    height: 20,
  },
});