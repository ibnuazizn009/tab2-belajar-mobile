import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { useRouter, Stack } from 'expo-router';
import { FontAwesome, MaterialIcons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

export default function PusatBantuan() {
  const insets = useSafeAreaInsets();
  const router = useRouter();

  return (
    <View style={[styles.mainContainer, { backgroundColor: '#f8fafc' }]}>
      <Stack.Screen options={{ headerShown: false }} />
      <View style={[styles.headerContainer, { paddingTop: insets.top + 10 }]}>
        <View style={styles.headerContent}>
            <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
            <FontAwesome name="arrow-left" size={18} color="#1e293b" />
            </TouchableOpacity>
            
            <Text style={styles.headerTitle}>Pusat Bantuan</Text>
            
            {/* Mengosongkan komentar di sini untuk menghindari error spasi literal */}
            <View style={{ width: 40 }} />
        </View>
      </View>
      
      <View style={styles.content}>
        <MaterialIcons name="headset-mic" size={64} color="#2563eb" />
        <Text style={styles.title}>Pusat Bantuan</Text>
        <Text style={styles.description}>
          Ada kendala atau pertanyaan? Silakan hubungi tim dukungan kami melalui WhatsApp di bawah ini.
        </Text>
        
        <TouchableOpacity style={styles.waButton}>
          <FontAwesome name="whatsapp" size={20} color="white" style={{ marginRight: 10 }} />
          <Text style={styles.waText}>Hubungi CS via WhatsApp</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc', padding: 20 },
  mainContainer: { flex: 1 },
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
  content: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  title: { fontSize: 24, fontWeight: '800', marginTop: 20, color: '#1e293b' },
  description: { textAlign: 'center', color: '#64748b', marginTop: 10, marginBottom: 30, paddingHorizontal: 20 },
  waButton: { flexDirection: 'row', backgroundColor: '#25d366', paddingVertical: 12, paddingHorizontal: 24, borderRadius: 30 },
  waText: { color: '#ffffff', fontWeight: '700' },
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
  headerTitle: { fontSize: 18, fontWeight: '700', color: '#0f172a' },
});