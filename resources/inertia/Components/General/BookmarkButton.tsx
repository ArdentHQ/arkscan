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
                    "button button-icon p-2 focus-visible:ring-inset",
                    isBookmarked
                        ? "button-primary"
                        : "button-secondary text-theme-secondary-700 hover:text-white dark:text-theme-dark-200 dark:hover:text-white",
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
