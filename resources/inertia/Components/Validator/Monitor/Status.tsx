import Badge from '@/Components/General/Badge';
import classNames from "@/utils/class-names";
import TimeToForge from "./TimeToForge";
import { useTranslation } from "react-i18next";

export default function Status({
    width = 'min-w-[8.75rem]',
    withTime = false,
    withText = true,
    validator,
    className = '',
}: {
    width?: string;
    withTime?: boolean;
    withText?: boolean;
    validator: any;
    className?: string;
}) {
    const { t } = useTranslation();

    const wallet = validator.wallet;

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
            [className]: true,
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
                                <TimeToForge
                                    forgingAt={validator.forgingAt}
                                    wallet={validator.wallet}
                                    className="text-xs font-semibold leading-3.75"
                                />
                            ) : (
                                <span>{t('tables.validator-monitor.forging-status.pending')}</span>
                            )}
                        </>
                    )}

                    {wallet.hasForged && <span>{t('tables.validator-monitor.forging-status.block_generated')}</span>}
                    {wallet.justMissed && <span>{t('tables.validator-monitor.forging-status.blocks_missed', { count: wallet.missedCount })}</span>}

                    {!wallet.isPending && !wallet.hasForged && !wallet.justMissed && (
                        <span>{t('tables.validator-monitor.forging-status.generating')}</span>
                    )}
                </div>
            )}
        </Badge>
    );
}
