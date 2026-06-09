/**
 * tab2ApiService.ts
 * API Service untuk React Native + Expo
 */

import * as SecureStore from 'expo-secure-store'
import { jwtDecode } from 'jwt-decode'
import Toast from 'react-native-toast-message'

interface DecodedToken {
  exp: number
  [key: string]: any
}

type SnackbarType = 'success' | 'error' | 'info' | 'warning'

const showSnackbar = (message: string, type: SnackbarType) => {
  Toast.show({
    type: type === 'success' ? 'success' : 'error', 
    text1: type === 'success' ? 'Sukses' : 'Gagal',
    text2: message,
    position: 'top',
    visibilityTime: 3500,
    // Menambahkan style kustom agar bergeser ke kanan atas
    props: {
      style: {
        alignSelf: 'flex-end', // Menggeser komponen ke kanan
        marginRight: 20,
        marginTop: 10,
        width: '70%', 
      }
    }
  })
}

const showSnackbarNonMessage = (_type: SnackbarType) => {
  // Intentionally silent
}


const getAccessToken = async (): Promise<string | null> => {
  try {
    return await SecureStore.getItemAsync('access_token')
  } catch (error) {
    console.error('[SecureStore] Gagal membaca access_token:', error)
    return null
  }
}

let handleTokenExpired: (() => void) | null = null

export const setTokenExpiredHandler = (handler: () => void) => {
  handleTokenExpired = handler
}

const validateToken = async (): Promise<string | null> => {
  const accessToken = await getAccessToken()

  if (!accessToken) {
    if (handleTokenExpired) handleTokenExpired()
    return null
  }

  try {
    const decoded: DecodedToken = jwtDecode(accessToken)
    const currentTime = Math.floor(Date.now() / 1000)
    if (decoded.exp < currentTime) {
      if (handleTokenExpired) handleTokenExpired()
      return null
    }
  } catch (error) {
    console.error('Error decoding token:', error)
    if (handleTokenExpired) handleTokenExpired()
    return null
  }

  return accessToken
}

const extractErrorDetails = (responseData: any, fallback: string): string => {
  const errorMessage = responseData?.message || fallback
  if (responseData?.errors) {
    const fieldErrors = Object.values(responseData.errors).flat()
    return `${errorMessage} ${fieldErrors.join(', ')}`
  }
  return errorMessage
}

// ---------------------------------------------------------------------------
// tab2ApiService
// ---------------------------------------------------------------------------

export const tab2ApiService = {
  /**
   * POST — dengan auth & snackbar
   * Mendukung JSON body maupun FormData
   */
  post: async (
    url: string,
    data: Record<string, any> | FormData,
    postType: string,
    showErrorSnackbar: boolean = true
  ): Promise<any> => {
    const accessToken = await validateToken()
    if (!accessToken) return null

    try {
      const isFormData = data instanceof FormData

      const response = await fetch(url, {
        method: 'POST',
        headers: isFormData
          ? { Authorization: `Bearer ${accessToken}` }
          : {
              'Content-Type': 'application/json',
              Accept: 'application/json',
              Authorization: `Bearer ${accessToken}`
            },
        body: isFormData ? data : JSON.stringify(data)
      })

      const responseData = await response.json()

      if (response.ok) {
        showSnackbar(`Berhasil menyimpan ${postType}`, 'success')
        return { success: true, message: responseData }
      }

      if (response.status === 401) {
        if (handleTokenExpired) handleTokenExpired()
        return null
      }

      const errorDetails = extractErrorDetails(responseData, 'Gagal menyimpan data')
      if (showErrorSnackbar) {
        showSnackbar(errorDetails, 'error')
      }
      // throw new Error(`Error: ${response.status} - ${response.statusText}`)
      return { success: false, message: errorDetails }
    } catch (error) {
      console.error('Error post:', error)
      return undefined
    }
  },

  postPublic: async (
    url: string,
    data: Record<string, any>,
    postType: string
  ): Promise<any> => {
    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json'
        },
        body: JSON.stringify(data)
      })
  
      const responseData = await response.json()
  
      if (response.ok) {
        // 💡 UBAH DI SINI: Kembalikan responseData utuh agar token & data user dari Laravel tidak hilang
        return { success: true, data: responseData }
      }
  
      const errorDetails = responseData.message || 'Gagal menyimpan data'
      return { success: false, message: errorDetails }
  
    } catch (error) {
      console.error('Error postPublic:', error)
      return { success: false, message: 'Terjadi kesalahan jaringan' }
    }
  },
  

  /**
   * GET — dengan auth & snackbar
   */
  get: async (url: string, postType: string): Promise<any> => {
    const accessToken = await validateToken()
    if (!accessToken) return undefined

    try {
      const response = await fetch(url, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          Authorization: `Bearer ${accessToken}`
        }
      })

      const responseData = await response.json()

      if (response.ok) {
        return responseData
      }

      if (response.status === 401) {
        if (handleTokenExpired) handleTokenExpired()
        return undefined
      }

      const errorMessage = responseData?.message || 'Gagal mengambil data'
      showSnackbar(errorMessage, 'error')
      throw new Error(`Error: ${response.status} - ${response.statusText}`)
    } catch (error) {
      console.error('Error get:', error)
      throw error
    }
  },

  /**
   * POST dengan auth — tanpa snackbar (silent)
   */
  postNonMessage: async (
    url: string,
    data: Record<string, any>,
    postType: string
  ): Promise<any> => {
    const accessToken = await validateToken()
    if (!accessToken) return null

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          Authorization: `Bearer ${accessToken}`
        },
        body: JSON.stringify(data)
      })

      const responseData = await response.json()

      if (response.ok) {
        showSnackbarNonMessage('success')
        return responseData
      }

      if (response.status === 401) {
        if (handleTokenExpired) handleTokenExpired()
        return null
      }

      showSnackbarNonMessage('error')
      throw new Error(`Error: ${response.status} - ${response.statusText}`)
    } catch (error) {
      console.error('Error postNonMessage:', error)
      showSnackbarNonMessage('error')
      throw error
    }
  },

  /**
   * GET dengan auth — tanpa snackbar (silent)
   */
  getNonMessage: async (url: string, postType: string): Promise<any> => {
    const accessToken = await validateToken()
    if (!accessToken) return undefined

    try {
      const response = await fetch(url, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          Authorization: `Bearer ${accessToken}`
        }
      })

      const responseData = await response.json()

      if (response.ok) {
        showSnackbarNonMessage('success')
        return responseData
      }

      if (response.status === 401) {
        if (handleTokenExpired) handleTokenExpired()
        return undefined
      }

      showSnackbarNonMessage('error')
      throw new Error(`Error: ${response.status} - ${response.statusText}`)
    } catch (error) {
      console.error('Error getNonMessage:', error)
      showSnackbarNonMessage('error')
      throw error
    }
  },

  getNonMessageNoAuth: async (url: string, postType: string): Promise<any> => {
    try {
      const response = await fetch(url, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          // Authorization: `Bearer ${accessToken}`
        }
      })

      const responseData = await response.json()

      if (response.ok) {
        showSnackbarNonMessage('success')
        return responseData
      }

      if (response.status === 401) {
        if (handleTokenExpired) handleTokenExpired()
        return undefined
      }

      showSnackbarNonMessage('error')
      throw new Error(`Error: ${response.status} - ${response.statusText}`)
    } catch (error) {
      console.error('Error getNonMessage:', error)
      showSnackbarNonMessage('error')
      throw error
    }
  }
}