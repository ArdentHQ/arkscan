export default function TruncateMiddle({
    length = 10,
    children,
}: {
    length?: number;
    children: React.ReactNode;
}) {
    const text = children as string;
    const maxLength = length;

    if (text.length <= maxLength) {
        return <span>{text}</span>;
    }

    const partLength = Math.floor((maxLength) / 2);

    const start = text.slice(0, partLength);
    const end = text.slice(-partLength);

    return <span>{start}…{end}</span>;
}
