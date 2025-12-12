import useSharedData from "@/hooks/use-shared-data";
import Currency from "./Currency";

export function NetworkCurrency({ value, decimals = 8 }: { value: string; decimals?: number }) {
    const { network } = useSharedData();

    return Currency({
        currency: network!.currency,
        decimals,
        value,
    });
}
