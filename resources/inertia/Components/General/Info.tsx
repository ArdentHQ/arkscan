import HintSmallIcon from "@ui/icons/hint-small.svg?react";
import QuestionMarkSmallIcon from "@ui/icons/question-mark-small.svg?react";
import classNames from "@/utils/class-names";
import Tippy from "@tippyjs/react";

export default function Info({
    tooltip,
    type = 'question',
    large = false,
    className = '',
}: {
    tooltip?: string;
    type?: 'question' | 'info';
    large?: boolean;
    className?: string;
}) {
    let iconOutput = (
        <HintSmallIcon className={classNames({
            'w-3 h-3': ! large,
            'w-4 h-4': large,
        })} />
    );

    if (type === 'question') {
        iconOutput = (
            <QuestionMarkSmallIcon className={classNames({
                'w-3 h-3': ! large,
                'w-4 h-4': large,
            })} />
        );
    }

    return (
        <>
            <div
                aria-label={tooltip}
                className={classNames({
                    "inline-block cursor-pointer transition-default rounded-full bg-theme-primary-100 text-theme-primary-600 dark:bg-theme-secondary-800 dark:text-theme-secondary-600 hover:text-white hover:bg-theme-primary-700 dark:hover:text-theme-secondary-800 dark:hover:bg-theme-secondary-600 outline-none focus-visible:ring-2 focus-visible:ring-theme-primary-500": true,
                    'p-1.5': large,
                    'p-1': ! large,
                    [className]: !! className,
                })}
            >
                {!! tooltip && (
                    <Tippy content={tooltip}>
                        <span>{iconOutput}</span>
                    </Tippy>
                )}

                {! tooltip && iconOutput}
            </div>
        </>
    )
}
