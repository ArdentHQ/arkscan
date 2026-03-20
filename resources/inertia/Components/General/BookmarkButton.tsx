import { useState } from "react";
import { useTranslation } from "react-i18next";
import BookmarkIcon from "@icons/bookmark.svg?react";
import Tooltip from "./Tooltip";
import classNames from "classnames";

export default function BookmarkButton({ testId, className }: { testId?: string; className?: string }) {
    // TODO: replace with localStorage state from bookmarks provider
    const [isBookmarked, setIsBookmarked] = useState(false);
    const { t } = useTranslation();

    return (
        <Tooltip content={isBookmarked ? t("general.bookmarks.saved") : t("general.bookmarks.save")}>
            <button
                type="button"
                className={classNames(
                    "transition-default flex items-center justify-center rounded border p-2 focus-visible:ring-inset",
                    isBookmarked
                        ? "border-theme-primary-600 bg-theme-primary-600 text-white dark:border-theme-dark-blue-500 dark:bg-theme-dark-blue-500"
                        : "border-theme-secondary-300 bg-white text-theme-secondary-700 hover:border-theme-primary-700 hover:bg-theme-primary-700 hover:text-white dark:border-theme-dark-700 dark:bg-theme-dark-900 dark:text-theme-dark-200 dark:hover:border-theme-primary-700 dark:hover:bg-theme-primary-700",
                    className,
                )}
                onClick={() => setIsBookmarked(!isBookmarked)}
                data-testid={testId}
            >
                <BookmarkIcon
                    className={classNames("h-4 w-4", {
                        "fill-current": isBookmarked,
                    })}
                />
            </button>
        </Tooltip>
    );
}
