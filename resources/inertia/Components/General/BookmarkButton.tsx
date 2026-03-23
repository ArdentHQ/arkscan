import { useTranslation } from "react-i18next";
import BookmarkIcon from "@icons/bookmark.svg?react";
import Tooltip from "./Tooltip";
import classNames from "classnames";
import { useBookmarks } from "@/Providers/Bookmarks/BookmarksContext";
import { BookmarkType } from "@/Providers/Bookmarks/types";

export default function BookmarkButton({
    type,
    id,
    variant = "button",
    testId,
    className,
    wrapperClassName,
}: {
    type: BookmarkType;
    id: string;
    variant?: "button" | "inline";
    testId?: string;
    className?: string;
    wrapperClassName?: string;
}) {
    const { isBookmarked, toggle } = useBookmarks();
    const { t } = useTranslation();
    const bookmarked = isBookmarked(type, id);

    const iconNotBookmarked =
        variant === "button"
            ? "text-theme-secondary-700 group-hover/bookmark:text-white dark:text-theme-dark-200 dark:group-hover/bookmark:text-white"
            : "text-theme-secondary-700 group-hover/bookmark:text-theme-secondary-900 dark:text-theme-dark-200 dark:group-hover/bookmark:text-white";

    const iconBookmarked =
        variant === "button"
            ? "fill-theme-primary-600 text-theme-primary-600 group-hover/bookmark:fill-none group-hover/bookmark:text-theme-primary-300 dark:fill-theme-dark-blue-500 dark:text-theme-dark-blue-500 dark:group-hover/bookmark:fill-none dark:group-hover/bookmark:text-theme-primary-300"
            : "fill-theme-primary-600 text-theme-primary-600 group-hover/bookmark:text-theme-primary-400 dark:fill-theme-dark-blue-500 dark:text-theme-dark-blue-500 dark:group-hover/bookmark:text-theme-dark-blue-300";

    return (
        <Tooltip
            content={bookmarked ? t("general.bookmarks.saved") : t("general.bookmarks.save")}
            className={classNames("flex", wrapperClassName)}
        >
            <button
                type="button"
                className={classNames(
                    "group/bookmark transition-default flex items-center justify-center focus-visible:ring-inset",
                    variant === "button" && [
                        "rounded border p-2",
                        bookmarked
                            ? "border-theme-primary-400 bg-theme-primary-100 hover:border-theme-primary-700 hover:bg-theme-primary-700 dim:border-theme-dark-600 dim:bg-theme-dark-800 dark:border-theme-dark-blue-800 dark:bg-theme-dark-blue-900 dark:hover:border-theme-primary-700 dark:hover:bg-theme-primary-700"
                            : "border-theme-secondary-300 bg-white hover:border-theme-primary-700 hover:bg-theme-primary-700 dark:border-theme-dark-700 dark:bg-theme-dark-900 dark:hover:border-theme-primary-700 dark:hover:bg-theme-primary-700",
                    ],
                    className,
                )}
                onClick={() => toggle(type, id)}
                data-testid={testId}
            >
                <BookmarkIcon
                    className={classNames(
                        "transition-default h-4 w-4",
                        bookmarked ? iconBookmarked : iconNotBookmarked,
                    )}
                />
            </button>
        </Tooltip>
    );
}
