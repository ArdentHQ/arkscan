import {
    createContext,
    useContext,
    useState,
    type ComponentProps,
    type ComponentPropsWithoutRef,
    type HTMLAttributes,
    type ReactNode,
} from "react";
import * as PopoverPrimitive from "@radix-ui/react-popover";
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

type PopoverRootProps = Omit<ComponentProps<typeof PopoverPrimitive.Root>, "open" | "defaultOpen" | "onOpenChange">;

interface MultiSelectRootProps extends HTMLAttributes<HTMLDivElement> {
    value: string[];
    onValueChange: (values: string[]) => void;
    defaultOpen?: boolean;
    popoverProps?: PopoverRootProps;
    className?: string;
}

const MultiSelectRoot = ({
    value,
    onValueChange,
    defaultOpen = false,
    className,
    popoverProps,
    children,
    ...containerProps
}: MultiSelectRootProps) => {
    const [isOpen, setIsOpen] = useState(defaultOpen);

    const handleToggleValue = (itemValue: string) => {
        const newValues = value.includes(itemValue) ? value.filter((v) => v !== itemValue) : [...value, itemValue];
        onValueChange(newValues);
    };

    const handleSetSelectedValues = (newValues: string[]) => {
        onValueChange(newValues);
    };

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
            <PopoverPrimitive.Root open={isOpen} onOpenChange={setIsOpen} {...popoverProps}>
                <div className={twMerge("relative", className)} {...containerProps}>
                    {children}
                </div>
            </PopoverPrimitive.Root>
        </MultiSelectContext.Provider>
    );
};

interface MultiSelectTriggerProps extends ComponentPropsWithoutRef<"button"> {
    placeholder?: string;
}

const MultiSelectTrigger = ({ placeholder, children, className, ...props }: MultiSelectTriggerProps) => {
    const context = useContext(MultiSelectContext);

    if (!context) {
        throw new Error("MultiSelect.Trigger must be used within MultiSelect");
    }

    return (
        <PopoverPrimitive.Trigger asChild>
            <button
                type="button"
                data-state={context.isOpen ? "open" : "closed"}
                className={twMerge(
                    "transition-default group flex h-11 w-full items-center justify-between rounded border border-theme-secondary-400 px-4 py-3.5 outline outline-1 outline-offset-0 outline-transparent focus:outline-none dark:border-theme-dark-500",
                    "text-theme-secondary-900 dark:text-theme-dark-200",
                    "hover:border-theme-primary-400 hover:outline-theme-primary-400 hover:dark:border-theme-dark-blue-600 hover:dark:outline-theme-dark-blue-600",
                    "data-[state=open]:border-theme-primary-400 data-[state=open]:outline-theme-primary-400 data-[state=open]:dark:border-theme-dark-blue-600 data-[state=open]:dark:outline-theme-dark-blue-600",
                    className,
                )}
                {...props}
            >
                {children || <span className="text-theme-secondary-700 dark:text-theme-dark-200">{placeholder}</span>}

                <span className="transition-default group-data-[state=open]:rotate-180">
                    <ChevronDownIcon className="h-3 w-3 text-theme-secondary-700 dark:text-theme-dark-200" />
                </span>
            </button>
        </PopoverPrimitive.Trigger>
    );
};

type MultiSelectContentProps = ComponentProps<typeof PopoverPrimitive.Content>;

const MultiSelectContent = ({ children, className, ...props }: MultiSelectContentProps) => {
    const context = useContext(MultiSelectContext);

    if (!context) {
        throw new Error("MultiSelect.Content must be used within MultiSelect");
    }

    return (
        <PopoverPrimitive.Portal>
            <PopoverPrimitive.Content
                className={twMerge(
                    "z-50 w-[var(--radix-popover-trigger-width)] overflow-hidden rounded-xl border border-white bg-white px-1 py-[0.125rem] shadow-lg dark:border-theme-dark-700 dark:bg-theme-dark-900",
                    "data-[state=closed]:animate-select-out data-[state=open]:animate-opacity-in",
                    className,
                )}
                align="start"
                sideOffset={8}
                {...props}
            >
                <div className="custom-scroll flex flex-col overflow-y-auto overscroll-contain">{children}</div>
            </PopoverPrimitive.Content>
        </PopoverPrimitive.Portal>
    );
};

