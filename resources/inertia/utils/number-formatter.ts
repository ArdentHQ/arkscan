import { useConfig } from "@/Providers/Config/ConfigContext";

export function hasSymbol(currency: string): boolean {
    const { currencies } = useConfig();

    return currencies![currency]?.symbol !== null;
}
