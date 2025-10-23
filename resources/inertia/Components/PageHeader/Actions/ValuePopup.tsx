import Clipboard from "@/Components/General/Clipboard";
import TruncateDynamic from "@/Components/General/TruncateDynamic";
import { useEffect, useRef, useState } from "react";
import { useTranslation } from "react-i18next";
import CrossIcon from "@ui/icons/cross.svg?react";

export default function PageHeaderValuePopup({
    value,
    button,
    title,
    id,
    additionalButtons,
}: {
    value: string;
    button: React.ReactNode;
    title: string;
    id: string;
    additionalButtons?: React.ReactNode;
}) {
    const { t } = useTranslation();

    const [modalVisible, setModalVisible] = useState(false);
    const [hasBeenOpened, setHasBeenOpened] = useState(false);
    const popupRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        function onOutsideClick(event: MouseEvent) {
            if (popupRef.current && !popupRef.current.contains(event.target as Node)) {
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
            className="ml-2 w-full flex-1"
            onKeyDown={(e) => {
                if (e.key === "Escape") {
                    setModalVisible(false);
                }
            }}
        >
            <button
                type="button"
                onClick={() => {
                    setModalVisible(!modalVisible);
                    if (modalVisible && !hasBeenOpened) {
                        (window as any).sa_event(`wallet_modal_${id}_opened`);

                        setHasBeenOpened(true);
                    }
                }}
                className="button button-secondary button-icon w-full p-2 focus-visible:ring-inset"
            >
                {button}
            </button>

            {modalVisible && (
                <div
                    ref={popupRef}
                    className="absolute left-0 right-0 z-15 mx-8 mt-4 flex w-auto items-end justify-between space-x-4 rounded-xl border border-transparent bg-white p-6 shadow-lg dark:border-theme-dark-800 dark:bg-theme-dark-900 dark:shadow-lg-dark md:mt-1 md-lg:left-auto lg:mr-32"
                >
                    <div className="flex min-w-0 flex-col space-y-2 leading-tight">
                        <span className="text-sm font-semibold text-theme-secondary-700 dark:text-theme-dark-500">
                            {title}
                        </span>

                        <span className="font-semibold text-theme-secondary-900 dark:text-theme-dark-200">
                            <TruncateDynamic value={value} />
                        </span>
                    </div>

                    <div className="flex items-center space-x-2">
                        <Clipboard
                            value={value}
                            className="group flex h-auto w-full items-center p-2"
                            wrapperClass="flex-1"
                            tooltipContent={t("pages.wallet.copied_public_key")}
                            withCheckmarks
                            checkmarksClass="group-hover:text-white text-theme-primary-900 dark:text-theme-dark-200"
                        />

                        {additionalButtons}

                        <button
                            type="button"
                            className="button button-generic p-2 hover:bg-theme-primary-700 hover:text-white dark:text-theme-dark-500 dark:hover:text-white"
                            onClick={() => setModalVisible(false)}
                        >
                            <CrossIcon className="h-4 w-4" />
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
