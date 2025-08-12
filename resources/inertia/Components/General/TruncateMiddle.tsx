export default function TruncateMiddle({ length = 10, children }: React.PropsWithChildren<{
    length?: number;
}>) {
    const text = children as string;
    const maxLength = length;

    if (text.length <= maxLength) {
        return <span>{text}</span>;
    }

    const start = text.slice(0, 10);
    const end = text.slice(-10);

    return <span>{start}…{end}</span>;
}
