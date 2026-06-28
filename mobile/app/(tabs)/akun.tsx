import React from "react";
import { View, Text, StyleSheet, TouchableOpacity } from "react-native";
import { FontAwesome } from "@expo/vector-icons";
import * as SecureStore from "expo-secure-store";
import { useQueryClient } from "@tanstack/react-query";
import PageHeader from "@/components/PageHeader";
import { triggerLogoutGlobal } from "./_layout";
import { MinimalBlueBackground } from "@/components/BackgroundLinearGradient";

export default function AkunScreen() {
  const queryClient = useQueryClient();

  const MenuItem = ({ icon, title, color = "#0f172a", onPress }: any) => (
    <TouchableOpacity
      style={styles.menuItem}
      activeOpacity={0.7}
      onPress={onPress}
    >
      <View style={styles.leftMenu}>
        <View style={styles.iconBox}>
          <FontAwesome name={icon} size={18} color={color} />
        </View>

        <Text style={[styles.menuText, { color }]}>{title}</Text>
      </View>

      <FontAwesome name="angle-right" size={20} color="#94a3b8" />
    </TouchableOpacity>
  );

  return (
    <View style={styles.container}>
      <MinimalBlueBackground />
      <PageHeader title="Profil Saya" subtitle="Kelola akun aplikasi" />

      <View style={styles.body}>
        {/* CARD PROFILE */}

        <View style={styles.profileCard}>
          <View style={styles.avatar}>
            <FontAwesome name="user" size={44} color="#2563eb" />
          </View>

          <Text style={styles.name}>Administrator</Text>

          <Text style={styles.role}>Petugas e-Tabungan</Text>
        </View>

        {/* MENU */}

        <View style={styles.menuCard}>
          <MenuItem icon="user-circle-o" title="Profil Saya" />

          <MenuItem icon="lock" title="Ubah Password" />

          <MenuItem icon="bell-o" title="Notifikasi" />

          <MenuItem icon="info-circle" title="Tentang Aplikasi" />
        </View>

        {/* LOGOUT */}

        <TouchableOpacity
          style={styles.logoutButton}
          onPress={triggerLogoutGlobal}
        >
          <FontAwesome name="sign-out" size={18} color="#fff" />

          <Text style={styles.logoutText}>Keluar dari Aplikasi</Text>
        </TouchableOpacity>
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

  profileCard: {
    backgroundColor: "#fff",
    borderRadius: 20,
    paddingVertical: 30,
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

  avatar: {
    width: 92,
    height: 92,
    borderRadius: 46,
    backgroundColor: "#eff6ff",
    justifyContent: "center",
    alignItems: "center",
  },

  name: {
    marginTop: 18,
    fontSize: 22,
    fontWeight: "700",
    color: "#0f172a",
  },

  role: {
    marginTop: 6,
    color: "#64748b",
    fontSize: 15,
  },

  menuCard: {
    marginTop: 18,
    backgroundColor: "#fff",
    borderRadius: 22,
    overflow: "hidden",
    shadowColor: "#000",
    shadowOpacity: 0.03,
    shadowRadius: 8,
    elevation: 2,
  },

  menuItem: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 18,
    height: 62,
    borderBottomWidth: 1,
    borderBottomColor: "#f1f5f9",
  },

  leftMenu: {
    flexDirection: "row",
    alignItems: "center",
  },

  iconBox: {
    width: 36,
    height: 36,
    borderRadius: 10,
    backgroundColor: "#eff6ff",
    justifyContent: "center",
    alignItems: "center",
  },

  menuText: {
    marginLeft: 14,
    fontSize: 15,
    fontWeight: "600",
  },

  logoutButton: {
    marginTop: 26,
    height: 56,
    borderRadius: 18,
    backgroundColor: "#dc2626",
    justifyContent: "center",
    alignItems: "center",
    flexDirection: "row",
    shadowColor: "#dc2626",
    shadowOpacity: 0.18,
    shadowRadius: 8,
    elevation: 4,
  },

  logoutText: {
    marginLeft: 10,
    color: "#fff",
    fontWeight: "700",
    fontSize: 16,
  },
});
