import useSharedData from "@/hooks/use-shared-data";
import useSettings from "@/Providers/Settings/useSettings";
import { toFloat, toArkString } from "@/utils/big-number";
import { formatWithCurrencyCustom, networkCurrency, currency as formatCurrency } from "@/utils/number-formatter";

export default function useWalletFormatting(balance: string) {
    const { network } = useSharedData();
    const { currency: selectedCurrency, priceExchangeRate } = useSettings();
    const balanceStr = toArkString(balance);
    const balanceFloat = toFloat(balance);
    const rate = priceExchangeRate ?? 0;

    return {
        balanceFloat,
        formattedBalanceTwoDecimals: formatWithCurrencyCustom(balanceStr, network.currency, 2),
        formattedBalanceFull: formatWithCurrencyCustom(balanceStr, network.currency, null),
        formattedBalanceFullWithoutSuffix: networkCurrency(balanceStr, 8, false),
        fiatValue: formatCurrency(balanceFloat * rate, selectedCurrency),
    };
}
