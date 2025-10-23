import Tooltip from "@/Components/General/Tooltip";
import classNames from "@/utils/class-names"

export default function InputErrorTooltip({
    inputRef,
    error,
    shifted = false,
}: {
    inputRef: React.RefObject<HTMLInputElement | null>;
    error: string;
    shifted?: boolean;
}) {
    return (
        <Tooltip content={error}>
            <button
                type="button"
                className={classNames({
                    'px-4 input-icon focus-visible:rounded': true,
                    'right-13': shifted,
                    'right-0': ! shifted,
                })}
                onClick={() => {
                    inputRef?.current?.focus();
                }}
            >
                {/* <x-ark-icon name="circle.exclamation-mark" class="text-theme-danger-500" /> */}

                {shifted && <div className="w-px h-5 transform translate-x-4 bg-theme-secondary-300 dark:bg-theme-secondary-800">&nbsp;</div>}
            </button>
        </Tooltip>
    );
}
