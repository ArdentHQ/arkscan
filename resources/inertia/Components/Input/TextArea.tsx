import { createRef, TextareaHTMLAttributes } from "react";
import InputErrorTooltip from "./Includes/InputErrorTooltip";
import InputField from "./Includes/InputField";
import InputLabel from "./Includes/InputLabel";
import TextAreaField from "./Includes/TextAreaField";

export default function TextArea({
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
}: TextareaHTMLAttributes<HTMLTextAreaElement> & {
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
    const inputRef = createRef<HTMLTextAreaElement>();

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
                    <TextAreaField
                        ref={inputRef}
                        name={name}
                        error={error}
                        id={id ?? name}
                        inputClass={inputClass}
                        {...props}
                    />
                </div>

                {error && <p className="input-help--error">The message field is required.</p>}
            </div>
        </div>
    );
}