interface MultiSelectItemProps extends HTMLAttributes<HTMLDivElement> {
    value: string;
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
                "transition-default group my-[0.125rem] flex cursor-pointer items-center rounded-lg px-5 py-[0.875rem] font-semibold leading-5 outline-none",
                "text-theme-secondary-700 dark:text-theme-dark-200",
                "hover:bg-theme-secondary-200 hover:dark:bg-theme-dark-950",
                isChecked &&
                    "bg-theme-secondary-200 text-theme-primary-600 dark:bg-theme-dark-950 dark:text-theme-dark-50",
                !isChecked && "hover:text-theme-secondary-900 hover:dark:text-theme-dark-50",
                className,
            )}
            onClick={() => context.onValueChange(value)}
            {...props}
        >
            <Checkbox.Input
                checked={isChecked}
                onCheckedChange={() => context.onValueChange(value)}
                onClick={(event) => event.stopPropagation()}
                className="checked:border-theme-primary-600 checked:bg-theme-primary-600 hover:checked:bg-theme-primary-700 group-hover:border-theme-primary-600 group-hover:checked:bg-theme-primary-700 dim:checked:border-theme-dark-blue-500 dim:checked:bg-theme-dark-blue-500 dim:hover:checked:bg-theme-dark-blue-600 dark:checked:border-theme-dark-blue-500 dark:checked:bg-theme-dark-blue-500 dark:hover:checked:bg-theme-dark-blue-600"
            />

            <span className="flex-1 px-3">{children}</span>

            {isChecked && <DoubleCheckMarkIcon className="h-4 w-4 text-theme-primary-600 dark:text-theme-dark-50" />}
        </div>
    );
};

interface MultiSelectAllItemProps extends HTMLAttributes<HTMLDivElement> {
    allValues: string[];
    children: ReactNode;
}

const MultiSelectAllItem = ({ allValues, children, className, ...props }: MultiSelectAllItemProps) => {
    const context = useContext(MultiSelectContext);

    if (!context) {
        throw new Error("MultiSelect.AllItem must be used within MultiSelect");
    }

    const allSelected = allValues.length > 0 && allValues.every((value) => context.selectedValues.includes(value));

    const handleToggleAll = () => {
        if (allSelected) {
            const newValues = context.selectedValues.filter((value) => !allValues.includes(value));
            context.setSelectedValues(newValues);
        } else {
            const newValues = Array.from(new Set([...context.selectedValues, ...allValues]));
            context.setSelectedValues(newValues);
        }
    };

    return (
        <div
            className={twMerge(
                "transition-default group my-[0.125rem] flex cursor-pointer items-center rounded-lg px-5 py-[0.875rem] font-semibold leading-5 outline-none",
                "text-theme-secondary-700 dark:text-theme-dark-200",
                "hover:bg-theme-secondary-200 hover:dark:bg-theme-dark-950",
                allSelected &&
                    "bg-theme-secondary-200 text-theme-primary-600 dark:bg-theme-dark-950 dark:text-theme-dark-50",
                !allSelected && "hover:text-theme-secondary-900 hover:dark:text-theme-dark-50",
                className,
            )}
            onClick={handleToggleAll}
            {...props}
        >
            <Checkbox.Input
                checked={allSelected}
                onCheckedChange={handleToggleAll}
                onClick={(event) => event.stopPropagation()}
                className="checked:border-theme-primary-600 checked:bg-theme-primary-600 hover:checked:bg-theme-primary-700 group-hover:border-theme-primary-600 group-hover:checked:bg-theme-primary-700 dim:checked:border-theme-dark-blue-500 dim:checked:bg-theme-dark-blue-500 dim:hover:checked:bg-theme-dark-blue-600 dark:checked:border-theme-dark-blue-500 dark:checked:bg-theme-dark-blue-500 dark:hover:checked:bg-theme-dark-blue-600"
            />

            <span className="flex-1 px-3">{children}</span>

            {allSelected && <DoubleCheckMarkIcon className="h-4 w-4 text-theme-primary-600 dark:text-theme-dark-50" />}
        </div>
    );
};

const MultiSelectSeparator = ({ className, ...props }: HTMLAttributes<HTMLDivElement>) => {
    return (
        <div
            className={twMerge("my-0.5 border-t border-theme-secondary-300 dark:border-theme-dark-500", className)}
            {...props}
        />
    );
};

const MultiSelect = Object.assign(MultiSelectRoot, {
    Trigger: MultiSelectTrigger,
    Content: MultiSelectContent,
    Item: MultiSelectItem,
    AllItem: MultiSelectAllItem,
    Separator: MultiSelectSeparator,
});

export default MultiSelect;
