import { useDropdown } from "@/Providers/Dropdown/DropdownContext";
import Dropdown from "./Dropdown";
import CrossIcon from "@ui/icons/cross.svg?react";
import classNames from "classnames";
import { useTranslation } from "react-i18next";

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
    const { t } = useTranslation();

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
            <div className="dark:text-theme-dark-50 flex items-center justify-between px-6 py-[0.875rem] text-left text-lg font-semibold">
                <div className="text-theme-secondary-900 dark:text-theme-dark-200 text-lg">{title}</div>

                <div>
                    <button
                        type="button"
                        aria-label={t("actions.close")}
                        className="button button-generic hover:bg-theme-primary-700 dark:text-theme-dark-600 flex h-6 w-6 items-center justify-center p-0 hover:text-white dark:hover:text-white"
                        onClick={() => {
                            setIsOpen(false);

                            if (onClosed) {
                                setTimeout(() => onClosed(), 250);
                            }
                        }}
                        data-testid={testId ? `${testId}:close` : undefined}
                    >
                        <CrossIcon className="h-3 w-3" />
                    </button>
                </div>
            </div>

            <div className="border-theme-secondary-300 dark:border-theme-dark-700 border-t px-6 pt-[0.875rem] pb-6">
                {children}
            </div>
        </Dropdown>
    );
}
