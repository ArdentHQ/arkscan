import Info from "@/Components/General/Info";
import classNames from "@/utils/class-names";
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

            {auxiliaryTitle !== "" && <span className="ml-1 text-theme-secondary-400">{auxiliaryTitle}</span>}

            {required && <div className="mb-3 ml-px h-1 w-1 rounded-full bg-theme-danger-400 p-px"></div>}

            {tooltip && <Info tooltip={tooltip} className={tooltipClass} type={tooltipType} />}
        </label>
    );
}
