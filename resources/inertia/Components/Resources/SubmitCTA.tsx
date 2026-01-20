export default function SubmitCTA({
    title,
    subtitle,
    button,
    onClick,
}: {
    title: string;
    subtitle: string;
    button: string;
    onClick: () => void;
}) {
    return (
        <div className="mt-6 flex w-full flex-col items-center justify-between space-y-3 rounded-xl bg-theme-primary-100 px-6 py-6 text-center dark:bg-theme-dark-800 sm:flex-row sm:space-y-0 sm:py-2 sm:text-start">
            <span className="space-x-1 font-semibold text-theme-primary-900 dim:text-theme-dark-50 dark:text-white sm:text-lg">
                <span>{title}</span>

                <span className="whitespace-nowrap">{subtitle}</span>
            </span>

            <button type="button" className="button-primary w-full sm:w-auto" onClick={onClick}>
                {button}
            </button>
        </div>
    );
}
