import classNames from "classnames";
import { useState } from "react";
import { useTranslation } from "react-i18next";
import Clipboard from "@/Components/General/Clipboard";
import { TransactionPayload } from "@/Pages/Transaction.contracts";

type CodeBlockView = "default" | "utf-8" | "original";

const views: CodeBlockView[] = ["default", "utf-8", "original"];

export default function TransactionCodeBlock({ payload }: { payload: TransactionPayload }) {
    const { t } = useTranslation();
    const [view, setView] = useState<CodeBlockView>("default");

    const contentMap: Record<CodeBlockView, string> = {
        default: payload.formatted ?? "",
        "utf-8": payload.utf8 ?? "",
        original: payload.raw ?? "",
    };

    const content = contentMap[view];

    return (
        <div className="flex flex-col text-sm">
            <div className="bg-theme-secondary-900 text-theme-secondary-200 shadow-code-block dark:bg-theme-dark-800 dark:text-theme-dark-200 flex flex-col justify-between space-y-3 rounded-t-lg px-4! pt-3 sm:h-10 sm:flex-row sm:items-center sm:space-y-0 sm:pt-0">
                <div>{t("pages.transaction.input_data")}</div>

                <div className="flex h-full sm:items-center sm:justify-between">
                    {views.map((tab) => (
                        <button
                            key={tab}
                            type="button"
                            className={classNames(
                                "group/tab transition-default flex h-full items-center px-1.5 sm:first:pl-1.5",
                                {
                                    "text-theme-primary-500 dark:text-theme-dark-blue-500 cursor-default": view === tab,
                                    "text-theme-secondary-500 hover:text-theme-secondary-50 dark:text-theme-dark-200 dark:hover:text-theme-dark-50 cursor-pointer":
                                        view !== tab,
                                },
                            )}
                            onClick={() => setView(tab)}
                        >
                            <div
                                className={classNames(
                                    "transition-default border-theme-primary-500 flex h-full items-center border-b-2 sm:pb-0",
                                    {
                                        "border-theme-primary-500 dark:border-theme-dark-blue-500": view === tab,
                                        "group-hover/tab:border-theme-secondary-700 dark:group-hover/tab:border-theme-dark-500 border-transparent":
                                            view !== tab,
                                    },
                                )}
                            >
                                <div className="leading-3.75 sm:pt-[2px]">
                                    {t(`pages.transaction.code-block.tab.${tab}`)}
                                </div>
                            </div>
                        </button>
                    ))}
                </div>
            </div>

            <div className="code-block-custom-scroll shadow-code-block flex flex-1 overflow-x-auto rounded-b-lg bg-black p-4 text-[13px] font-normal text-[#C3B6FD]">
                <pre>{content}</pre>
            </div>

            <div className="inline-flex">
                <Clipboard
                    value={content}
                    noStyling
                    className="button button-secondary mt-4 flex h-8 w-full items-center justify-center space-x-2 px-4! text-base sm:w-auto"
                    checkmarksClass=""
                    tooltipContent={t("tooltips.copied")}
                >
                    <div>{t("pages.transaction.code-block.copy_code")}</div>
                </Clipboard>
            </div>
        </div>
    );
}
