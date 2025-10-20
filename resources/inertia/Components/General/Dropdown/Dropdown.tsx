import EllipsisVerticalIcon from "@ui/icons/ellipsis-vertical.svg?react";
import classNames from "@/utils/class-names";
import { Placement, useFloating, autoUpdate, offset, useTransitionStyles, useInteractions, useDismiss, shift, flip } from '@floating-ui/react';
import { useDropdown } from "@/Providers/Dropdown/DropdownContext";
import Tooltip from "../Tooltip";

export default function Dropdown({
    dropdownContentClasses = 'bg-white dark:bg-theme-dark-900 border border-white dark:border-theme-dark-700 px-1 rounded-xl',
    buttonClassExpanded = 'text-theme-primary-500',
    buttonClassClosed = '',
    buttonClass = 'bg-white rounded border border-theme-secondary-300 dark:bg-theme-dark-900 dark:border-theme-dark-700',
    dropdownClasses = 'w-40',
    popupStyles = {},
    zIndex = 'z-10',
    wrapperClass = '',
    fullScreen = false,
    buttonTooltip,
    closeOnClick = true,
    disabled = false,
    placement = 'bottom',
    button,
    children,
    onClosed,
}: {
    dropdownContentClasses?: string;
    buttonClassExpanded?: string;
    buttonClassClosed?: string;
    buttonClass?: string;
    dropdownClasses?: string;
    popupStyles?: React.CSSProperties;
    zIndex?: string;
    wrapperClass?: string;
    fullScreen?: boolean;
    dusk?: boolean;
    buttonTooltip?: string;
    closeOnClick?: boolean;
    disabled?: boolean;
    placement?: Placement;
    button?: React.ReactNode;
    children: React.ReactNode;
    onClosed?: () => void;
}) {
    const { isOpen, setIsOpen } = useDropdown();

    const flipMiddleware = flip({
        // Ensure we flip to the perpendicular axis if it doesn't fit
        // on narrow viewports.
        crossAxis: 'alignment',
        fallbackAxisSideDirection: 'end', // or 'start'
        padding: 8,
    });
    const shiftMiddleware = shift();

    const middleware = [offset(8)];
    if (placement.includes('-')) {
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

            if (! nextOpen && onClosed !== undefined) {
                setTimeout(() => onClosed(), 250);
            }
        },
    });

    const dismiss = useDismiss(context);

    const {getReferenceProps, getFloatingProps} = useInteractions([
        dismiss,
    ]);

    const {isMounted: isFloatingOpen, styles: floatingTransitionStyled} = useTransitionStyles(context, {
        duration: 250, // explicitly setting default value for reference to onClosed
    });

    let dropdownButton = (
        <div>
            <button
                ref={refs.setReference}
                className={classNames({
                    "flex items-center focus:outline-none dropdown-button transition-default": true,
                    'text-theme-secondary-500 bg-theme-secondary-200 dark:text-theme-dark-500 dark:bg-theme-dark-800 dark:border-theme-dark-700': disabled,
                    'bg-theme-secondary-200 dark:bg-theme-dark-800 md:bg-white md:dark:text-theme-dark-600 md:hover:text-theme-secondary-900 md:dark:bg-theme-dark-900 text-theme-secondary-700 dark:text-theme-dark-200 md:hover:text-theme-secondary-900 dark:hover:bg-theme-dark-700': ! disabled,
                    [buttonClass]: true,
                    [buttonClassExpanded]: isOpen,
                    [buttonClassClosed]: ! isOpen,
                })}
                onClick={() => setIsOpen(!isOpen)}
                type="button"
                disabled={disabled}
                {...getReferenceProps()}
            >
                {button !== undefined && button}
                {button === undefined && (
                    <EllipsisVerticalIcon className="w-5 h-5" />
                )}
            </button>
        </div>
    );

    if (buttonTooltip !== undefined) {
        dropdownButton = (
            <Tooltip content={buttonTooltip}>
                {dropdownButton}
            </Tooltip>
        );
    }

    return (
        (
            <div className={classNames({
                [wrapperClass]: true,
                [zIndex]: true,
            })}>
                {dropdownButton}

                {isFloatingOpen && (
                    <div
                        ref={refs.setFloating}
                        style={{
                            ...floatingStyles,
                            ...popupStyles,
                        }}
                        {...getFloatingProps()}
                    >
                        <div style={floatingTransitionStyled}>
                            <div className={classNames({
                                'dropdown': true,
                                'w-screen -mx-8 md:w-auto md:mx-0': fullScreen,
                                [dropdownClasses]: true,
                            })}>
                                <div
                                    className={dropdownContentClasses}
                                    onClick={() => {
                                        if (closeOnClick) {
                                            setIsOpen(false);
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
        )
    );
}
