import { useContext } from "react";
import SettingsContext from "./SettingsContext";

export default function useSettings() {
    const context = useContext(SettingsContext);

    if (!context) {
        throw new Error("useSettings must be used within a SettingsProvider");
    }

    return context;
}
