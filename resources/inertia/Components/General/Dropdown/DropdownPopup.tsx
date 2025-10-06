import { useDropdown } from "@/Providers/Dropdown/DropdownContext";
import Dropdown from "./Dropdown";
import Cross from "@/Assets/Icons/Cross";
import { Placement } from "@floating-ui/react";
import classNames from "@/utils/class-names";

export default function DropdownPopup({
    title,
    children,
    button,
    onClosed,
    dropdownClasses = 'px-4',
    width = 'min-w-[300px]',
}: {
    title: string;
    children: React.ReactNode;
    button: React.ReactNode;
    onClosed?: () => void;
    dropdownClasses?: string;
    width?: string;
}) {
    const { setIsOpen } = useDropdown();

    return (
        <Dropdown
            closeOnClick={false}
            buttonClass="bg-white rounded dark:bg-theme-dark-900"
            button={button}
            dropdownClasses={classNames({
                [dropdownClasses]: true,
                [width]: true,
            })}
            dropdownContentClasses="bg-white dark:bg-theme-dark-900 border border-white dark:border-theme-dark-700 rounded-xl shadow-lg dark:shadow-lg-dark"
            onClosed={onClosed}
        >
            <div className="flex justify-between items-center px-6 text-lg font-semibold text-left py-[0.875rem] dark:text-theme-dark-50">
                <div className="text-lg text-theme-secondary-900 dark:text-theme-dark-200">
                    {title}
                </div>

                <div>
                    <button
                        type="button"
                        className="flex justify-center items-center p-0 w-6 h-6 hover:text-white button button-generic dark:hover:text-white dark:text-theme-dark-600 hover:bg-theme-primary-700"
                        onClick={() => {
                            setIsOpen(false);
                        }}
                    >
                        <Cross className="h-3 w-3" />
                    </button>
                </div>
            </div>

            <div className="px-6 pb-6 border-t pt-[0.875rem] border-theme-secondary-300 dark:border-theme-dark-700">
                {children}
            </div>
        </Dropdown>
    );
}
