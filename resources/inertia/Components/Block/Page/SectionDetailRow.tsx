interface SectionDetailRowProps {
    title: string;
    value?: string | number | null;
    children?: React.ReactNode;
}

export default function SectionDetailRow({ title, value, children }: SectionDetailRowProps) {
    return (
        <div className="flex items-center space-x-4">
            <div className="w-[106px] whitespace-nowrap">{title}</div>

            <div className="text-theme-secondary-900 dark:text-theme-dark-50 flex-1 space-y-3 text-right sm:text-left">
                <span>{value ?? children}</span>
            </div>
        </div>
    );
}
