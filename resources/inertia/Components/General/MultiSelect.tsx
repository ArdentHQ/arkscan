import { createContext, useContext, useState, useRef, useEffect } from "react";
import { twMerge } from "tailwind-merge";
import ChevronDownIcon from "@ui/icons/arrows/chevron-down-small.svg?react";
import DoubleCheckMarkIcon from "@ui/icons/double-check-mark.svg?react";
import Checkbox from "./Checkbox";

interface MultiSelectContextType {
    selectedValues: string[];
    onValueChange: (value: string) => void;
    setSelectedValues: (values: string[]) => void;
    isOpen: boolean;
    setIsOpen: (open: boolean) => void;
}

const MultiSelectContext = createContext<MultiSelectContextType | undefined>(undefined);

// Root Component
interface MultiSelectRootProps {
    value: string[];
    onValueChange: (values: string[]) => void;
    children: React.ReactNode;
}

const MultiSelectRoot = ({ value, onValueChange, children }: MultiSelectRootProps) => {
    const [isOpen, setIsOpen] = useState(false);
    const containerRef = useRef<HTMLDivElement>(null);

    const handleToggleValue = (itemValue: string) => {
        const newValues = value.includes(itemValue) ? value.filter((v) => v !== itemValue) : [...value, itemValue];
        onValueChange(newValues);
    };

    const handleSetSelectedValues = (newValues: string[]) => {
        onValueChange(newValues);
    };

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
                setIsOpen(false);
            }
        };

        if (isOpen) {
            document.addEventListener("mousedown", handleClickOutside);
        }

        return () => {
            document.removeEventListener("mousedown", handleClickOutside);
        };
    }, [isOpen]);

    return (
        <MultiSelectContext.Provider
            value={{
                selectedValues: value,
                onValueChange: handleToggleValue,
                setSelectedValues: handleSetSelectedValues,
                isOpen,
                setIsOpen,
            }}
        >
            <div ref={containerRef} className="relative">
                {children}
            </div>
        </MultiSelectContext.Provider>
    );
};

// Trigger Component
interface MultiSelectTriggerProps extends React.HTMLAttributes<HTMLButtonElement> {
    placeholder?: string;
    children?: React.ReactNode;
}

const MultiSelectTrigger = ({ placeholder, children, className, ...props }: MultiSelectTriggerProps) => {
    const context = useContext(MultiSelectContext);

    if (!context) {
        throw new Error("MultiSelect.Trigger must be used within MultiSelect");
    }

    const isOpen = context.isOpen;

    return (
        <button
            type="button"
            className={twMerge(
                "transition-default group flex h-11 w-full items-center justify-between rounded border border-theme-secondary-400 px-4 py-3.5 outline outline-1 outline-offset-0 outline-transparent focus:outline-none",
                "text-theme-secondary-900 dark:border-theme-dark-500 dark:text-theme-dark-200",
                "hover:border-theme-primary-400 hover:outline-theme-primary-400 dark:border-theme-dark-500 hover:dark:border-theme-dark-blue-600 hover:dark:outline-theme-dark-blue-600",
                isOpen &&
                    "border-theme-primary-400 outline-theme-primary-400 dark:border-theme-dark-blue-600 dark:outline-theme-dark-blue-600",
                className,
            )}
            onClick={() => context.setIsOpen(!isOpen)}
            {...props}
        >
            {children || <span className="text-theme-secondary-700 dark:text-theme-dark-500">{placeholder}</span>}

            <span className={twMerge("transition-default", isOpen && "rotate-180")}>
                <ChevronDownIcon className="h-3 w-3 text-theme-secondary-700 dark:text-theme-dark-200" />
            </span>
        </button>
    );
};

// Content Component
interface MultiSelectContentProps extends React.HTMLAttributes<HTMLDivElement> {
    children: React.ReactNode;
}

const MultiSelectContent = ({ children, className, ...props }: MultiSelectContentProps) => {
    const context = useContext(MultiSelectContext);

    if (!context) {
        throw new Error("MultiSelect.Content must be used within MultiSelect");
    }

    if (!context.isOpen) {
        return null;
    }

    return (
        <div
            className={twMerge(
                "absolute top-full z-50 mt-2 w-full overflow-hidden rounded-xl border border-white bg-white px-1 py-[0.125rem] shadow-lg dark:border-theme-dark-700 dark:bg-theme-dark-900",
                "animate-opacity-in",
                className,
            )}
            {...props}
        >
            <div className="custom-scroll flex max-h-[300px] flex-col overflow-y-auto overscroll-contain">
                {children}
            </div>
        </div>
    );
};

