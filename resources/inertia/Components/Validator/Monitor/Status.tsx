import Badge from '@/Components/General/Badge';
import classNames from "@/utils/class-names";

export default function Status({ width = 'min-w-[8.75rem]', withTime = false, withText = true, wallet }) {
    return (
        <Badge colors={classNames({
            'inline-flex space-x-2 items-center whitespace-nowrap': true,
            '!px-2': withText,
            'border-transparent bg-theme-secondary-200 dark:border-theme-dark-700 encapsulated-badge': withText && wallet.isPending,
            'border-transparent bg-theme-success-100 dark:border-theme-success-700': withText && wallet.hasForged,
            'border-transparent bg-theme-danger-100 dark:border-theme-danger-400': withText && wallet.justMissed,
            'border-transparent bg-theme-primary-100 dark:border-theme-dark-blue-600 dim:border-theme-dark-blue-800': withText && ! wallet.isPending && ! wallet.hasForged && ! wallet.justMissed,
            'border-none': ! withText,
            [width]: withText,
        })}>
            <div className="flex items-center">
                <div className={classNames({
                    'w-3 h-3 rounded-full': true,
                    'bg-theme-secondary-500 dark:bg-theme-dark-500': wallet.isPending,
                    'bg-theme-success-700 dark:bg-theme-success-500': wallet.hasForged,
                    'bg-theme-danger-600 dark:bg-theme-danger-300': wallet.justMissed,
                    'bg-theme-primary-600 dark:bg-theme-dark-blue-400 dim:bg-theme-dark-blue-600': ! wallet.isPending && ! wallet.hasForged && ! wallet.justMissed,
                })}></div>
            </div>

            {withText && (
                <div className={classNames({
                    'leading-3.75': true,
                    'text-theme-secondary-700 dark:text-theme-dark-200': wallet.isPending,
                    'text-theme-success-700 dark:text-theme-success-500': wallet.hasForged,
                    'text-theme-danger-600 dark:text-theme-danger-300': wallet.justMissed,
                    'text-theme-primary-600 dark:text-theme-dark-blue-400 dim:text-theme-dark-blue-600': ! wallet.isPending && ! wallet.hasForged && ! wallet.justMissed,
                })}>
                    {wallet.isPending && (
                        <>
                            {withTime ? (
                                <span>TTF</span>
                            ) : (
                                <span>Pending</span>
                            )}
                        </>
                    )}

                    {wallet.hasForged && <span>Block Generated</span>}
                    {wallet.justMissed && <span>{wallet.missedCount} Blocks Missed</span>}

                    {!wallet.isPending && !wallet.hasForged && !wallet.justMissed && (
                        <span>Generating ...</span>
                    )}
                </div>
            )}
        </Badge>
    );
}
