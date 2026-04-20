import Tooltip from "@/Components/General/Tooltip";
import classNames from "classnames";
import CircleExclamationMarkIcon from "@ui/icons/circle/exclamation-mark.svg?react";

export default function InputErrorTooltip({
    inputRef,
    error,
    shifted = false,
}: {
    inputRef: React.RefObject<HTMLElement | null>;
    error: string;
    shifted?: boolean;
}) {
    return (
        <button
            type="button"
            className={classNames({
                "input-icon px-4 focus-visible:rounded": true,
                "right-13": shifted,
                "right-0": !shifted,
            })}
            onClick={() => {
                inputRef?.current?.focus();
            }}
        >
            <Tooltip content={error}>
                <>
                    <CircleExclamationMarkIcon className="h-5 w-5 text-theme-danger-500" />

                    {shifted && (
                        <div className="h-5 w-px translate-x-4 transform bg-theme-secondary-300 dark:bg-theme-secondary-800">
                            &nbsp;
                        </div>
                    )}
                </>
            </Tooltip>
        </button>
    );
}
