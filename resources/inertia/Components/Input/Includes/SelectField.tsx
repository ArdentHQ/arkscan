import classNames from "classnames";
import { InputHTMLAttributes } from "react";

export default function SelectField({
    ref,
    id,
    name,
    inputClass = "",
    inputTypeClass = "form-select block w-full pl-4 pr-8 py-3 h-14",
    errorClass = "form-select--error",
    error,

    ...props
}: InputHTMLAttributes<HTMLSelectElement> & {
    ref: React.RefObject<HTMLSelectElement | null>;
    inputClass?: string;
    inputTypeClass?: string;
    errorClass?: string;
    error?: string;
}) {
    return (
        <select
            ref={ref}
            className={classNames({
                [inputClass]: !!inputClass,
                [inputTypeClass]: !!inputTypeClass,
                [errorClass]: !!error,
            })}
            name={name}
            id={id ?? name}
            {...props}
        >
            {props.children}
        </select>
    );
}
