import Tippy, { TippyProps } from "@tippyjs/react";
import React from "react";

export default function Tooltip({ children, ...props }: TippyProps & React.PropsWithChildren) {
    return (
        <Tippy theme="ark" {...props}>
            <div>{children}</div>
        </Tippy>
    );
}
