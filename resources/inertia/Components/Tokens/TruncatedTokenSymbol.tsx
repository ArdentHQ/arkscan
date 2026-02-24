import { useMemo } from "react";
import TruncateDynamic from "../General/TruncateDynamic";
import TokenSymbol from "./TokenSymbol";

export default function TruncatedTokenSymbol({ tokenSymbol, fullTokenSymbol, className }: { tokenSymbol: string; fullTokenSymbol?: string; className?: string }) {
    const isTruncated = useMemo(() => {
        return tokenSymbol.endsWith("...") || tokenSymbol.endsWith("…");
    }, [tokenSymbol]);

    return isTruncated ? (
        <TokenSymbol tokenSymbol={tokenSymbol} fullTokenSymbol={fullTokenSymbol} addSpace={false} />
    ) : (
        <TruncateDynamic value={tokenSymbol} location="end" className={className} />
    );
}
