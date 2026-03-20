import { useState } from "react";
import { useTranslation } from "react-i18next";
import BookmarkIcon from "@icons/bookmark.svg?react";
import Tooltip from "./Tooltip";
import classNames from "classnames";

export default function BookmarkButton({
    testId,
    className,
    wrapperClassName,
}: {
    testId?: string;
    className?: string;
    wrapperClassName?: string;
}) {
    // TODO: replace with localStorage state from bookmarks provider
    const [isBookmarked, setIsBookmarked] = useState(false);
    const { t } = useTranslation();

    return (
        <Tooltip
            content={isBookmarked ? t("general.bookmarks.saved") : t("general.bookmarks.save")}
            className={classNames("flex", wrapperClassName)}
        >
            <button
                type="button"
                className={classNames(
                    "group/bookmark transition-default flex items-center justify-center rounded border p-2 focus-visible:ring-inset",
                    isBookmarked
                        ? "border-theme-primary-400 bg-theme-primary-100 hover:border-theme-primary-700 hover:bg-theme-primary-700 dark:border-theme-dark-blue-400 dark:bg-theme-dark-800 dark:hover:border-theme-primary-700 dark:hover:bg-theme-primary-700 dim:border-theme-dark-600 dim:bg-theme-dark-800"
                        : "border-theme-secondary-300 bg-white hover:border-theme-primary-700 hover:bg-theme-primary-700 dark:border-theme-dark-700 dark:bg-theme-dark-900 dark:hover:border-theme-primary-700 dark:hover:bg-theme-primary-700",
                    className,
                )}
                onClick={() => setIsBookmarked(!isBookmarked)}
                data-testid={testId}
            >
                <BookmarkIcon
                    className={classNames(
                        "transition-default h-4 w-4",
                        isBookmarked
                            ? "fill-theme-primary-600 text-theme-primary-600 group-hover/bookmark:fill-none group-hover/bookmark:text-theme-primary-300 dark:fill-theme-dark-blue-500 dark:text-theme-dark-blue-500 dark:group-hover/bookmark:fill-none dark:group-hover/bookmark:text-theme-primary-300"
                            : "text-theme-secondary-700 group-hover/bookmark:text-white dark:text-theme-dark-200 dark:group-hover/bookmark:text-white",
                    )}
                />
            </button>
        </Tooltip>
    );
}
