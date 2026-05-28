import { useEffect, useRef } from 'react';
import { Animated, View, ScrollView, StyleSheet } from 'react-native';

// ===========================
// ATOM: Satu kotak skeleton
// ===========================
export const SkeletonBox = ({ width, height, style }: { 
  width: number | string, 
  height: number, 
  style?: any 
}) => {
  const opacity = useRef(new Animated.Value(0.4)).current;

  useEffect(() => {
    Animated.loop(
      Animated.sequence([
        Animated.timing(opacity, { toValue: 1, duration: 700, useNativeDriver: true }),
        Animated.timing(opacity, { toValue: 0.4, duration: 700, useNativeDriver: true }),
      ])
    ).start();
  }, []);

  return (
    <Animated.View style={[{
      width, height,
      backgroundColor: '#e2e8f0',
      borderRadius: 6,
      opacity
    }, style]} />
  );
};

// ===========================
// SKELETON: Halaman Home
// ===========================
export const SkeletonHome = () => (
  <ScrollView contentContainerStyle={{ padding: 20, paddingTop: 60 }}>
    <View style={{ flexDirection: 'row', justifyContent: 'space-between', marginBottom: 25 }}>
      <View>
        <SkeletonBox width={100} height={13} style={{ marginBottom: 8 }} />
        <SkeletonBox width={160} height={22} style={{ marginBottom: 6 }} />
        <SkeletonBox width={130} height={18} style={{ marginBottom: 6 }} />
        <SkeletonBox width={180} height={12} />
      </View>
      <SkeletonBox width={72} height={34} style={{ borderRadius: 8 }} />
    </View>

    <View style={{ backgroundColor: '#0284c7', borderRadius: 16, padding: 20,
                   flexDirection: 'row', justifyContent: 'space-between',
                   alignItems: 'center', marginBottom: 20 }}>
      <View>
        <SkeletonBox width={140} height={12} style={{ marginBottom: 10, opacity: 0.4 }} />
        <SkeletonBox width={180} height={28} style={{ opacity: 0.4 }} />
      </View>
      <SkeletonBox width={44} height={44} style={{ borderRadius: 22, opacity: 0.3 }} />
    </View>

    <View style={{ flexDirection: 'row', justifyContent: 'space-between', marginBottom: 25 }}>
      {[0, 1].map(i => (
        <View key={i} style={sk.statsBox}>
          <SkeletonBox width={32} height={32} style={{ borderRadius: 16, marginBottom: 8 }} />
          <SkeletonBox width={50} height={18} style={{ marginBottom: 6 }} />
          <SkeletonBox width={80} height={11} />
        </View>
      ))}
    </View>

    <SkeletonBox width={100} height={16} style={{ marginBottom: 14 }} />
    <View style={{ flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between' }}>
      {[0, 1, 2, 3].map(i => (
        <View key={i} style={sk.menuBox}>
          <SkeletonBox width={50} height={50} style={{ borderRadius: 25, marginBottom: 10 }} />
          <SkeletonBox width={80} height={13} />
        </View>
      ))}
    </View>
  </ScrollView>
);

// ===========================
// SKELETON: Halaman Transaksi
// ===========================
export const SkeletonTransaksi = () => (
  <ScrollView contentContainerStyle={{ padding: 20, paddingTop: 24 }}>
    {/* Dropdown / Pilih Siswa */}
    <SkeletonBox width={120} height={13} style={{ marginBottom: 8 }} />
    <SkeletonBox width='100%' height={46} style={{ borderRadius: 10, marginBottom: 20 }} />

    {/* Input Nominal */}
    <SkeletonBox width={100} height={13} style={{ marginBottom: 8 }} />
    <SkeletonBox width='100%' height={46} style={{ borderRadius: 10, marginBottom: 20 }} />

    {/* Pilihan Jenis Transaksi */}
    <SkeletonBox width={130} height={13} style={{ marginBottom: 8 }} />
    <View style={{ flexDirection: 'row', gap: 10, marginBottom: 20 }}>
      <SkeletonBox width='48%' height={44} style={{ borderRadius: 10 }} />
      <SkeletonBox width='48%' height={44} style={{ borderRadius: 10 }} />
    </View>

    {/* Keterangan */}
    <SkeletonBox width={90} height={13} style={{ marginBottom: 8 }} />
    <SkeletonBox width='100%' height={80} style={{ borderRadius: 10, marginBottom: 24 }} />

    {/* Tombol Submit */}
    <SkeletonBox width='100%' height={48} style={{ borderRadius: 10 }} />
  </ScrollView>
);

// ===========================
// SKELETON: Halaman Riwayat (List)
// ===========================
export const SkeletonRiwayat = () => (
  <ScrollView contentContainerStyle={{ padding: 20, paddingTop: 24 }}>
    {/* Search bar */}
    <SkeletonBox width='100%' height={44} style={{ borderRadius: 10, marginBottom: 16 }} />

    {/* List item */}
    {[0, 1, 2, 3, 4, 5].map(i => (
      <View key={i} style={sk.listItem}>
        <SkeletonBox width={44} height={44} style={{ borderRadius: 22, marginRight: 12 }} />
        <View style={{ flex: 1 }}>
          <SkeletonBox width={140} height={14} style={{ marginBottom: 8 }} />
          <SkeletonBox width={100} height={11} />
        </View>
        <View style={{ alignItems: 'flex-end' }}>
          <SkeletonBox width={80} height={14} style={{ marginBottom: 8 }} />
          <SkeletonBox width={60} height={11} />
        </View>
      </View>
    ))}
  </ScrollView>
);

// ===========================
// SKELETON: Generic (Fallback)
// Untuk halaman baru yang belum punya skeleton khusus
// ===========================
export const SkeletonGeneric = ({ rows = 5 }: { rows?: number }) => (
  <ScrollView contentContainerStyle={{ padding: 20, paddingTop: 24 }}>
    {Array.from({ length: rows }).map((_, i) => (
      <View key={i} style={sk.listItem}>
        <View style={{ flex: 1 }}>
          <SkeletonBox width='60%' height={14} style={{ marginBottom: 8 }} />
          <SkeletonBox width='40%' height={11} />
        </View>
      </View>
    ))}
  </ScrollView>
);

const sk = StyleSheet.create({
  statsBox: {
    flex: 1,
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 15,
    marginHorizontal: 5,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#f1f5f9',
  },
  menuBox: {
    width: '48%',
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 15,
    alignItems: 'center',
    marginBottom: 15,
    borderWidth: 1,
    borderColor: '#f1f5f9',
  },
  listItem: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 14,
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 10,
    borderWidth: 1,
    borderColor: '#f1f5f9',
  },
});