import Cross from "@/Assets/Icons/Cross";
import Clipboard from "@/Components/General/Clipboard";
import TruncateDynamic from "@/Components/General/TruncateDynamic";
import { useEffect, useRef, useState } from "react";
import { useTranslation } from "react-i18next";

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
            className="flex-1 ml-2 w-full"
            onKeyDown={(e) => {
                if (e.key === 'Escape') {
                    setModalVisible(false);
                }
            }}
        >
            <button
                type="button"
                onClick={() => {
                    setModalVisible(!modalVisible);
                    if (modalVisible && ! hasBeenOpened) {
                        sa_event(`wallet_modal_${id}_opened`);

                        setHasBeenOpened(true);
                    }
                }}
                className="p-2 w-full focus-visible:ring-inset button button-secondary button-icon"
            >
                {button}
            </button>

            {modalVisible && (
                <div
                    ref={popupRef}
                    className="flex absolute right-0 left-0 justify-between items-end p-6 mx-8 mt-4 space-x-4 w-auto bg-white rounded-xl border border-transparent shadow-lg md:mt-1 lg:mr-32 z-15 md-lg:left-auto dark:shadow-lg-dark dark:bg-theme-dark-900 dark:border-theme-dark-800"
                >
                    <div className="flex flex-col space-y-2 min-w-0 leading-tight">
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
                            className="flex items-center p-2 w-full h-auto group"
                            wrapperClass="flex-1"
                            tooltipContent={t('pages.wallet.copied_public_key')}
                            withCheckmarks
                            checkmarksClass="group-hover:text-white text-theme-primary-900 dark:text-theme-dark-200"
                        />

                        {additionalButtons}

                        <button
                            type="button"
                            className="p-2 hover:text-white button button-generic dark:hover:text-white dark:text-theme-dark-500 hover:bg-theme-primary-700"
                            onClick={() => setModalVisible(false)}
                        >
                            <Cross className="h-4 w-4" />
                        </button>
                    </div>
                </div>
            )}
        </div>
    )
}
