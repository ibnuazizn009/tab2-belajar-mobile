import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { BaseToast, ErrorToast, ToastConfig } from 'react-native-toast-message';
import { FontAwesome } from '@expo/vector-icons';

// ─── Token Warna ────────────────────────────────────────────────
const COLOR = {
  success: { bg: '#f0fdf4', border: '#16a34a', icon: '#16a34a', title: '#14532d', msg: '#166534' },
  error:   { bg: '#fef2f2', border: '#dc2626', icon: '#dc2626', title: '#7f1d1d', msg: '#991b1b' },
  info:    { bg: '#eff6ff', border: '#2563eb', icon: '#2563eb', title: '#1e3a5f', msg: '#1d4ed8' },
  warning: { bg: '#fffbeb', border: '#d97706', icon: '#d97706', title: '#78350f', msg: '#92400e' },
};

const ICON: Record<string, string> = {
  success: 'check-circle',
  error:   'times-circle',
  info:    'info-circle',
  warning: 'exclamation-triangle',
};

// ─── Komponen Toast ──────────────────────────────────────────────
type ToastVariant = 'success' | 'error' | 'info' | 'warning';

interface Tab2ToastProps {
  text1?: string;
  text2?: string;
  variant: ToastVariant;
}

const Tab2ToastComponent = ({ text1, text2, variant }: Tab2ToastProps) => {
  const c = COLOR[variant];
  return (
    <View style={[styles.container, { backgroundColor: c.bg, borderLeftColor: c.border }]}>
      <View style={[styles.iconWrapper, { backgroundColor: c.border + '18' }]}>
        <FontAwesome name={ICON[variant] as any} size={20} color={c.icon} />
      </View>
      <View style={styles.textWrapper}>
        {text1 ? <Text style={[styles.title, { color: c.title }]} numberOfLines={1}>{text1}</Text> : null}
        {text2 ? <Text style={[styles.message, { color: c.msg }]} numberOfLines={2}>{text2}</Text> : null}
      </View>
    </View>
  );
};

// ─── Toast Config (pasang di RootLayoutNav / App root) ───────────
export const toastConfig: ToastConfig = {
  success: ({ text1, text2 }) => <Tab2ToastComponent text1={text1} text2={text2} variant="success" />,
  error:   ({ text1, text2 }) => <Tab2ToastComponent text1={text1} text2={text2} variant="error" />,
  info:    ({ text1, text2 }) => <Tab2ToastComponent text1={text1} text2={text2} variant="info" />,
  warning: ({ text1, text2 }) => <Tab2ToastComponent text1={text1} text2={text2} variant="warning" />,
};

// ─── Styles ──────────────────────────────────────────────────────
const styles = StyleSheet.create({
  container: {
    width: '92%',
    flexDirection: 'row',
    alignItems: 'center',
    borderRadius: 14,
    borderLeftWidth: 4,
    paddingVertical: 13,
    paddingHorizontal: 14,
    marginTop: 8,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.10,
    shadowRadius: 10,
    elevation: 6,
  },
  iconWrapper: {
    width: 38,
    height: 38,
    borderRadius: 19,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  textWrapper: {
    flex: 1,
  },
  title: {
    fontSize: 14,
    fontWeight: '700',
    marginBottom: 2,
  },
  message: {
    fontSize: 13,
    fontWeight: '500',
    lineHeight: 18,
  },
});