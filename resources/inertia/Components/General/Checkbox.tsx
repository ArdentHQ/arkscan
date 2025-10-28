import { twMerge } from "tailwind-merge";

const CheckboxRoot = ({ className, ...props }: React.LabelHTMLAttributes<HTMLLabelElement>) => {
    return <label className={twMerge("relative flex items-center space-x-2", className)} {...props} />;
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
    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const newChecked = e.target.checked;
        onCheckedChange?.(newChecked);
    };

    return (
        <input
            type="checkbox"
            checked={checked}
            onChange={handleChange}
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
    return (
        <span
            className={twMerge(
                "transition-default cursor-pointer text-sm leading-5 text-theme-secondary-700 dark:text-theme-secondary-500",
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
