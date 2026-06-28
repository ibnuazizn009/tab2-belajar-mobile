import React from "react";
import { View, Text, StyleSheet } from "react-native";
import { Stack } from "expo-router";
import { useSafeAreaInsets } from "react-native-safe-area-context";

interface Props {
  title: string;
  subtitle?: string;
}

export default function PageHeader({ title, subtitle }: Props) {
  const insets = useSafeAreaInsets();

  return (
    <>
      <Stack.Screen options={{ headerShown: false }} />

      <View
        style={[
          styles.container,
          {
            paddingTop: insets.top + 10,
          },
        ]}
      >
        <Text style={styles.title}>{title}</Text>

        {subtitle && <Text style={styles.subtitle}>{subtitle}</Text>}
      </View>
    </>
  );
}

const styles = StyleSheet.create({
  container: {
    backgroundColor: "#2563eb",

    paddingHorizontal: 20,

    paddingBottom: 26,

    alignItems: "center",

    shadowColor: "#2563eb",

    shadowOffset: {
      width: 0,
      height: 6,
    },

    shadowOpacity: 0.12,

    shadowRadius: 10,

    elevation: 4,
  },

  title: {
    color: "#fff",
    fontSize: 20,
    fontWeight: "700",
  },

  subtitle: {
    marginTop: 4,
    color: "#dbeafe",
    fontSize: 13,
  },
});
