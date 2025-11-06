import useConfig from "@/hooks/use-config";
import Currency from "./Currency";

export function NetworkCurrency({ value, decimals = 8 }: { value: string; decimals?: number }) {
    const { network } = useConfig();

    return Currency({
        currency: network!.currency,
        decimals,
        value,
    });
}
