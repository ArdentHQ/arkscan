import DoubleCheckMarkIcon from "@ui/icons/double-check-mark.svg?react";
import classNames from "@/utils/class-names";
import { Slot } from "@radix-ui/react-slot";

export default function DropdownItem({
    children,
    onClick,
    selected = false,
    disabled = false,
    asChild = false,
}: {
    children: React.ReactNode;
    onClick?: () => void;
    selected?: boolean;
    disabled?: boolean;
    asChild?: boolean;
}) {
    const Comp = asChild ? Slot : "div";

    return (
        <Comp
            className={classNames({
                "transition-default my-1 cursor-pointer rounded-lg px-5 py-[0.875rem] font-semibold leading-5": true,
                "bg-theme-secondary-200 text-theme-secondary-500 dark:bg-theme-secondary-900 dark:text-theme-dark-500":
                    disabled,
                "bg-theme-secondary-200 text-theme-primary-600 dark:bg-theme-dark-950 dark:text-theme-dark-50":
                    selected && !disabled,
                "border-transparent font-semibold text-theme-secondary-700 hover:bg-theme-secondary-200 hover:text-theme-secondary-900 dark:text-theme-dark-200 hover:dark:bg-theme-dark-950 hover:dark:text-theme-dark-50":
                    !selected && !disabled,
            })}
            onClick={onClick}
        >
            <div className="flex items-center justify-between">
                <span>{children}</span>

                {selected && (
                    <DoubleCheckMarkIcon className="ml-2 inline h-4 w-4 text-theme-primary-600 dark:text-theme-dark-50" />
                )}
            </div>
        </Comp>
    );
}
