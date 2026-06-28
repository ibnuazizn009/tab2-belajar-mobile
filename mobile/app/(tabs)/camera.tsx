import React from "react";
import { View, Text, StyleSheet, TouchableOpacity } from "react-native";
import { FontAwesome } from "@expo/vector-icons";
import PageHeader from "@/components/PageHeader";
import { MinimalBlueBackground } from "@/components/BackgroundLinearGradient";

export default function CameraScreen() {
  return (
    <View style={styles.container}>
      <MinimalBlueBackground />
      <PageHeader title="Take Photo" subtitle="Ambil dokumentasi transaksi" />
      <View style={styles.body}>
        <View style={styles.card}>
          <View style={styles.circle}>
            <FontAwesome name="camera" size={52} color="#2563eb" />
          </View>

          <Text style={styles.title}>Ambil Foto Baru</Text>

          <Text style={styles.subtitle}>
            Gunakan kamera untuk mengambil bukti transaksi, kartu siswa, ataupun
            dokumentasi lainnya.
          </Text>

          <TouchableOpacity style={styles.button}>
            <FontAwesome name="camera" size={18} color="#FFF" />

            <Text style={styles.buttonText}>Mulai Kamera</Text>
          </TouchableOpacity>
        </View>

        <View style={styles.tipCard}>
          <View style={styles.tipIcon}>
            <FontAwesome name="lightbulb-o" size={22} color="#2563eb" />
          </View>

          <View style={{ flex: 1 }}>
            <Text style={styles.tipTitle}>Tips</Text>

            <Text style={styles.tipText}>
              Pastikan pencahayaan cukup agar hasil foto lebih jelas.
            </Text>
          </View>
        </View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#f8fafc",
  },
  body: {
    flex: 1,
    marginTop: -18,
    paddingHorizontal: 18,
  },
  card: {
    backgroundColor: "#FFF",
    borderRadius: 20,
    paddingVertical: 32,
    paddingHorizontal: 26,
    alignItems: "center",
    shadowColor: "#2563eb",
    shadowOffset: {
      width: 0,
      height: 6,
    },
    shadowOpacity: 0.08,
    shadowRadius: 12,
    elevation: 5,
  },

  circle: {
    width: 120,
    height: 120,
    borderRadius: 60,
    backgroundColor: "#eff6ff",
    justifyContent: "center",
    alignItems: "center",
  },

  title: {
    marginTop: 24,
    fontSize: 24,
    fontWeight: "700",
    color: "#0f172a",
  },

  subtitle: {
    marginTop: 12,
    textAlign: "center",
    fontSize: 15,
    color: "#64748b",
    lineHeight: 24,
  },

  button: {
    marginTop: 28,
    width: "100%",
    height: 56,
    borderRadius: 18,
    backgroundColor: "#2563eb",
    justifyContent: "center",
    alignItems: "center",
    flexDirection: "row",
  },

  buttonText: {
    color: "#FFF",
    fontWeight: "700",
    fontSize: 18,
    marginLeft: 10,
  },

  tipCard: {
    marginTop: 20,
    backgroundColor: "#FFF",
    borderRadius: 20,
    padding: 18,
    flexDirection: "row",
    shadowColor: "#000",
    shadowOpacity: 0.03,
    shadowRadius: 8,
    elevation: 2,
  },

  tipIcon: {
    width: 42,
    alignItems: "center",
    marginTop: 2,
  },

  tipTitle: {
    fontSize: 18,
    fontWeight: "700",
    color: "#0f172a",
  },

  tipText: {
    marginTop: 6,
    color: "#64748b",
    lineHeight: 22,
    fontSize: 15,
  },
});
