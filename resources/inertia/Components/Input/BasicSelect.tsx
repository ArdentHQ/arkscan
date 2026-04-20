import { createRef, InputHTMLAttributes } from "react";
import InputErrorTooltip from "./Includes/InputErrorTooltip";
import InputLabel from "./Includes/InputLabel";
import SelectField from "./Includes/SelectField";

export default function BasicSelect({
    id,
    name,
    label,
    tooltip,
    tooltipClass,
    tooltipType,
    required = false,
    hideLabel = false,
    className = "",
    inputClass = "",
    auxiliaryTitle = "",
    error,
    testId,

    ...props
}: InputHTMLAttributes<HTMLSelectElement> & {
    id?: string;
    name: string;
    label?: string | React.ReactNode;
    tooltip?: string;
    tooltipClass?: string;
    tooltipType?: "info" | "question";
    required?: boolean;
    hideLabel?: boolean;
    className?: string;
    inputClass?: string;
    auxiliaryTitle?: string;
    error?: string;
    testId?: string;
}) {
    const selectRef = createRef<HTMLSelectElement>();

    return (
        <div className={className} data-testid={testId}>
            <div className="input-group">
                {!hideLabel && (
                    <InputLabel
                        name={name}
                        error={error}
                        id={id ?? name}
                        label={label}
                        tooltip={tooltip}
                        tooltipClass={tooltipClass}
                        tooltipType={tooltipType}
                        required={required}
                        auxiliaryTitle={auxiliaryTitle}
                    />
                )}

                <div className="input-wrapper">
                    <SelectField
                        ref={selectRef}
                        name={name}
                        error={error}
                        id={id ?? name}
                        inputClass={inputClass}
                        {...props}
                    />

                    {error && <InputErrorTooltip inputRef={selectRef} error={error} />}
                </div>
            </div>
        </div>
    );
}
