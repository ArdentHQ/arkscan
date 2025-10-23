import { useEffect, useState } from "react";
import "@ui/js/clipboard";
import classNames from "@/utils/class-names";
import CopyIcon from "@ui/icons/copy.svg?react";
import DoubleCheckMarkIcon from "@ui/icons/double-check-mark.svg?react";
import Tooltip from "./Tooltip";

export default function Clipboard({
    value,
    className = "h-10 w-12",
    noStyling = false,
    tooltipContent,
    wrapperClass = "",
    withCheckmarks = false,
    checkmarksClass = "",
    children,
}: {
    value?: string;
    className?: string;
    copyInput?: boolean;
    noStyling?: boolean;
    tooltipContent?: string;
    wrapperClass?: string;
    withCheckmarks?: boolean;
    checkmarksClass: string;
    children?: React.ReactNode;
}) {
    const [clipboardInstance, setClipboardInstance] = useState<any>();
    const [showTooltip, setShowTooltip] = useState(false);

    useEffect(() => {
        setClipboardInstance((window as any).clipboard());
    }, []);

    const copyToClipboard = () => {
        setShowTooltip(true);

        setTimeout(() => {
            setShowTooltip(false);
        }, 3000);

        clipboardInstance.copy(value);
    };

    if (!clipboardInstance) {
        return null;
    }

    return (
        <div className={wrapperClass}>
            <Tooltip content={tooltipContent || ""} visible={showTooltip}>
                <button
                    type="button"
                    className={classNames({
                        "clipboard relative": true,
                        "button-icon": !noStyling,
                        [className]: !!className,
                    })}
                    onClick={copyToClipboard}
                >
                    {withCheckmarks && (
                        <>
                            <div
                                className={classNames({
                                    "transition-default flex items-center": true,
                                    "opacity-0": clipboardInstance.showCheckmarks,
                                })}
                            >
                                <CopyIcon className="h-4 w-4" />

                                {children}
                            </div>

                            {clipboardInstance.showCheckmarks && (
                                <div
                                    className={classNames({
                                        "absolute m-auto": true,
                                        [checkmarksClass]: !!checkmarksClass,
                                    })}
                                >
                                    <DoubleCheckMarkIcon name="double-check-mark" className="h-4 w-4" />
                                </div>
                            )}
                        </>
                    )}

                    {!withCheckmarks && (
                        <>
                            <CopyIcon className="h-4 w-4" />

                            {children}
                        </>
                    )}
                </button>
            </Tooltip>
        </div>
    );
}
