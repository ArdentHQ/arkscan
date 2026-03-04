import useSettings from "@/Providers/Settings/useSettings";
import { hasSymbol } from "@/utils/number-formatter";

export default function FiatValue({ value }: { value: number | string }) {
    const { currency } = useSettings();

    return (
        <span>
            <span>{value}</span>

            {hasSymbol(currency) && <span>{currency}</span>}
        </span>
    );
}
