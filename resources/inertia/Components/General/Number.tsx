export function formattedNumber(value: string | number): string {
    return new Intl.NumberFormat("en-US", {
        style: "decimal",
        maximumFractionDigits: 2,
    }).format(parseFloat(value as string));
}

export default function Number({
    children,
    ...props
}: React.HTMLAttributes<HTMLSpanElement> & { children: string | number }) {
    return <span {...props}>{formattedNumber(children)}</span>;
}
