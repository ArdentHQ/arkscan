import classNames from "@/utils/class-names";

export default function Card({ children, className = '' }: React.PropsWithChildren<{
    className?: string;
}>) {
    return (
        <div className={classNames({
            "rounded md:rounded-xl px-4 py-3 md:px-6 md:py-4 border border-theme-secondary-300 dark:border-theme-dark-700": true,
            [className]: true,
        })}>
            {children}
        </div>
    )
}
