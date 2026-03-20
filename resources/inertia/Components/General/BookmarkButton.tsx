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
                    "group/bookmark transition-default flex items-center justify-center rounded border p-2 focus-visible:ring-inset",
                    "border-theme-secondary-300 bg-white",
                    "dark:border-theme-dark-700 dark:bg-theme-dark-900",
                    className,
                )}
                onClick={() => setIsBookmarked(!isBookmarked)}
                data-testid={testId}
            >
                <BookmarkIcon
                    className={classNames(
                        "transition-default h-4 w-4",
                        isBookmarked
                            ? "fill-theme-primary-600 text-theme-primary-600 group-hover/bookmark:text-theme-primary-400 dark:fill-theme-dark-blue-500 dark:text-theme-dark-blue-500 dark:group-hover/bookmark:text-theme-dark-blue-300"
                            : "text-theme-secondary-700 group-hover/bookmark:text-theme-secondary-900 dark:text-theme-dark-200 dark:group-hover/bookmark:text-white",
                    )}
                />
            </button>
        </Tooltip>
    );
}
