import { twMerge } from "tailwind-merge";
import CrossSmallIcon from "@ui/icons/cross-small.svg?react";

interface MultiSelectTagProps extends React.HTMLAttributes<HTMLButtonElement> {
    value: string;
    onRemove: (value: string) => void;
}

const MultiSelectTagsRoot = ({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) => {
    return <div className={twMerge("mt-3 flex flex-wrap items-center gap-3", className)} {...props} />;
};

const MultiSelectTag = ({ value, onRemove, className, children, ...props }: MultiSelectTagProps) => {
    return (
        <button
            type="button"
            onClick={() => onRemove(value)}
            className={twMerge(
                "inline-flex cursor-pointer items-center space-x-2 rounded border border-transparent p-2.5 text-sm font-semibold",
                "bg-theme-primary-100 text-theme-primary-600",
                "dark:border-theme-dark-600 dark:bg-theme-dark-800 dark:text-white",
                "hover:bg-theme-primary-700 hover:text-white",
                "hover:dark:border-theme-dark-blue-700 hover:dark:bg-theme-dark-blue-700",
                "transition-default group",
                className,
            )}
            {...props}
        >
            <span>{children}</span>

            <span
                className={twMerge(
                    "inline-block p-1 text-theme-secondary-700 dark:text-theme-dark-200",
                    "group-hover:text-white group-hover:dark:text-white",
                )}
            >
                <CrossSmallIcon className="fill-current h-2 w-2" />
            </span>
        </button>
    );
};

const MultiSelectTags = Object.assign(MultiSelectTagsRoot, {
    Tag: MultiSelectTag,
});

export default MultiSelectTags;
