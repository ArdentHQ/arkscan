export default function Number({
    children,
    ...props
}: React.HTMLAttributes<HTMLSpanElement> & { children: string | number }) {
    const formattedNumber = new Intl.NumberFormat("en-US", {
        style: "decimal",
        maximumFractionDigits: 2,
    }).format(parseFloat(children as string));

    return <span {...props}>{formattedNumber}</span>;
}
