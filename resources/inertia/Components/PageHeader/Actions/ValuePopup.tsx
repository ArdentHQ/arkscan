import Clipboard from "@/Components/General/Clipboard";
import TruncateDynamic from "@/Components/General/TruncateDynamic";
import { useEffect, useRef, useState } from "react";
import CrossIcon from "@ui/icons/cross.svg?react";

export default function PageHeaderValuePopup({
    value,
    button,
    title,
    id,
    additionalButtons,
    copiedTooltip,
    testId,
}: {
    value: string;
    button: React.ReactNode;
    title: string;
    id: string;
    additionalButtons?: React.ReactNode;
    copiedTooltip: string;
    testId?: string;
}) {
    const [modalVisible, setModalVisible] = useState(false);
    const popupRef = useRef<HTMLDivElement>(null);
    const buttonRef = useRef<HTMLButtonElement>(null);

    const hasTrackedOpen = useRef(false);

    useEffect(() => {
        function onOutsideClick(event: MouseEvent) {
            if (
                popupRef.current &&
                !popupRef.current.contains(event.target as Node) &&
                !buttonRef.current?.contains(event.target as Node)
            ) {
                setModalVisible(false);
            }
        }

        document.addEventListener("mousedown", onOutsideClick);

        return () => {
            document.removeEventListener("mousedown", onOutsideClick);
        };
    }, [popupRef]);

    return (
        <div
            className="w-full flex-1"
            onKeyDown={(e) => {
                if (e.key === "Escape") {
                    setModalVisible(false);
                }
            }}
            data-testid={testId}
        >
            <button
                ref={buttonRef}
                type="button"
                onClick={() => {
                    const isVisible = !modalVisible;
                    setModalVisible(isVisible);
                    if (isVisible && !hasTrackedOpen.current) {
                        window.sa_event(`wallet_modal_${id}_opened`);

                        hasTrackedOpen.current = true;
                    }
                }}
                className="button button-secondary button-icon w-full p-2! focus-visible:ring-inset"
            >
                {button}
            </button>

            {modalVisible && (
                <div
                    ref={popupRef}
                    className="dark:border-theme-dark-800 dark:bg-theme-dark-900 dark:shadow-lg-dark md-lg:left-auto absolute right-0 left-0 z-15 mx-8 mt-4 flex w-auto items-end justify-between space-x-4 rounded-xl border border-transparent bg-white p-6 shadow-lg md:mt-1 lg:mr-32"
                >
                    <div className="flex min-w-0 flex-col space-y-2 leading-tight">
                        <span className="text-theme-secondary-700 dark:text-theme-dark-500 text-sm font-semibold">
                            {title}
                        </span>

                        <span className="text-theme-secondary-900 dark:text-theme-dark-200 font-semibold">
                            <TruncateDynamic value={value} />
                        </span>
                    </div>

                    <div className="flex items-center space-x-2">
                        <Clipboard
                            value={value}
                            className="group flex h-auto w-full items-center p-2!"
                            wrapperClass="flex-1"
                            tooltipContent={copiedTooltip}
                            withCheckmarks
                            checkmarksClass="group-hover:text-white text-theme-primary-900 dark:text-theme-dark-200"
                            testId={testId ? `${testId}:clipboard` : undefined}
                        />

                        {additionalButtons}

                        <button
                            type="button"
                            className="button button-generic hover:bg-theme-primary-700 dark:text-theme-dark-500 p-2! hover:text-white dark:hover:text-white"
                            onClick={() => setModalVisible(false)}
                            data-testid={testId ? `${testId}:close` : undefined}
                        >
                            <CrossIcon className="h-4 w-4" />
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
