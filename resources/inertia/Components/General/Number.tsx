export default function Number({ children }: React.PropsWithChildren) {
    const formattedNumber = new Intl.NumberFormat('en-US', {
        style: 'decimal',
        maximumFractionDigits: 2,
    }).format(parseFloat(children as string));

    return (
        <span>
            {formattedNumber}
        </span>
    );
}
