export default function TruncateMiddle({
    length = 10,
    children,
    className,
}: {
    length?: number;
    children: React.ReactNode;
    className?: string;
}) {
    const text = children as string;
    const maxLength = length;

    if (text.length <= maxLength) {
        return <span className={className}>{text}</span>;
    }

    const partLength = Math.floor(maxLength / 2);

    const start = text.slice(0, partLength);
    const end = text.slice(-partLength);

    return (
        <span className={className}>
            {start}…{end}
        </span>
    );
}
