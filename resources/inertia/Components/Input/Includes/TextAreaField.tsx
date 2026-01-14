import classNames from "classnames";
import { InputHTMLAttributes } from "react";

export default function TextAreaField({
    ref,
    id,
    name,
    inputClass = "",
    inputTypeClass = "input-text",
    errorClass = "input-text--error",
    error,

    ...props
}: InputHTMLAttributes<HTMLTextAreaElement> & {
    ref: React.RefObject<HTMLTextAreaElement | null>;
    inputClass?: string;
    inputTypeClass?: string;
    errorClass?: string;
    error?: string;
}) {
    return (
        <textarea
            ref={ref}
            className={classNames({
                [inputClass]: !!inputClass,
                [inputTypeClass]: !!inputTypeClass,
                [errorClass]: !!error,
            })}
            name={name}
            id={id ?? name}
            {...props}
        />
    );
}
