import classNames from '../../../utils/class-names';
export default function TableCell({ label, children, className = '' }: React.PropsWithChildren<{
    label?: string;
    className?: string;
}>) {
    return (
        <div className={classNames({
            "flex flex-col space-y-2 font-semibold leading-4.25": true,
            [className]: true,
        })}>
            {label && (
                <span className="dark:text-theme-dark-200">
                    {label}
                </span>
            )}
            <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                {children}
            </div>
        </div>
    );
}
