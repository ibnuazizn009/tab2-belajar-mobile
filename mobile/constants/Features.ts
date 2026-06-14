export const CHECK_FEATURE = (paket: 'free' | 'middle' | 'golden' | undefined, featureName: 'WHATSAPP' | 'ADD_PETUGAS' | 'DOWNLOAD_REPORT' | 'IMPORT_EXCEL') => {
    if (!paket) return false;
  
    const Rules = {
      free: {
        WHATSAPP: false,
        ADD_PETUGAS: false, // Maksimal dibatasi dari info alert backend
        DOWNLOAD_REPORT: false,
        IMPORT_EXCEL: false,
      },
      middle: {
        WHATSAPP: true, // (Nanti dibatasi kuota harian oleh backend)
        ADD_PETUGAS: true,
        DOWNLOAD_REPORT: false, // Middle tidak bisa download excel/pdf
        IMPORT_EXCEL: false,
      },
      golden: {
        WHATSAPP: true,
        ADD_PETUGAS: true,
        DOWNLOAD_REPORT: true,
        IMPORT_EXCEL: true
      }
    };
  
    return Rules[paket]?.[featureName] ?? false;
  };