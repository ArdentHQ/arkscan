import classNames from "@/utils/class-names";
import ChevronDownSmall from "@/Assets/Icons/ChevronDownSmall";

export default function DropdownArrow({
    isOpen = false,
    color = 'text-theme-secondary-700 dark:text-theme-dark-200',
    onClick = () => {},
}: {
    isOpen?: boolean;
    color?: string;
    onClick?: () => void;
}) {
    return (
        <div
            className={classNames({
                "transition-default": true,
                "rotate-180": isOpen,
                [color]: true,
            })}
            onClick={onClick}
        >
            <ChevronDownSmall className="w-3 h-3" />
        </div>
    );
}
