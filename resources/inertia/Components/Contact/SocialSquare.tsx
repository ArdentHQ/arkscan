// import { SVGIconComponent } from "@/Pages/Support.contracts";
import classNames from "classnames";
import React from "react";

type SVGIconComponent = React.FunctionComponent<React.SVGProps<SVGSVGElement>>;

export interface InformationCardNetwork {
    url: string;
    icon: SVGIconComponent;
}

export default function SocialSquare({
    internal = false,
    url,
    icon,
    className = "w-16 border h-14 border-theme-secondary-300 lg:w-14 lg:h-12 dark:border-theme-secondary-800",
    hoverClass = "hover:bg-theme-primary-700 hover:text-white",
}: {
    internal?: boolean;
    url: string;
    icon: SVGIconComponent;
    className?: string;
    hoverClass?: string;
}) {
    return (
        <a
            href={url}
            target={internal ? undefined : "_blank"}
            rel={internal ? undefined : "noopener noreferrer"}
            className={classNames(["transition-default block cursor-pointer rounded-xl", className, hoverClass])}
        >
            <div className="flex h-full items-center justify-center">
                {React.createElement(icon, { className: "w-4 h-4" })}
            </div>
        </a>
    );
}
