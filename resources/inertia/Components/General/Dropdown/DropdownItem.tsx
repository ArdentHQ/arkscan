import DoubleCheckMark from "@/Assets/Icons/DoubleCheckMark";
import classNames from "@/utils/class-names";

export default function DropdownItem({
    children,
    onClick,
    selected = false,
    disabled = false,
}: {
    children: React.ReactNode;
    onClick?: () => void;
    selected?: boolean;
    disabled?: boolean;
}) {
    return (
        <div
            className={classNames({
                'px-5 py-[0.875rem] my-1 font-semibold transition-default cursor-pointer leading-5 rounded-lg': true,
                'text-theme-secondary-500 bg-theme-secondary-200 dark:bg-theme-secondary-900 dark:text-theme-dark-500': disabled,
                'bg-theme-secondary-200 dark:bg-theme-dark-950 text-theme-primary-600 dark:text-theme-dark-50': selected && ! disabled,
                'font-semibold border-transparent text-theme-secondary-700 dark:text-theme-dark-200 hover:text-theme-secondary-900 hover:bg-theme-secondary-200 hover:dark:bg-theme-dark-950 hover:dark:text-theme-dark-50': ! selected && ! disabled,
            })}
            onClick={onClick}
        >
            <div className="flex justify-between items-center">
                <span>{children}</span>

                {selected && (
                    <DoubleCheckMark className="w-4 h-4 inline ml-2 text-theme-primary-600 dark:text-theme-dark-50" />
                )}
            </div>
        </div>
    );
}
