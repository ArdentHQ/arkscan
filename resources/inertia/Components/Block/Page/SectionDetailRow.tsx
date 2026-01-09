interface SectionDetailRowProps {
    title: string;
    value?: string | number | null;
    children?: React.ReactNode;
}

export default function SectionDetailRow({ title, value, children }: SectionDetailRowProps) {
    return (
        <div className="flex items-center justify-between">
            <span className="text-theme-secondary-700 dark:text-theme-dark-200">{title}</span>
            <span className="text-theme-secondary-900 dark:text-theme-dark-50">{value ?? children}</span>
        </div>
    );
}
