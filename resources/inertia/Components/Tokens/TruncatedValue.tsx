import { useMemo } from "react";
import TruncateDynamic from "../General/TruncateDynamic";

export default function TruncatedValue({ value, className }: { value: string; className?: string }) {
    const isTruncated = useMemo(() => {
        return value.endsWith("...") || value.endsWith("…");
    }, [value]);

    return isTruncated ? (
        <div className={className}>{value}</div>
    ) : (
        <TruncateDynamic value={value} location="end" className={className} />
    );
}
