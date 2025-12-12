import { useDropdown } from "@/Providers/Dropdown/DropdownContext";
import Dropdown from "./Dropdown";
import CrossIcon from "@ui/icons/cross.svg?react";
import classNames from "classnames";

export default function DropdownPopup({
    title,
    children,
    button,
    onOpened,
    onClosed,
    dropdownClasses = "px-4",
    width = "min-w-[300px]",
    zIndex = 10,
    testId,
}: {
    title: string;
    children: React.ReactNode;
    button: React.ReactNode;
    onOpened?: () => void;
    onClosed?: () => void;
    dropdownClasses?: string;
    width?: string;
    zIndex?: number;
    testId?: string;
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
            onOpened={onOpened}
            onClosed={onClosed}
            zIndex={zIndex}
            testId={testId}
        >
            <div className="flex items-center justify-between px-6 py-[0.875rem] text-left text-lg font-semibold dark:text-theme-dark-50">
                <div className="text-lg text-theme-secondary-900 dark:text-theme-dark-200">{title}</div>

                <div>
                    <button
                        type="button"
                        className="button button-generic flex h-6 w-6 items-center justify-center p-0 hover:bg-theme-primary-700 hover:text-white dark:text-theme-dark-600 dark:hover:text-white"
                        onClick={() => {
                            setIsOpen(false);
                        }}
                        data-testid={testId ? `${testId}:close` : undefined}
                    >
                        <CrossIcon className="h-3 w-3" />
                    </button>
                </div>
            </div>

            <div className="border-t border-theme-secondary-300 px-6 pb-6 pt-[0.875rem] dark:border-theme-dark-700">
                {children}
            </div>
        </Dropdown>
    );
}
