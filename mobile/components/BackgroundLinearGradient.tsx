import Svg, { Defs, LinearGradient, Stop, Rect, Circle } from 'react-native-svg';
import { View, Text, StyleSheet } from "react-native";

export const MinimalBlueBackground = () => (
    <View style={StyleSheet.absoluteFill}>
      <Svg height="100%" width="100%">
        <Defs>
          <LinearGradient id="bgGradient" x1="0" y1="0" x2="0" y2="1">
            <Stop offset="0" stopColor="#eff6ff" stopOpacity="1" />
            <Stop offset="1" stopColor="#f8fafc" stopOpacity="1" />
          </LinearGradient>
        </Defs>
        <Rect width="100%" height="100%" fill="url(#bgGradient)" />
        <Circle cx="10%" cy="10%" r="120" fill="#dbeafe" opacity="0.4" />
        <Circle cx="90%" cy="90%" r="180" fill="#dbeafe" opacity="0.3" />
      </Svg>
    </View>
);