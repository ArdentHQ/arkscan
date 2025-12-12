import classNames from "classnames";
import { InputHTMLAttributes } from "react";

export default function InputField({
    ref,
    id,
    name,
    inputClass = "",
    inputTypeClass = "input-text",
    errorClass = "input-text--error",
    error,

    ...props
}: InputHTMLAttributes<HTMLInputElement> & {
    ref: React.RefObject<HTMLInputElement | null>;
    inputClass?: string;
    inputTypeClass?: string;
    errorClass?: string;
    error?: string;
}) {
    return (
        <input
            ref={ref}
            className={classNames({
                [inputClass]: !!inputClass,
                [inputTypeClass]: !!inputTypeClass,
                [errorClass]: !!error,
            })}
            autoCapitalize="none"
            id={id ?? name}
            {...props}

            // @unless ($noModel)
            // @if ($deferred)
            // wire:model="{{ $model ?? $name }}"
            // @elseif ($debounce === true)
            // wire:model.live.debounce="{{ $model ?? $name }}"
            // @elseif (is_string($debounce))
            // wire:model.live.debounce.{{ $debounce }}="{{ $model ?? $name }}"
            // @else
            // wire:model.live="{{ $model ?? $name }}"
            // @endif
            // @endUnless
        />
    );
}
