import Info from "@/Components/General/Info";
import classNames from "classnames";
import { useTranslation } from "react-i18next";

export default function InputLabel({
    name,
    error,
    id,
    label,
    tooltip,
    tooltipClass,
    tooltipType,
    required = false,
    auxiliaryTitle = "",
}: {
    name: string;
    error?: string;
    id: string;
    label?: string | React.ReactNode;
    tooltip?: string;
    tooltipClass?: string;
    tooltipType?: "info" | "question";
    required: boolean;
    auxiliaryTitle?: string;
}) {
    const { t } = useTranslation();

    return (
        <label
            htmlFor={id ?? name}
            className={classNames({
                "input-label items-center": true,
                "input-label--error": !!error,
            })}
        >
            {label ? label : t(`forms.${name}`)}

            {auxiliaryTitle !== "" && <span className="text-theme-secondary-400 ml-1">{auxiliaryTitle}</span>}

            {required && <div className="bg-theme-danger-400 mb-3 ml-px h-1 w-1 rounded-full p-px"></div>}

            {tooltip && <Info tooltip={tooltip} className={tooltipClass} type={tooltipType} />}
        </label>
    );
}
