import classNames from "@/utils/class-names";

export default function Badge({ colors = 'border-transparent bg-theme-secondary-200 dark:border-theme-dark-700 dark:text-theme-dark-200', children }) {
    return (
        <div className={classNames({
            'text-xs font-semibold rounded border dark:bg-transparent px-[3px] py-[2px] leading-3.75 shrink-0': true,
            [colors]: true,
        })}>
            {children}
        </div>
    )
}
