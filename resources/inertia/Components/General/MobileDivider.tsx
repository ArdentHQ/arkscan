import classNames from "@/utils/class-names";

export default function MobileDivider({
    color = 'bg-theme-secondary-200 dark:bg-theme-dark-950 text-theme-secondary-200 dark:text-theme-dark-950',
    className = '',
}: {
    color?: string;
    className?: string;
}) {
    return (
        <hr className={classNames({
            "h-1 md:hidden": true,
            [color]: true,
            [className]: true,
         })} />
    );
}
