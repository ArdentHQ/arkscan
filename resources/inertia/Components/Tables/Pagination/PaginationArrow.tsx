import classNames from "@/utils/class-names"
import React from "react";

export default function PaginationArrow({
    icon,
    disabled = false,
    text = null,
    className = '',
    onClick,
}: {
    icon: React.ComponentType<{ className?: string }>;
    disabled?: boolean;
    text?: string | null;
    className?: string;
    onClick: () => void;
}) {
    return (
        <div className={classNames({
            "flex-1 sm:flex-none sm:w-8 md:w-auto": true,
            'cursor-not-allowed': disabled,
            [className]: true,
        })}>
            <button
                type="button"
                className={classNames({
                    'items-center button-secondary flex justify-center h-8 p-0 w-full focus:ring-theme-primary-500 focus:dark:ring-theme-dark-blue-300': true,
                    'sm:w-8': ! text,
                    'w-8 md:w-auto md:px-4': !! text,
                })}
                onClick={onClick}
                disabled={disabled}
            >
                {React.createElement(icon, { className: classNames({
                    'w-3 h-3': true,
                    'md:hidden': !! text,
                }) })}

                {text && (
                    <span className="hidden md:inline">
                        {text}
                    </span>
                )}
            </button>
        </div>
    );
}
