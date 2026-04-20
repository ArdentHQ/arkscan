type PercentageProps = React.HTMLAttributes<HTMLSpanElement> & {
    children: string | number;
    decimals?: number;
};

export default function Percentage({ children, decimals = 2, ...props }: PercentageProps) {
    const value = typeof children === "number" ? children : parseFloat(children as string);

    const formattedNumber =
        typeof decimals === "number"
            ? `${value.toFixed(decimals)}%`
            : new Intl.NumberFormat("en-US", {
                  style: "percent",
              }).format(value);

    return <span {...props}>{formattedNumber}</span>;
}
