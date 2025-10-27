import { createContext } from "react";
import * as SelectPrimitive from "@radix-ui/react-select";
import ChevronDownIcon from "@ui/icons/arrows/chevron-down-small.svg?react";
import { twMerge } from "tailwind-merge";
import DoubleCheckMarkIcon from "@ui/icons/double-check-mark.svg?react";

interface SelectContextType {
    value?: string;
}

const SelectContext = createContext<SelectContextType | undefined>(undefined);

// Main Select Component
const SelectRoot = ({
    value,
    onValueChange,
    children,
    ...props
}: React.ComponentProps<typeof SelectPrimitive.Root>) => (
    <SelectContext.Provider value={{ value }}>
        <SelectPrimitive.Root value={value} onValueChange={onValueChange} {...props}>
            {children}
        </SelectPrimitive.Root>
    </SelectContext.Provider>
);

interface SelectTriggerProps extends React.ComponentProps<typeof SelectPrimitive.Trigger> {
    placeholder?: string;
}

const SelectTrigger = ({ children, placeholder, className, ...props }: SelectTriggerProps) => {
    return (
        <SelectPrimitive.Trigger
            className={twMerge(
                "transition-default flex h-11 w-full items-center justify-between rounded border border-theme-secondary-400 px-4 py-3.5 outline outline-1 outline-transparent",
                "text-theme-secondary-900 dark:border-theme-dark-500 dark:text-theme-dark-200",
                "hover:border-theme-primary-400 hover:outline-theme-primary-400 hover:dark:border-theme-dark-blue-600 hover:dark:outline-theme-dark-blue-600",
                "data-[state=open]:border-theme-primary-400 data-[state=open]:outline-theme-primary-400 data-[state=open]:dark:border-theme-dark-blue-600 data-[state=open]:dark:outline-theme-dark-blue-600",
                className,
            )}
            {...props}
        >
            <SelectPrimitive.Value placeholder={placeholder} />

            <SelectPrimitive.Icon className="transition-default data-[state=open]:rotate-180">
                <ChevronDownIcon className="h-3 w-3 text-theme-secondary-700 dark:text-theme-dark-200" />
            </SelectPrimitive.Icon>
        </SelectPrimitive.Trigger>
    );
};

const SelectContent = ({ children, className, ...props }: React.ComponentProps<typeof SelectPrimitive.Content>) => {
    return (
        <SelectPrimitive.Portal>
            <SelectPrimitive.Content
                className={twMerge(
                    "z-50 overflow-hidden rounded-xl border border-white bg-white shadow-lg dark:border-theme-dark-700 dark:bg-theme-dark-900",
                    className,
                )}
                position="popper"
                sideOffset={8}
                {...props}
            >
                <SelectPrimitive.Viewport className="custom-scroll flex h-full flex-col overflow-y-auto overscroll-contain">
                    {children}
                </SelectPrimitive.Viewport>
            </SelectPrimitive.Content>
        </SelectPrimitive.Portal>
    );
};

interface SelectItemProps extends React.ComponentProps<typeof SelectPrimitive.Item> {
    children: React.ReactNode;
}

const SelectItem = ({ children, className, value, ...props }: SelectItemProps) => {
    return (
        <SelectPrimitive.Item
            value={value}
            className={twMerge(
                "transition-default my-[0.125rem] inline-flex cursor-pointer items-center justify-between rounded-lg px-5 py-[0.875rem] font-semibold leading-5 outline-none",
                "text-theme-secondary-700 dark:text-theme-dark-200",
                "hover:bg-theme-secondary-200 hover:text-theme-secondary-900 hover:dark:bg-theme-dark-950 hover:dark:text-theme-dark-50",
                "data-[state=checked]:bg-theme-secondary-200 data-[state=checked]:text-theme-primary-600 data-[state=checked]:dark:bg-theme-dark-950 data-[state=checked]:dark:text-theme-dark-50",
                className,
            )}
            {...props}
        >
            <SelectPrimitive.ItemText>{children}</SelectPrimitive.ItemText>
            <SelectPrimitive.ItemIndicator>
                <DoubleCheckMarkIcon className="h-4 w-4 text-theme-primary-600 dark:text-theme-dark-50" />
            </SelectPrimitive.ItemIndicator>
        </SelectPrimitive.Item>
    );
};

const SelectSeparator = ({ className, ...props }: React.ComponentProps<typeof SelectPrimitive.Separator>) => {
    return (
        <SelectPrimitive.Separator
            className={twMerge("my-0.5 border-t border-theme-secondary-300 dark:border-theme-dark-500", className)}
            {...props}
        />
    );
};

const Select = Object.assign(SelectRoot, {
    Trigger: SelectTrigger,
    Content: SelectContent,
    Item: SelectItem,
    Separator: SelectSeparator,
});

export default Select;
