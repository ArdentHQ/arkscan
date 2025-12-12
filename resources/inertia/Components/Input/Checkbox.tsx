import classNames from "classnames";
import { useTranslation } from "react-i18next";

export default function Checkbox({
    name,
    id,
    onChange,
    className = "mt-4",
    verticalPosition = "middle",
    label,
    labelClasses,
    value,
    checked = false,
    disabled = false,
    right = false,
}: {
    name: string;
    id: string;
    onChange: (checked: boolean) => void;
    className?: string;
    verticalPosition?: "top" | "middle" | "bottom";
    label?: string | React.ReactNode;
    labelClasses?: string;
    value?: string | number;
    checked?: boolean;
    disabled?: boolean;
    right?: boolean;
}) {
    const { t } = useTranslation();

    let verticalPositionClass = "items-center";
    if (verticalPosition === "middle") {
        verticalPositionClass = "items-center";
    }

    if (verticalPosition === "top") {
        verticalPositionClass = "items-start";
    }

    if (verticalPosition === "bottom") {
        verticalPositionClass = "items-end";
    }

    return (
        <div className={className}>
            <div
                className={classNames({
                    "relative flex": true,
                    "flex-row-reverse": right,
                    [verticalPositionClass]: true,
                })}
            >
                <div className="absolute flex h-5 items-center">
                    <input
                        id={id}
                        name={name}
                        type="checkbox"
                        className="form-checkbox input-checkbox focus-visible:ring-2 focus-visible:ring-theme-primary-500"
                        value={value}
                        checked={checked}
                        disabled={disabled}
                        onChange={(e) => onChange(e.currentTarget.checked)}
                    />
                </div>

                <div
                    className={classNames({
                        "text-sm leading-5 text-theme-secondary-700 dark:text-theme-secondary-500": true,
                        "pr-7": right,
                        "pl-7": !right,
                    })}
                >
                    <label htmlFor={id} className={labelClasses}>
                        {label ? label : t(`forms.${name}`)}
                    </label>
                </div>
            </div>
        </div>
    );
}