// Item Component
interface MultiSelectItemProps extends React.HTMLAttributes<HTMLDivElement> {
    value: string;
    children: React.ReactNode;
}

const MultiSelectItem = ({ value, children, className, ...props }: MultiSelectItemProps) => {
    const context = useContext(MultiSelectContext);

    if (!context) {
        throw new Error("MultiSelect.Item must be used within MultiSelect");
    }

    const isChecked = context.selectedValues.includes(value);

    return (
        <div
            className={twMerge(
                "transition-default my-[0.125rem] flex cursor-pointer items-center rounded-lg px-5 py-[0.875rem] font-semibold leading-5 outline-none",
                "text-theme-secondary-700 dark:text-theme-dark-200",
                "hover:bg-theme-secondary-200 hover:text-theme-secondary-900 hover:dark:bg-theme-dark-950 hover:dark:text-theme-dark-50",
                isChecked &&
                    "bg-theme-secondary-200 text-theme-primary-600 dark:bg-theme-dark-950 dark:text-theme-dark-50",
                className,
            )}
            onClick={() => context.onValueChange(value)}
            {...props}
        >
            <Checkbox.Input
                checked={isChecked}
                onCheckedChange={() => context.onValueChange(value)}
                onClick={(e) => e.stopPropagation()}
                className="!h-5 !w-5"
            />

            <span className="flex-1 px-3">{children}</span>

            {isChecked && <DoubleCheckMarkIcon className="h-4 w-4 text-theme-primary-600 dark:text-theme-dark-50" />}
        </div>
    );
};

// AllItem Component
interface MultiSelectAllItemProps extends React.HTMLAttributes<HTMLDivElement> {
    allValues: string[];
    children: React.ReactNode;
}

const MultiSelectAllItem = ({ allValues, children, className, ...props }: MultiSelectAllItemProps) => {
    const context = useContext(MultiSelectContext);

    if (!context) {
        throw new Error("MultiSelect.AllItem must be used within MultiSelect");
    }

    const allSelected = allValues.length > 0 && allValues.every((v) => context.selectedValues.includes(v));

    const handleToggleAll = () => {
        if (allSelected) {
            // Deselect all values in allValues
            const newValues = context.selectedValues.filter((v) => !allValues.includes(v));
            context.setSelectedValues(newValues);
        } else {
            // Select all values
            const newValues = Array.from(new Set([...context.selectedValues, ...allValues]));
            context.setSelectedValues(newValues);
        }
    };

    return (
        <div
            className={twMerge(
                "transition-default my-[0.125rem] flex cursor-pointer items-center rounded-lg px-5 py-[0.875rem] font-semibold leading-5 outline-none",
                "text-theme-secondary-700 dark:text-theme-dark-200",
                "hover:bg-theme-secondary-200 hover:text-theme-secondary-900 hover:dark:bg-theme-dark-950 hover:dark:text-theme-dark-50",
                allSelected &&
                    "bg-theme-secondary-200 text-theme-primary-600 dark:bg-theme-dark-950 dark:text-theme-dark-50",
                className,
            )}
            onClick={handleToggleAll}
            {...props}
        >
            <Checkbox.Input
                checked={allSelected}
                onCheckedChange={handleToggleAll}
                onClick={(e) => e.stopPropagation()}
                className="!h-5 !w-5"
            />

            <span className="flex-1 px-3">{children}</span>

            {allSelected && <DoubleCheckMarkIcon className="h-4 w-4 text-theme-primary-600 dark:text-theme-dark-50" />}
        </div>
    );
};

// Separator Component
const MultiSelectSeparator = ({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) => {
    return (
        <div
            className={twMerge("my-0.5 border-t border-theme-secondary-300 dark:border-theme-dark-500", className)}
            {...props}
        />
    );
};

// Composed Component
const MultiSelect = Object.assign(MultiSelectRoot, {
    Trigger: MultiSelectTrigger,
    Content: MultiSelectContent,
    Item: MultiSelectItem,
    AllItem: MultiSelectAllItem,
    Separator: MultiSelectSeparator,
});

export default MultiSelect;
