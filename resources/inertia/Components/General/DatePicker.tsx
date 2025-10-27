import { useEffect, useRef } from "react";
import Pikaday, { PikadayOptions } from "pikaday";
import CalendarIcon from "@ui/icons/calendar-without-dots.svg?react";
import { twMerge } from "tailwind-merge";

interface DatePickerProps extends PikadayOptions {
    value?: Date | null;
    onChange?: (date: Date | null) => void;
    minDate?: Date;
    maxDate?: Date;
    className?: string;
}

export default function DatePicker({ value, onChange, minDate, maxDate, className, ...options }: DatePickerProps) {
    const inputRef = useRef<HTMLInputElement>(null);
    const pickerRef = useRef<Pikaday | null>(null);

    useEffect(() => {
        if (!inputRef.current) return;

        pickerRef.current = new Pikaday({
            field: inputRef.current,
            format: "DD/MM/YYYY",
            minDate: minDate,
            maxDate: maxDate || new Date(),
            onSelect: function (date: Date) {
                onChange?.(date);
            },
            onClose: function () {
                if (this.getDate() === null) {
                    this.clear();
                    onChange?.(null);
                } else {
                    onChange?.(this.getDate());
                }
            },
            toString: (date: Date) => {
                return date.toLocaleDateString(navigator.language, {
                    year: "numeric",
                    month: "2-digit",
                    day: "2-digit",
                });
            },
            ...options,
        });

        return () => {
            pickerRef.current?.destroy();
        };
    }, [minDate, maxDate]);

    return (
        <div
            className={twMerge(
                "flex flex-1 items-center justify-between rounded border border-theme-secondary-400 bg-white dark:border-theme-dark-500 dark:bg-theme-dark-900",
                className,
            )}
        >
            <input
                ref={inputRef}
                type="text"
                placeholder="DD/MM/YYYY"
                className="w-full rounded py-3 pl-4 dark:bg-theme-dark-900 dark:text-theme-dark-50 placeholder:dark:text-theme-dark-200"
                onKeyDown={(e) => {
                    if (e.key === "Backspace") {
                        e.stopPropagation();
                    }
                }}
            />

            <div
                className="flex h-full cursor-pointer items-center pl-2 pr-4"
                onClick={() => inputRef.current?.click()}
            >
                <CalendarIcon className="text-theme-primary-600 dark:text-theme-dark-300" />
            </div>
        </div>
    );
}
