import { createContext, useContext } from "react";
import { twMerge } from "tailwind-merge";

interface CheckboxContextType {
    disabled?: boolean;
}

const CheckboxContext = createContext<CheckboxContextType | undefined>(undefined);

const useCheckboxContext = () => {
    const context = useContext(CheckboxContext);
    if (!context) {
        throw new Error("Checkbox subcomponents must be used within <Checkbox>");
    }
    return context;
};

interface CheckboxRootProps extends React.LabelHTMLAttributes<HTMLLabelElement> {
    disabled?: boolean;
}

const CheckboxRoot = ({ className, disabled, ...props }: CheckboxRootProps) => {
    return (
        <CheckboxContext.Provider value={{ disabled }}>
            <label className={twMerge("relative flex items-center space-x-2", className)} {...props} />
        </CheckboxContext.Provider>
    );
};

const CheckboxInput = ({
    className,
    checked,
    onCheckedChange,
    ...props
}: React.InputHTMLAttributes<HTMLInputElement> & {
    checked?: boolean;
    onCheckedChange?: (checked: boolean) => void;
}) => {
    const { disabled } = useCheckboxContext();

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const newChecked = e.target.checked;
        onCheckedChange?.(newChecked);
    };

    return (
        <input
            type="checkbox"
            checked={checked}
            onChange={handleChange}
            disabled={disabled}
            className={twMerge(
                "form-checkbox input-checkbox cursor-pointer",
                "focus-visible:ring-2 focus-visible:ring-theme-primary-500",
                "!h-5 !w-5",
                "checked:border-theme-primary-600 checked:bg-theme-primary-600",
                className,
            )}
            {...props}
        />
    );
};

const CheckboxLabel = ({ className, ...props }: React.HTMLAttributes<HTMLSpanElement>) => {
    const { disabled } = useCheckboxContext();

    return (
        <span
            className={twMerge(
                "transition-default cursor-pointer text-sm leading-5 text-theme-secondary-700 dark:text-theme-secondary-500",
                disabled && "cursor-not-allowed opacity-50",
                className,
            )}
            {...props}
        />
    );
};

const Checkbox = Object.assign(CheckboxRoot, {
    Input: CheckboxInput,
    Label: CheckboxLabel,
});

export default Checkbox;
