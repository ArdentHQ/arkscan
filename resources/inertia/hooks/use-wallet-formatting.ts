import useSharedData from "@/hooks/use-shared-data";
import { toFloat, toArkString } from "@/utils/big-number";
import { formatWithCurrencyCustom, networkCurrency, currency as formatCurrency } from "@/utils/number-formatter";

export default function useWalletFormatting(balance: string) {
    const { network, settings, priceTickerData } = useSharedData();
    const balanceStr = toArkString(balance);
    const balanceFloat = toFloat(balance);
    const rate = priceTickerData?.priceExchangeRate ?? 0;

    return {
        balanceFloat,
        formattedBalanceTwoDecimals: formatWithCurrencyCustom(balanceStr, network.currency, 2),
        formattedBalanceFull: formatWithCurrencyCustom(balanceStr, network.currency, null),
        formattedBalanceFullWithoutSuffix: networkCurrency(balanceStr, 8, false),
        fiatValue: formatCurrency(balanceFloat * rate, settings!.currency),
    };
}
