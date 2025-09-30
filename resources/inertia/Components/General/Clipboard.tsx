import { useEffect, useState } from "react"
import '@ui/js/clipboard';
import classNames from "@/utils/class-names";
import DoubleCheckMark from "@/Assets/Icons/DoubleCheckMark";
import Copy from "@/Assets/Icons/Copy";
import Tippy from "@tippyjs/react";

export default function Clipboard({
    value,
    className = 'h-10 w-12',
    noStyling = false,
    tooltipContent,
    wrapperClass = '',
    withCheckmarks = false,
    checkmarksClass = '',
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
    }

    if (!clipboardInstance) {
        return null;
    }

    return (
        <div className={wrapperClass}>
            <Tippy
                content={tooltipContent || ''}
                visible={showTooltip}
            >
                <button
                    type="button"
                    className={classNames({
                        'clipboard relative': true,
                        'button-icon': ! noStyling,
                        [className]: !! className,
                    })}
                    onClick={copyToClipboard}
                >
                    {withCheckmarks && (
                        <>
                            <div
                                className={classNames({
                                    "flex items-center transition-default": true,
                                    'opacity-0': clipboardInstance.showCheckmarks,
                                })}
                            >
                                <Copy className="h-4 w-4" />

                                {children}
                            </div>

                            {clipboardInstance.showCheckmarks && (
                                <div
                                    className={classNames({
                                        'absolute m-auto': true,
                                        [checkmarksClass]: !! checkmarksClass,
                                    })}
                                >
                                    <DoubleCheckMark className="h-4 w-4" />
                                </div>
                            )}
                        </>
                    )}

                    {! withCheckmarks &&
                        <>
                            <Copy className="h-4 w-4" />

                            {children}
                        </>
                    }
                </button>
            </Tippy>
        </div>
    );
}
