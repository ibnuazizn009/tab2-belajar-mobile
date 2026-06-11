import Toast from 'react-native-toast-message';

type ToastType = 'success' | 'error' | 'info' | 'warning';

interface ToastOptions {
  title: string;
  message?: string;
  duration?: number;
  onHide?: () => void;
}

const show = (type: ToastType, options: ToastOptions) => {
  Toast.show({
    type,
    text1: options.title,
    text2: options.message,
    visibilityTime: options.duration ?? 3000,
    position: 'top',
    onHide: options.onHide,
  });
};

export const tab2Toast = {
  success: (title: string, message?: string, opts?: Partial<ToastOptions>) =>
    show('success', { title, message, ...opts }),

  error: (title: string, message?: string, opts?: Partial<ToastOptions>) =>
    show('error', { title, message, ...opts }),

  info: (title: string, message?: string, opts?: Partial<ToastOptions>) =>
    show('info', { title, message, ...opts }),

  warning: (title: string, message?: string, opts?: Partial<ToastOptions>) =>
    show('warning', { title, message, ...opts }),

  // Shorthand untuk kasus login berhasil dengan callback onHide
  loginSuccess: (nama: string, onHide: () => void) =>
    show('success', {
      title: 'Login Berhasil',
      message: `Selamat datang, ${nama}`,
      duration: 2000,
      onHide,
    }),
};