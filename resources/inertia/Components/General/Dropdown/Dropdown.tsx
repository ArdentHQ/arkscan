import EllipsisVerticalIcon from "@ui/icons/ellipsis-vertical.svg?react";
import classNames from "@/utils/class-names";
import {
    Placement,
    useFloating,
    autoUpdate,
    offset,
    useTransitionStyles,
    useInteractions,
    useDismiss,
    shift,
    flip,
} from "@floating-ui/react";
import { useDropdown } from "@/Providers/Dropdown/DropdownContext";
import Tooltip from "../Tooltip";

export default function Dropdown({
    dropdownContentClasses = "shadow-lg bg-white dark:bg-theme-dark-900 border border-white dark:border-theme-dark-700 px-1 rounded-xl",
    buttonClassExpanded = "text-theme-primary-500",
    buttonClassClosed = "",
    buttonClass = "bg-white rounded border border-theme-secondary-300 dark:bg-theme-dark-900 dark:border-theme-dark-700",
    useDefaultButtonClasses = true,
    dropdownClasses = "w-40",
    popupStyles = {},
    zIndex = 10,
    wrapperClass = "",
    fullScreen = false,
    buttonTooltip,
    closeOnClick = true,
    disabled = false,
    placement = "bottom",
    button,
    children,
    onClosed,
    onOpened,
    testId,
}: {
    dropdownContentClasses?: string;
    buttonClassExpanded?: string;
    buttonClassClosed?: string;
    buttonClass?: string;
    dropdownClasses?: string;
    useDefaultButtonClasses?: boolean;
    popupStyles?: React.CSSProperties;
    zIndex?: number;
    wrapperClass?: string;
    fullScreen?: boolean;
    buttonTooltip?: string;
    closeOnClick?: boolean;
    disabled?: boolean;
    placement?: Placement;
    button?: React.ReactNode;
    children: React.ReactNode;
    onClosed?: () => void;
    onOpened?: () => void;
    testId?: string;
}) {
    const { isOpen, setIsOpen } = useDropdown();

    const flipMiddleware = flip({
        // Ensure we flip to the perpendicular axis if it doesn't fit
        // on narrow viewports.
        crossAxis: "alignment",
        fallbackAxisSideDirection: "end", // or 'start'
        padding: 8,
    });
    const shiftMiddleware = shift();

    const middleware = [offset(8)];
    if (placement.includes("-")) {
        middleware.push(flipMiddleware, shiftMiddleware);
    } else {
        middleware.push(shiftMiddleware, flipMiddleware);
    }

    const { context, refs, floatingStyles } = useFloating({
        open: isOpen,
        placement,
        whileElementsMounted: autoUpdate,
        middleware,
        onOpenChange(nextOpen) {
            setIsOpen(nextOpen);

            if (nextOpen && onOpened !== undefined) {
                setTimeout(() => onOpened(), 250);
            }

            if (!nextOpen && onClosed !== undefined) {
                setTimeout(() => onClosed(), 250);
            }
        },
    });

    const triggerOpenClosed = (isDropdownOpen: boolean) => {
        setIsOpen(isDropdownOpen);
        if (isDropdownOpen && onOpened !== undefined) {
            onOpened();
        }

        return;
    };

    const dismiss = useDismiss(context);

    const { getReferenceProps, getFloatingProps } = useInteractions([dismiss]);

    const { isMounted: isFloatingOpen, styles: floatingTransitionStyled } = useTransitionStyles(context, {
        duration: 250, // explicitly setting default value for reference to open/closed events
    });

    let dropdownButton = (
        <div>
            <button
                ref={refs.setReference}
                className={classNames({
                    "dropdown-button transition-default flex items-center focus:outline-none": true,
                    "bg-theme-secondary-200 text-theme-secondary-500 dark:border-theme-dark-700 dark:bg-theme-dark-800 dark:text-theme-dark-500":
                        disabled,
                    "bg-theme-secondary-200 text-theme-secondary-700 dark:bg-theme-dark-800 dark:text-theme-dark-200 dark:hover:bg-theme-dark-700 md:bg-white md:hover:text-theme-secondary-900 md:dark:bg-theme-dark-900 md:dark:text-theme-dark-600":
                        !disabled && useDefaultButtonClasses,
                    [buttonClass]: true,
                    [buttonClassExpanded]: isOpen,
                    [buttonClassClosed]: !isOpen,
                })}
                onClick={() => triggerOpenClosed(!isOpen)}
                type="button"
                disabled={disabled}
                data-testid={testId ? `${testId}:button` : undefined}
                {...getReferenceProps()}
            >
                {button !== undefined && button}
                {button === undefined && <EllipsisVerticalIcon className="h-5 w-5" />}
            </button>
        </div>
    );

    if (buttonTooltip !== undefined) {
        dropdownButton = <Tooltip content={buttonTooltip}>{dropdownButton}</Tooltip>;
    }

    return (
        <div
            className={classNames({
                [wrapperClass]: true,
            })}
            data-testid={testId}
        >
            {dropdownButton}

            {isFloatingOpen && (
                <div
                    ref={refs.setFloating}
                    style={{
                        ...floatingStyles,
                        ...popupStyles,
                        zIndex: zIndex,
                    }}
                    {...getFloatingProps()}
                    data-testid={testId ? `${testId}:dropdown` : undefined}
                >
                    <div style={floatingTransitionStyled}>
                        <div
                            className={classNames({
                                dropdown: true,
                                "-mx-8 w-screen md:mx-0 md:w-auto": fullScreen,
                                [dropdownClasses]: true,
                            })}
                        >
                            <div
                                className={dropdownContentClasses}
                                onClick={() => {
                                    if (closeOnClick) {
                                        triggerOpenClosed(false);
                                    }
                                }}
                            >
                                {children}
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
