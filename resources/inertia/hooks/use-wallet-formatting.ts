import useSharedData from "@/hooks/use-shared-data";
import { toFloat } from "@/utils/big-number";
import { formatWithCurrencyCustom, networkCurrency, currency as formatCurrency } from "@/utils/number-formatter";

export default function useWalletFormatting(balance: string) {
    const { network, settings, priceTickerData } = useSharedData();
    const balanceFloat = toFloat(balance);
    const rate = priceTickerData?.priceExchangeRate ?? 0;

    return {
        balanceFloat,
        formattedBalanceTwoDecimals: formatWithCurrencyCustom(balanceFloat, network.currency, 2),
        formattedBalanceFull: formatWithCurrencyCustom(balanceFloat, network.currency, null),
        formattedBalanceFullWithoutSuffix: networkCurrency(balanceFloat, 8, false),
        fiatValue: formatCurrency(balanceFloat * rate, settings!.currency),
    };
}
