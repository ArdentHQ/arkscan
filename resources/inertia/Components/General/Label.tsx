import { twMerge } from "tailwind-merge";

function Label({ children, className, ...props }: React.HTMLAttributes<HTMLLabelElement>) {
    return (
        <label
            className={twMerge(
                "transition-default text-theme-secondary-900 dark:text-theme-dark-50 block pb-3 text-lg font-semibold",
                className,
            )}
            {...props}
        >
            {children}
        </label>
    );
}

export default Label;
