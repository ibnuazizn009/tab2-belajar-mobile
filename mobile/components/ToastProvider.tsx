// components/ToastProvider.tsx

import React from 'react'
import Toast, { BaseToast, ErrorToast, ToastConfig } from 'react-native-toast-message'

export const toastConfig: ToastConfig = {
  success: (props) => (
    <BaseToast
        {...props}
        style={[{ borderLeftColor: '#16a34a', height: 'auto', minHeight: 70, paddingVertical: 10, zIndex: 9999, elevation: 9999 }, props.props?.style]}
        contentContainerStyle={{ paddingHorizontal: 15 }}
        text1Style={{ fontSize: 16, fontWeight: 'bold', color: '#1e293b' }}
        text2Style={{ fontSize: 13, color: '#475569' }}
        text2NumberOfLines={2}
    />
  ),
  error: (props) => (
    <ErrorToast
        {...props}
        style={[{ borderLeftColor: '#dc2626', height: 'auto', minHeight: 70, paddingVertical: 10, zIndex: 9999, elevation: 9999 }, props.props?.style]}
        contentContainerStyle={{ paddingHorizontal: 15 }}
        text1Style={{ fontSize: 16, fontWeight: 'bold', color: '#1e293b' }}
        text2Style={{ fontSize: 13, color: '#475569' }}
        text2NumberOfLines={3}
    />
  ),
}

export const AppToast = ({ topOffset = 50 }: { topOffset?: number }) => (
  <Toast config={toastConfig} topOffset={topOffset} />
)