import useSharedData from "@/hooks/use-shared-data";
import { hasSymbol } from "@/utils/number-formatter";

export default function FiatValue({ value }: { value: number | string }) {
    const { settings } = useSharedData();

    return (
        <span>
            <span>{value}</span>

            {hasSymbol(settings!.currency) && <span>{settings!.currency}</span>}
        </span>
    );
}
