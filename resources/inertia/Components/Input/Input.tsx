import { createRef, InputHTMLAttributes } from "react";
import InputErrorTooltip from "./Includes/InputErrorTooltip";
import InputField from "./Includes/InputField";
import InputLabel from "./Includes/InputLabel";

export default function Input({
    id,
    name,
    label,
    tooltip,
    tooltipClass,
    tooltipType,
    required = false,
    hideLabel = false,
    className = '',
    inputClass = '',
    auxiliaryTitle = '',
    error,

    ...props
}: InputHTMLAttributes<HTMLInputElement> & {
    id?: string;
    name: string;
    label?: string | React.ReactNode;
    tooltip?: string;
    tooltipClass?: string;
    tooltipType?: 'info' | 'question';
    required?: boolean;
    hideLabel?: boolean;
    className?: string;
    inputClass?: string;
    auxiliaryTitle?: string;
    error?: string;
}) {
    const inputRef = createRef<HTMLInputElement>();

    return (
        <div className={className}>
            <div className="input-group">
                {! hideLabel && (
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
                    <InputField
                        ref={inputRef}
                        name={name}
                        error={error}
                        id={id ?? name}
                        inputClass={inputClass}
                        // noModel={noModel ?? false}
                        // model={model ?? name}
                        // deferred={deferred ?? false}
                        // debounce={debounce ?? null}

                        {...props}
                    />

                    {error && (
                        <InputErrorTooltip
                            inputRef={inputRef}
                            error={error}
                        />
                    )}
                </div>
            </div>
        </div>
    );
}
