import { useConfig } from "@/Providers/Config/ConfigContext";
import { hasSymbol } from "@/utils/number-formatter";

export default function FiatValue({ value }: { value: number | string }) {
    const { settings } = useConfig();

    return (
        <span>
            <span>{value}</span>

            {hasSymbol(settings!.currency) && (
                <span>{settings!.currency}</span>
            )}
        </span>
    );
}
