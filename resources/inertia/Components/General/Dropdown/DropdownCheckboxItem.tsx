import Checkbox from "@/Components/Input/Checkbox";
import classNames from "classnames";

export default function DropdownCheckboxItem({
    id,
    name,
    checked,
    children,
    onClick,
    disabled = false,
    className = "",
}: {
    id: string;
    name: string;
    checked: boolean;
    children: React.ReactNode;
    onClick: (checked: boolean) => void;
    disabled?: boolean;
    className?: string;
}) {
    return (
        <Checkbox
            id={id}
            name={name}
            className={classNames({
                "dropdown__checkbox transition-default my-[0.125rem] select-none rounded-lg px-5 font-semibold": true,
                "!dark:text-theme-dark-50 bg-theme-secondary-200 !text-theme-primary-600 dark:bg-theme-dark-950":
                    checked === true,
                "text-theme-secondary-700 hover:bg-theme-secondary-200 hover:text-theme-secondary-900 dark:text-theme-dark-200 hover:dark:bg-theme-dark-950 hover:dark:text-theme-dark-50":
                    checked === false,
                [className]: !!className,
            })}
            labelClasses={classNames({
                "w-full text-base block cursor-pointer py-[0.875rem] whitespace-nowrap": true,
                "text-theme-primary-600 dark:text-theme-dark-50": checked === true,
                "text-theme-secondary-700 dark:text-theme-dark-200 group-hover/filter-item:bg-theme-secondary-200 group-hover/filter-item:dark:bg-theme-dark-950 group-hover/filter-item:text-theme-secondary-900 group-hover/filter-item:dark:text-theme-dark-50":
                    !checked === false,
            })}
            label={children}
            onChange={onClick}
            checked={checked}
            disabled={disabled}
        />
    );
}
